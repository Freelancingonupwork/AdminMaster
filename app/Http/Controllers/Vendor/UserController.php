<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\StripeAccountDetails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return view('vendor.vendorDashboard');
        } else {
            return view('vendor.auth.login');
        }
    }

    public function vendor_login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'email' => 'required|email',
                'password' => 'required'
            ];

            $messages = [
                'email.required' => 'Please enter your email',
                'email.email' => 'Please enter valid email',
                'password' => 'Please enter password'
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }

            if (isset($data['remember']) || !empty($data['remember'])) {
                $remember = true;
            } else {
                $remember = false;
            }
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'isUser' => 2], $remember)) {
                if ($remember) {
                    setcookie('email', $data['email'], time() + 3600);
                    setcookie('password', $data['password'], time() + 3600);
                } else {
                    setcookie('email', '');
                    setcookie('password', '');
                }

                if(!isset($data['device_token']) || empty($data['device_token'])){
                    $data['device_token'] = "";
                }

                if(!isset($data['device_type']) || empty($data['device_type'])){
                    $data['device_type'] = "";
                }

                $login = array(
                    'login_key' => $this->getLoginKey(Auth::user()->id),
                    'device_token' => $data['device_token'],
                    'device_type' => $data['device_type']
                );

                $updateUser = User::where(['id' => Auth::user()->id, 'email' => $data['email']])->update($login);
                return redirect()->route('vendor.dashboard');
            } else {
                return redirect()->back()->withErrors("Invalid Credentials");
            }
        }
        if (Auth::check()) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendor.auth.login');
    }

    public function getLoginKey($user_id)
    {
        $salt = "23df$#%%^66sd$^%fg%^sjgdk90fdklndg099ndfg09LKJDJ*@##lkhlkhlsa#$%";
        $login_key = hash('sha1', $salt . $user_id . time());
        return $login_key;
    }

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'email' => 'required|email',
            ];

            $messages = [
                'email.requierd' => 'Please enter your email',
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }

            // Check if email exists or not
            $user = User::where(['email' => $data['email']])->first();
            if ($user) {
                $markdown = 'vendor.email.forgotPassword';
                $token = $token = Str::random(64);

                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
                if (Mail::to($data['email'])->send(new ForgotPassword($markdown, $user, $token))) {
                    return redirect()->back()->withSuccess("Please Check your Mailbox");
                }
            } else {
                return redirect()->back()->withErrors("Email does not exist");
            }
        }
        return view('vendor.auth.forgotPassword');
    }

    public function resetPassword(Request $request, $token)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
                'cpassword' => 'required_with:password|same:password'
            ];

            $messages = [
                'email.requierd' => 'Please enter your email',
                'email.email' => 'Please enter valid email',
                'password' => 'Please enter password',
                'cpassword.same' => 'Passwoed and Confirm Password must be same'
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }

            $resetPassData = DB::table('password_resets')->where(['token' => $token])->first();

            if (!$resetPassData) {
                return redirect()->back()->with('error', 'Invalid token!');
            }
            $user = User::where('email', $data['email'])->update(['password' => Hash::make($request->password)]);
            if ($user) {
                return redirect('/vendor')->with('success', 'Password reset successfully');
                $resetPassData = DB::table('password_resets')->where(['token' => $token])->delete();
            } else {
                return redirect('/forgot-password')->with('error', 'Something went wrong. Please try again');
            }
        }
        return view('vendor.auth.resetPassword')->with(compact('token'));
    }

    public function profile(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // echo "<pre>"; print_r($data); die;

            if (!isset($data['avatar']) || empty($data['avatar'])) {
                $profilePic = Auth::user()->avatar;
            } else {
                $profilePic = time() . '.' . $data['avatar']->extension();
                if (! Storage::disk('public')->exists("/users/avatars")) {
                    Storage::disk('public')->makeDirectory("/users/avatars"); //creates directory
                }
                if (Storage::disk('public')->exists("/users/".Auth::user()->avatar)) {
                    Storage::disk('public')->delete("/users/".Auth::user()->avatar);
                }
                $request->avatar->storeAs("users/avatars", $profilePic, 'public');

                $profilePic = "users/avatars/$profilePic";
            }
            $user = User::where(['id' => Auth::user()->id])->update(['name' => $data['name'], 'mobile' => $data['mobile'], 'avatar' => $profilePic]);
            return redirect()->back()->with('success', 'User details updated successfully.');
        }
        return view('vendor.vendorProfile');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/vendor');
    }

    public function stripePayment()
    {
        // try{
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            $stripeAccountDetails = $stripe->accounts->create([
                'type' => 'standard',
                'country' => 'US',
                'email' => Auth::user()->email
            ]);


            if(!empty($stripeAccountDetails->id)){
                $createStripeData = new StripeAccountDetails;
                $createStripeData->user_id = Auth::user()->id;
                $createStripeData->user_email = Auth::user()->email;
                $createStripeData->stripe_accountId = $stripeAccountDetails->id;

                if($createStripeData->save()){
                    $accountLink = $stripe->accountLinks->create([
                        'account' => $stripeAccountDetails->id,
                        'refresh_url' => 'http://localhost:8000/vendor/dashboard',
                        'return_url' => 'http://localhost:8000/vendor/dashboard',
                        'type' => 'account_onboarding',
                    ]);

                    if(!empty($accountLink->url)){
                        $updateStripeData = StripeAccountDetails::where(['user_id' => Auth::user()->id, 'user_email' => Auth::user()->email, 'stripe_accountId' => $stripeAccountDetails->id])->update(['stripe_accountLink' => $accountLink->url, 'created' => $accountLink->created, 'expires_at' => $accountLink->expires_at]);

                        if($updateStripeData){
                            return redirect()->route('vendor.dashboard')->with('success', 'Your payment setup is completed');
                        }
                    }
                }
            }
        // }catch(Throwable $th){
        //     return redirect()->route('vendor.dashboard')->with('error', $th);
        // }
        // $stripeAccount = $stripe->accounts->retrieve('acct_1OWedgSBG6srozWG', []);
    }
}
