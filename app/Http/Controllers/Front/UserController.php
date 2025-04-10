<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            if (isset($data['remember']) || !empty($data['remember'])) {
                $remember = true;
            } else {
                $remember = false;
            }
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'status' => 1, 'isUser' => 1], $remember)) {
                if ($remember) {
                    setcookie('email', $data['email'], time() + 3600);
                    setcookie('password', $data['password'], time() + 3600);
                } else {
                    setcookie('email', '');
                    setcookie('password', '');
                }

                if (!isset($data['device_token']) || empty($data['device_token'])) {
                    $data['device_token'] = "";
                }

                if (!isset($data['device_type']) || empty($data['device_type'])) {
                    $data['device_type'] = "";
                }

                $login = array(
                    'login_key' => $this->getLoginKey(Auth::user()->id),
                    'device_token' => $data['device_token'],
                    'device_type' => $data['device_type']
                );

                $updateUser = User::where(['id' => Auth::user()->id, 'email' => $data['email']])->update($login);
                return redirect()->route('cart')->with('success', "Logged In Successfully");
            } else {
                return redirect()->back()->withErrors("Invalid Credentials");
            }
        }
    }

    public function getLoginKey($user_id)
    {
        $salt = "23df$#%%^66sd$^%fg%^sjgdk90fdklndg099ndfg09LKJDJ*@##lkhlkhlsa#$%";
        $login_key = hash('sha1', $salt . $user_id . time());
        return $login_key;
    }

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // echo "<pre>"; Print_r($data); die;
            //Check if User already exists
            $usersCount = User::where('email', $data['email'])->count();

            if ($usersCount > 0) {
                return redirect()->back()->with('error', 'This email is already exists with another account.');
            } else {

                $randomStr = Str::random(8);
                $userSlug = Str::slug($data['name']);
                if (!isset($data['referCode']) && empty($data['referCode'])) {
                    $referalCode = $userSlug . '-' . $randomStr;
                } else {
                    $referalCode = $userSlug . $randomStr . $data['referCode'];
                }
                // $password = Hash::make($data['password']);

                // die($password);
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->password = $data['password'];
                $user->type = "user";
                $user->referal_code = $referalCode;
                $user->status = 1;
                $user->isUser = $data['isUser'];
                $user->save();

                return redirect()->back()->with('success', 'Please login.');
            }
        }
    }

    public function invite(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            if (Auth::check()) {
            } else {
                return redirect()->back()->with('error', "Please login first");
            }
        }

        $authUserReferCode = Auth::user()->referal_code;
        $link = "/register/" . $authUserReferCode . "/" . Auth::user()->id;
        $planData = Plan::get()->toArray();
        return view('front.auth.invite')->with(compact('link', 'planData'));
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        if ($request->isMethod('post')) {
            $validation = [
                'name' => ['nullable'],
                'email' => ['nullable', 'regex:/(.+)@(.+)\.(.+)/i', 'unique:users,email,' .$user->id],
                'mobile' => ['nullable', 'numeric', 'min:10', 'unique:users,mobile, ' . $user->id],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ];

            $validation_messages = [
                'name.required' => 'The Name field is required.',
                'email.required' => 'The E-Mail field is required.',
                'email.regex' => 'Enter valid email address.',
                'email.unique' => 'Entered Email has already been taken.',
                'mobile.required' => 'The Mobile field is required.',
                'mobile.numeric' => 'Enter valid mobile number.',
                'mobile.unique' => 'Entered mobile number is already used.',
            ];

            $validator = Validator::make($request->all(), $validation, $validation_messages);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->getMessageBag());
            }
            $input = $request->all();
            //Check if User already exists
            // $usersCount = User::where('email', $input['email'])->count();

            // if ($usersCount > 0) {
            //     return redirect()->back()->with('error', "Email already exists.");
            // } else {
                if (!isset($input['name']) || empty($input['name'])) {
                    $input['name'] = $user->name;
                }
                if (!isset($input['email']) || empty($input['email'])) {
                    $input['email'] = $user->email;
                }
                if (!isset($input['mobile']) || empty($input['mobile'])) {
                    $input['mobile'] = $user->mobile;
                }
                if (!isset($input['avatar']) || empty($input['avatar'])) {
                    $input['avatar'] = $user->avatar;
                }
                if ($request->has('avatar')) {
                    $profilePic = time() . '.' . $input['avatar']->extension();
                    if (!Storage::disk('public')->exists("/users/avatars")) {
                        Storage::disk('public')->makeDirectory("/users/avatars"); //creates directory
                    }
                    if (Storage::disk('public')->exists("/users/$user->avatar")) {
                        Storage::disk('public')->delete("/users/$user->avatar");
                    }
                    $request->avatar->storeAs("users/avatars", $profilePic, 'public');

                    $input['avatar'] = "users/avatars/$profilePic";
                }

                $updateUser = User::where(['id' => $user->id])->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'avatar' => $input['avatar']]);
                if ($updateUser) {
                    $user = User::where(['id' => $user->id])->first()->toArray();
                    return redirect()->back()->with('success', 'Profile Updated Successfully !!!');
                }
            // }
        }
        return view('front.auth.profile');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('listing');
    }
}
