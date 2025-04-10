<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function cart()
    {
        $cartDetails = Cart::with('product','productCategory', 'productCoupon')->where(['user_id' => Auth::user()->id, 'status' => 1, 'payment_status' => 0])->get()->toArray();

        $grandTotal = DB::table('carts')->select(DB::raw('sum(product_total) as grandTotal'))->where(['user_id' => Auth::user()->id, 'status' => 1, 'payment_status' => 0])->get();

        if(!empty($cartDetails)){
            Session::put(['cartData' => $cartDetails, 'grandTotal' => $grandTotal]);
        }
        return view('front.checkout.cart')->with(compact('cartDetails'));
    }

    public function addToCart(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Check if product already exists in cart

            if (Auth::check()) {
                $userId = Auth::user()->id;
                $countProducts = Cart::where(['product_id' => $data['product_id'], 'user_id' => Auth::user()->id, 'status' => 1, 'payment_status' => 0])->count();

                if ($countProducts > 0) {
                    return redirect()->back()->with('error', 'This product already exists in your cart!!!');
                } else {

                    $productData = Product::with('productCoupon')->where(['id' => $data['product_id'], 'promo_code' => $data['coupon_id']])->first();

                    if($productData['productCoupon']['amount_type'] === "percent"){
                        $discount = $data['product_price'] * ($productData['productCoupon']['amount'] / 100);
                    }else{
                        $discount = $data['product_price'] - $productData['productCoupon']['amount'];
                    }

                    $shipping_charge = 30;

                    $ProductTotal = $data['product_price'] - $discount + $shipping_charge;

                    $cart = new Cart;
                    $cart->user_id = $userId;
                    $cart->product_id = $data['product_id'];
                    $cart->product_name = $data['product_name'];
                    $cart->product_price = $data['product_price'];
                    $cart->category_id = $data['category_id'];
                    $cart->coupon_id = $data['coupon_id'];
                    $cart->product_image = $data['product_image'];
                    $cart->product_discount = $discount;
                    $cart->shipping_charge = $shipping_charge;
                    $cart->product_total = $ProductTotal;
                    $cart->payment_status = 0;
                    $cart->session_id = Str::random(40);
                    $cart->save();

                    return redirect('/cart')->with('success', 'Product added to your cart');
                }
            } else {
                return redirect()->back()->with('error', 'Please Login!!!');
            }
        }
    }
}
