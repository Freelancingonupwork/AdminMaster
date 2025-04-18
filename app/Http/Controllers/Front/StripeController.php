<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\StripeAccountDetails;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function stripePost(Request $request)
    {

        $session = Session::get('cartData');
        $grandTotal = Session::get('grandTotal');
        $grandTotal = $grandTotal[0]->grandTotal;

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $redirect_url = route('stripe-checkout') . '?session_id={CHECKOUT_SESSION_ID}';

        $stripeData = $stripe->checkout->sessions->create([
            'customer_email' => Auth::user()->email,
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => Auth::user()->name,
                        ],
                        'unit_amount' => $grandTotal * 100,
                        'currency' => 'inr',
                    ],
                    'quantity' => 1,
                ]
            ],

            'mode' => 'payment',
            'success_url' => $redirect_url,
        ]);

        return redirect($stripeData['url']);
    }

    public function checkoutSuccess(Request $request)
    {

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $response = $stripe->checkout->sessions->retrieve($request->session_id);
        $session = Session::get('cartData');

        foreach ($session as $cartData) {

            $orders = new Order;
            $orders->user_id = Auth::user()->id;
            $orders->user_email = Auth::user()->email;
            $orders->product_id = $cartData['product_id'];
            $orders->category_id = $cartData['category_id'];
            $orders->coupon_id = $cartData['coupon_id'];
            $orders->cart_id = $cartData['id'];
            $orders->order_date = date("Y-m-d");
            $orders->order_time = date("H:i:s");
            $orders->is_paid = 1;
            if ($orders->save()) {
                $updateCart = Cart::where(['id' => $cartData['id'], 'user_id' => Auth::user()->id, 'status' => 1])->update(['payment_status' => "1"]);
                Session::forget('cartData');
                Session::forget('grandTotal');
            }
        }

        return redirect()->route('listing')->with('success', "Payment Successfull");
    }
}
