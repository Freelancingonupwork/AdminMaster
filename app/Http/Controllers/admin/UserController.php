<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return view('admin.dashboard');
        } else {
            return view('admin.auth.login');
        }
    }

    public function admin_login(Request $request)
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
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'isUser' => 0], $remember)) {
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
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->back()->withErrors("Invalid Credentials");
            }
        }
        return view('admin.auth.login');
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
                $markdown = 'admin.email.forgotPassword';
                $token = $token = Str::random(64);

                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
                Mail::to($data['email'])->send(new ForgotPassword($markdown, $user, $token));
            } else {
                return redirect()->back()->withErrors("Email does not exist");
            }
        }
        return view('admin.auth.forgotPassword');
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
                return back()->withInput()->with('error', 'Invalid token!');
            }

            $user = User::where('email', $data['email'])->update(['password' => Hash::make($request->password)]);

            $resetPassData = DB::table('password_resets')->where(['token' => $token])->delete();
            return redirect('/admin')->with('success', 'Password reset successfully');
        }
        return view('admin.auth.resetPassword')->with(compact('token'));
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
        return view('admin.profile');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }

    public function users()
    {
        $users = User::get();

        return view('admin.settings.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.settings.users.create');
    }

    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'name' => 'required',
                'type' =>  'required',
                'email' => 'required|email',
            ];

            $messages = [
                'name.required' => "Please enter user name",
                'type.required' => "Please enter user type",
                'email.required' => 'Please enter user email',
                'email.email' => 'Please enter valid email',
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }

            $randomStr = Str::random(8);
            $userSlug = Str::slug($data['name']);
            if (!isset($data['referCode']) && empty($data['referCode'])) {
                $referalCode = $userSlug . '-' . $randomStr;
            } else {
                $referalCode = $userSlug . $randomStr . $data['referCode'];
            }

            if(($data['type'] === "vendor") || ($data['type'] === "Vendor")){
                $isUser = 2;
            }else{
                $isUser = 0;
            }

            $password = Hash::make('test123');
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $password;
            $user->type = $data['type'];
            $user->isUser = $isUser;
            $user->referal_code = $referalCode;
            $user->save();

            return redirect('admin/user-index')->withSuccess(__('User created successfully.'));
        }
    }

    public function edit(User $user)
    {
        return view('admin.settings.users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'name' => 'required',
                'type' =>  'required',
                'email' => 'required|email',
            ];

            $messages = [
                'name.required' => "Please enter user name",
                'type.required' => "Please enter user type",
                'email.required' => 'Please enter user email',
                'email.email' => 'Please enter valid email',
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation->messages());
            }

            $userUpdate = User::where(['id' => $user->id])->update(['name' => $data['name'], 'email' => $data['email'], 'type' => $data['type']]);
            $user->syncRoles($request->get('role'));

            return redirect('admin/user-index')->withSuccess(__('User updated successfully.'));
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->withSuccess(__('User deleted successfully.'));
    }
}
