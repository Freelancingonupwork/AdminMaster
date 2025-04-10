<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubscriberController extends Controller
{
    public function subscriberPost(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();

            Session::put(['data'=>$data]);

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $redirect_url = route('subscribe-checkout') . '?session_id={CHECKOUT_SESSION_ID}';
            
            $amount = $data['plan_value'];
    
            $stripeData = $stripe->checkout->sessions->create([
                'customer_email' => Auth::user()->email,
    
                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => $data['plan_name'],
                                'description' => Auth::user()->name. " has made subscription on your portal ".env('APP_NAME'),
                            ],
                            'unit_amount' => $amount * 100,
                            'currency' => 'inr',
                        ],
                        'quantity' => 1,
                    ]
                ],
    
                'mode' => 'payment',
                'success_url' => $redirect_url,
            ]);
            Session::flash('success', 'Payment successful!');
    
            return redirect($stripeData['url']);
        }
    }

    public function subscriberCheckoutSuccess(Request $request)
    {

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $response = $stripe->checkout->sessions->retrieve($request->session_id);

        
        if(Session::has('data')){
            $data = Session::get('data');

            $startDate = date("Y-m-d");
            if($data['plan_type'] === "0"){
                $endDate = date("Y-m-d",strtotime($startDate. ' + 1 month'));
            }elseif($data['plan_type'] === "1"){
                $endDate = date("Y-m-d",strtotime($startDate. ' + 6 month'));
            }else{
                $endDate = date("Y-m-d",strtotime($startDate. ' + 1 year'));
            }

            $subscription = new Subscription;
            $subscription->user_id = Auth::user()->id;
            $subscription->user_email = Auth::user()->email;
            $subscription->plan_name = $data['plan_name'];
            $subscription->plan_value = $data['plan_value'];
            $subscription->subscription_type = $data['plan_type'];
            $subscription->start_date = $startDate;
            $subscription->end_date = $endDate;
            $subscription->subscription_status = 1;
            $subscription->save();
        }

        return redirect()->route('listing')->with('success', "Payment Successfull");
    }
}
