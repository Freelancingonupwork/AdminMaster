<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function orderList($type = null){

        $orderData = Order::with('product','category','coupon','cart')->where(['user_id' => Auth::user()->id, 'status' => 1])->get()->toArray();
        if($type==="subscription"){
            $orderData = Subscription::where(['user_id' => Auth::user()->id, 'status' => 1])->get()->toArray();
        }
        return view('front.checkout.orderList')->with(compact('type', 'orderData'));
    }
}
