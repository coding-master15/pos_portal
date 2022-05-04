<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\CashRegister;
use App\Models\Setting;


class ApiController extends Controller
{

    function __construct($request) {
        $header = $request->bearerToken();

        if($header != '9fb65ce4-3908-475e-8e67-4893e5b5cd9d') {
            throw new \Exception('INVALID API KEY');
        }
    }

    public function getUsers($request) {
        $admin = $request->input('admin');
        $search = $request->input('search');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return User::where('admin_id', $admin)->where('name', 'LIKE', "%$search%")->where('type', $request->input('type') ?? 'client')->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
    }

    public function getSettings($request) {
        return Setting::find(1);
    }

    public function getProducts($request) {
        $admin = $request->input('admin');
        $search = $request->input('search');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return Product::where('admin_id', $admin)->where('name', 'LIKE', "%$search%")->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
    }

    public function getTransactions($request) {
        $admin = $request->input('admin');
        $customer = $request->input('user_id');
        $orderBy = $request->input('order_by') ?? 'DESC';
        if($customer) {
            return Transaction::where('admin_id', $admin)->where('user_id', $customer)->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
        } else {
            return Transaction::where('admin_id', $admin)->where('type', $request->input('type') ?? 'sell')->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
        }
    }
    public function getStock($request) {
        $search = $request->input('search');
        $admin = $request->input('admin');
        $orderBy = $request->input('order_by') ?? 'DESC';
        $data = Product::where('admin_id', $admin)->where('name', 'LIKE', "%$search%")->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
        
        foreach($data as $key => $value) {
            $amount = TransactionItem::groupBy('product_id')
        ->selectRaw('sum(quantity * price) as sum, product_id')
        ->where('id', $value->id)
        //->where('price', '<=', '0')
        ->limit(1)
        ->offset(0)
        ->pluck('sum','product_id');
        $price = 0;

        foreach($amount as $key => $valuea) {
            $price = intval($valuea);
            
        }
        $value->price = $price;
        }
            return $data;
    }

    public function getTotalStock($request) {
        $admin = $request->input('admin');
    
        $amount = TransactionItem::selectRaw('sum(quantity) as sum')
        ->where('admin_id', $admin)
        ->where('price', '<=', '0')
        ->get('sum');
        $amount2 = TransactionItem::selectRaw('sum(quantity) as sum')
        ->where('admin_id', $admin)
        ->where('price', '>', '0')
        ->get('sum');
        return $amount[0]['sum'] - $amount2[0]['sum'];
    }

    public function login($request) {
        $email = $request->input('email');
        $password = md5($request->input('password'));
        $user = Admin::where('email', $email)->first();
        if($user) {
            if($user->password == $password) {
                return [
                    'user' => $user
                ];
            } else {
                return [
                    'user' => null,
                    'message' => 'Password is incorrect'
                ];
            }
        } else {
            return [
                'user' => null,
                'message' => 'Email is incorrect'
            ];
        }
    }

    public function register($request) {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $shopName = $request->input('shop_name');
        $image = $request->input('image');
        $password = md5($request->input('password'));

        $user = Admin::where('email', $email)->first();

        if($user) {
           return [
               'message' => 'Email already taken'
           ]; 
        }
        
        Admin::create([
            'name' => $name,
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'shop_name' => $shopName,
            'image' => $image,
            'password' => $password
        ]);

        return [
            'user' => Admin::where('email', $email)->where('password', $password)->first(),
        ];
    }
    public function createProduct($request) {
        $admin = $request->input('admin_id');
        $name = $request->input('name');
        $description = $request->input('description');
        $image = $request->input('image');
        $sellingPrice = $request->input('selling_price');
        $purchasePrice = $request->input('purchase_price');
        $sku = $request->input('sku');

        $delimiter = '-';

        $sku = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $sku))))), $delimiter));

        $product = Product::where('sku', $sku)->first();

        if($product) {
           return [
               'message' => 'SKU is already in use'
           ]; 
        }
        
        Product::create([
            'admin_id' => $admin,
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'sku' => $sku,
            'selling_price' => $sellingPrice,
            'purchase_price' => $purchasePrice
        ]);

        return [
            'product' => Product::where('sku', $sku)->first(),
        ];
    }
    public function createTransaction($request) {
        $admin = intval($request->input('admin_id'));
        $customerId = intval($request->input('user_id'));
        $type = $request->input('type');
        $total = floatval($request->input('total'));
        $balance = floatval($request->input('balance'));
        $products = json_decode($request->input('products'));

        $cus = User::find($customerId);

        CashRegister::create([
            'admin_id' => $admin,
            'note' => '',
            'name' => $cus->name,
            'user_id' => $customerId,
            'type' => $type == 'sell' ? 'normal_sell' : ($type == 'purchase' ? 'normal_purchase' : ''),
            'amount' => $type == 'sell' ? ($total-$balance) : (-1)*($total-$balance),
            'payment_date' => date("Y-m-d H:i:s")
        ]);
        
        $transaction = Transaction::create([
            'admin_id' => $admin,
            'total' => $total,
            'balance' => $balance,
            'user_id' => $customerId,
            'type' => $type
        ]);

        foreach($products as $product) {
            TransactionItem::create([
                'admin_id' => $admin,
                'product_id' => $product->id,
                'transaction_id' => $transaction->id,
                'quantity' => intval($product->stock),
                'name' => $product->name,
                'price' => $type == 'sell' ? $product->selling_price : $product->purchase_price,
                'image' => $product->image
            ]);
            $prod = Product::find($product->id);
            Product::whereId($product->id)->update([
                'stock' => $type == 'sell' ? $prod->stock - intval($product->stock) : $prod->stock + intval($product->stock) 
            ]);
        }

        return [
            'transaction' => $transaction,
        ];
    }
    public function createCashRegister($request) {
        $admin = intval($request->input('admin_id'));
        $customerId = intval($request->input('user_id'));
        $type = $request->input('type');
        $note = $request->input('note');
        $name = $request->input('name');
        $amount = $request->input('amount');
        $paymentDate = $request->input('payment_date');
        
        $cashRegister = CashRegister::create([
            'admin_id' => $admin,
            'note' => $note,
            'name' => $name,
            'user_id' => $customerId,
            'type' => $type,
            'amount' => $amount,
            'payment_date' => $paymentDate
        ]);

        return [
            'cash_register' => $cashRegister,
        ];
    }
    public function createUser($request) {
        $admin = $request->input('admin_id');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $image = $request->input('image');
        $address = $request->input('address');
        $type = $request->input('type') ?? 'client';

        // $product = Product::where('sku', $sku)->first();

        // if($product) {
        //    return [
        //        'message' => 'SKU is already in use'
        //    ]; 
        // }
        
        $user = User::create([
            'admin_id' => $admin,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'type' => $type,
            'address' => $address,
            'image' => $image
        ]);

        return [
            'user' => $user,
        ];
    }
    public function updateProduct($request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $description = $request->input('description');
        $image = $request->input('image');
        $sellingPrice = $request->input('selling_price');
        $purchasePrice = $request->input('purchase_price');
        $sku = $request->input('sku');

        $delimiter = '-';

        $sku = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $sku))))), $delimiter));

        $product = Product::where('sku', $sku)->first();

        if($product && !($product->id == $id)) {
           return [
               'message' => 'SKU is already in use'
           ]; 
        }
        
        Product::whereId($id)->update([
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'sku' => $sku,
            'selling_price' => $sellingPrice,
            'purchase_price' => $purchasePrice
        ]);

        return [
            'product' => Product::where('sku', $sku)->first(),
        ];
    }

    public function getTotals($request) {

        $data = [];

        $admin = $request->input('admin');
    
        $products = Product::
        selectRaw('count(*) as count')
        ->where('admin_id', $admin)
            ->get();

        $clients = User::
            selectRaw('count(*) as count')
            ->where('admin_id', $admin)
            ->where('type', 'client')
                ->get();

        $suppliers = User::
        selectRaw('count(*) as count')
        ->where('admin_id', $admin)
        ->where('type', 'supplier')
            ->get();

        $sells = Transaction::
        selectRaw('count(*) as count')
        ->where('admin_id', $admin)
        ->where('type', 'sell')
            ->get();

        $purchases = Transaction::
        selectRaw('count(*) as count')
        ->where('admin_id', $admin)
        ->where('type', 'purchase')
            ->get();

            $cashhistory = CashRegister::
            selectRaw('count(*) as count')
            ->where('admin_id', $admin)
                ->get();
        $data['products'] = $products[0]['count'];
        $data['sells'] = $sells[0]['count'];
        $data['clients'] = $clients[0]['count'];
        $data['suppliers'] = $suppliers[0]['count'];
        $data['purchases'] = $purchases[0]['count'];
        $data['cash_history'] = $cashhistory[0]['count'];

        return $data;
    }

    public function getCashRegisters($request) {
        $admin = $request->input('admin');
        $search = $request->input('search');
        $type = $request->input('type');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return CashRegister::where('admin_id', $admin)->where('type', 'LIKE', "%$type%")->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 20);
    }

    public function getCashRegisterBalance($request) {
        $admin = $request->input('admin');

        $amount = CashRegister::selectRaw('sum(amount) as sum')
        ->where('admin_id', $admin)
        ->where('amount', '<=', '0')
        ->get('sum');

        $amount2 = CashRegister::selectRaw('sum(amount) as sum')
        ->where('admin_id', $admin)
        ->where('amount', '>', '0')
        ->get('sum');

        return [
            'cash_in' => $amount2[0]['sum'],
            'cash_out' => $amount[0]['sum']
        ];
    }
}
