<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;

class ApiController extends Controller
{
    public function getUsers($request) {
        $admin = $request->input('admin');
        $search = $request->input('search');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return User::where('admin_id', $admin)->where('name', 'LIKE', "%$search%")->where('type', $request->input('type') ?? 'client')->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
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

        foreach($amount as $key => $value) {
            $price = $value;
        }
        $value->price = $price;
        }
            return $data;
    }

    public function getTotalStock($request) {
        $admin = $request->input('admin');
    
        $amount = TransactionItem::groupBy('admin_id')
        ->selectRaw('sum(quantity) as sum, admin_id')
        ->where('admin_id', $admin)
        //->where('price', '<=', '0')
        ->pluck('sum','admin_id');
        return $amount == [] ? [] : $amount[$admin];
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
        $total = $request->input('total');
        $balance = $request->input('balance');
        $products = json_decode($request->input('products'));
        
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
                'quantity' => $product->quantity,
                'name' => $product->name,
                'price' => $type == 'sell' ? $product->selling_price : $product->purchase_price,
                'image' => $product->image
            ]);
            $prod = Product::find($product->id);
            Product::whereId($id)->update([
                'quantity' => $product->quantity + $prod->quantity
            ]);
        }

        return [
            'transaction' => $transaction,
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
}
