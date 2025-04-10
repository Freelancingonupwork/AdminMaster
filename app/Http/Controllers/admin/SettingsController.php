<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SettingsController extends Controller
{
    public function email(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                $validation = [
                    'smtp_host' => ['required', 'string', 'nullable', 'max:255'],
                    'smtp_port' => ['required', 'numeric', 'nullable', 'digits_between:1,5'],
                    'smtp_user' => ['required', 'string', 'nullable', 'email'],
                    'smtp_password' => ['string', 'nullable'],
                    'smtp_encryption' => ['string', 'nullable'],
                    'from_mail' => ['required', 'string', 'nullable', 'email'],
                    'from_name' => ['required', 'string', 'nullable'],
                ];
                $validator = Validator::make($data, $validation);
                $input = $request->except(['_token']);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
                }

                foreach ($input as $key => $value) {
                    $settings = new Setting();
                    $settings->setSetting($key, $value);
                }

                return redirect()->back()->with('success', 'Email Setting updated successfully');
            }

            $settings = new Setting();
            return view('admin.settings.email')->with(compact('settings'));
        } catch (Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $th->getMessage(),
                ],
                403
            );
        }
    }

    public function twillio(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                $validation = [
                    'twilio_account_sid' => ['string', 'nullable', 'max:255'],
                    'twilio_auth_token' => ['string', 'nullable', 'max:255'],
                    'twilio_number' => ['string', 'nullable', 'max:12'],
                ];
                $validator = Validator::make($request->all(), $validation);
                $input = $request->except(['_token']);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            'success' => false,
                            'errors' => $validator->getMessageBag(),
                        ],
                        400
                    );
                }

                foreach ($input as $key => $value) {
                    $settings = new Setting();
                    $settings->setSetting($key, $value);
                }

                return redirect()->back()->with('success', 'Twillio Setting updated successfully');
            }

            $settings = new Setting();
            return view('admin.settings.twillio')->with(compact('settings'));
        } catch (Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $th->getMessage(),
                ],
                403
            );
        }
    }

    public function stripeIndex()
    {
        $user = auth()->user();
        return view('admin.settings.stripe.stripePayment', ['intent' => $user->createSetupIntent()]);
    }

    public function singelCharge(Request $request)
    {
        $amount = $request->amount;
        $paymentMethod = $request->payment_method;
        // echo "<pre>"; print_r($request->all()); die;

        // Create Stripe Customer
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripe_customer = \Stripe\Customer::create(array(
            "name" => auth()->user()->name,
            "email" => auth()->user()->email,
            "source" => $request->stripeToken,
            'address' => [
                'line1' => '510 Townsend St',
                'postal_code' => '98140',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'US',
            ],
        ));

        // Retrive Card
        $response = Http::withBasicAuth(env('STRIPE_SECRET'), '')
            ->get('https://api.stripe.com/v1/customers/'.$stripe_customer->id.'/cards/'.$stripe_customer->default_source.'');

        // You can then access the response data using $response->json()
        $responseData = $response->json();

        // Do something with the response data
        // dd($responseData);

        // charge

        $charge = $stripe->paymentIntents->create([
            'customer' => $stripe_customer->id,
            'currency' => 'GBP',
            'amount' => 1000 * 100,
            'off_session' => true,
            'payment_method_types' => ['card'],
            'confirm' => true,
            'description' => "Order from Website",
            "shipping" => [
                "name" => auth()->user()->name,
                "phone" => 7418529630,
                'address' => [
                    'line1' => '510 Townsend St',
                    'postal_code' => '98140',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                ],
            ]
        ]);

        // $charge = \Stripe\Charge::create([
        //     'amount' => 2000,
        //     'currency' => 'usd',
        //     'customer' => $stripe_customer->id,
        //     'description' => 'Testing Charge'
        // ]);

        echo "<pre>"; print_r($charge); die;
    }

    // public function saveStripeCard(Request $request)
    // {

    //     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    //     $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

    //     $response = \Stripe\Checkout\Session::retrieve($request->session_id, []);

    //     $intentDetail = $stripe->setupIntents->retrieve($response->setup_intent, []);
    //     dd($intentDetail);

    //     // Attach a PaymentMethod to a Customer

    //     $attachPaymentMethod = $stripe->paymentMethods->attach(
    //         $intentDetail->payment_method,
    //         ['customer' => $intentDetail->customer]
    //     );

    //     if (!empty($attachPaymentMethod)) {
    //         if (Auth::user() == null) {

    //             $customer = Customer::where('user_id', $userId)->first();
    //         } else {

    //             $customer = Customer::where('user_id', auth()->id())->first();
    //         }
    //         if (empty($customer)) {
    //             $customer =  new Customer();
    //             $customer->user_id = $userId;
    //         }
    //         $customer->card_details = json_encode($attachPaymentMethod);
    //         $customer->save();
    //     }
    //     $customer = $request->session()->put('customer', $customer);
    //     return redirect()->route('checkout.get.step04')->with('success', "Added Successfully");
    // }
}
