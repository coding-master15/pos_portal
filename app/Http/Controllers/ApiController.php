<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;

class ApiController extends Controller
{
    public function getUsers($request) {
        $admin = $request->input('admin');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return User::where('admin_id', $admin)->where('type', $request->input('type') ?? 'client')->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
    }

    public function getProducts($request) {
        $admin = $request->input('admin');
        $orderBy = $request->input('order_by') ?? 'DESC';
        return Product::where('admin_id', $admin)->orderBy('id', $orderBy)->paginate($request->input('per_page') ?? 10);
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
}
