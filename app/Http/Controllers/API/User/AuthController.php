<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\ApiController;
use App\Mail\ForgotPassword;
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

class AuthController extends ApiController
{
    public function userRegister(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                /* Validate Data */
                $validation = [
                    'name' => ['required'],
                    'email' => ['required', 'regex:/(.+)@(.+)\.(.+)/i', 'unique:users,email'],
                    'password' => [
                        'required',
                        'min:10',
                        'regex:/[a-z]/',     // must contain at least one lowercase letter
                        'regex:/[A-Z]/',     // must contain at least one uppercase letter
                        'regex:/[0-9]/',     // must contain at least one digit
                        'regex:/[@$!%*#?&]/' // must contain a special character
                    ],
                    'cnf_password' => ['required', 'same:password'],
                    'user_type' => ['required', 'in:admin,vendor,user'],
                    'mobile' => ['required', 'numeric', 'min:10', 'unique:users,mobile']
                ];
                $validation_messages = [
                    'name.required' => 'The Name field is required.',
                    'email.required' => 'The E-Mail field is required.',
                    'email.regex' => 'Enter valid email address.',
                    'email.unique' => 'Entered Email has already been taken.',
                    'password.required' => 'The Password field is required.',
                    'password.regex' => 'Password must contains at least one lowercase, one uppercase, one digit and a special character.',
                    'cnf_password.required' => 'The Confirm Password field is required.',
                    'cnf_password.same' => 'The Password and Confirm Password should be same.',
                    'user_type.required' => 'The User Type field is required.',
                    'user_type.in' => 'The User Type must be either admin, vendor or user.',
                    'mobile.required' => 'The Mobile field is required.',
                    'mobile.numeric' => 'Enter valid mobile number.',
                    'mobile.unique' => 'Entered mobile number is already used.',
                ];
                $validator = Validator::make($request->all(), $validation, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            "status" => "fail",
                            // 'errors' => $validator->getMessageBag(),
                            'message' => $validator->errors()->first(),
                        ],
                        400
                    );
                }
                //Check if User already exists
                $usersCount = User::where('email', $data['email'])->count();

                if ($usersCount > 0) {
                    return $this->errorResponse('This email is already exists with another account.');
                } else {

                    $randomStr = Str::random(8);
                    $userSlug = Str::slug($data['name']);
                    if (!isset($data['referCode']) && empty($data['referCode'])) {
                        $referalCode = $userSlug . '-' . $randomStr;
                    } else {
                        $referalCode = $userSlug . $randomStr . $data['referCode'];
                    }

                    if (($data['user_type'] === "admin")) {
                        $isUser = 0;
                    } elseif (($data['user_type'] === "user")) {
                        $isUser = 1;
                    } else {
                        $isUser = 2;
                    }

                    $password = Hash::make($data['password']);

                    $user = new User();
                    $user->name = $data['name'];
                    $user->email = $data['email'];
                    $user->password = $password;
                    $user->type = $data['user_type'];
                    $user->mobile = $data['mobile'];
                    $user->referal_code = $referalCode;
                    $user->status = 1;
                    $user->isUser = $isUser;
                    $user->save();

                    if ($user->save()) {
                        return $this->getSingleResponse("USER REGISTERED SUCCESSFULLY", $user, 200);
                    }
                }
            }
        } catch (Throwable $th) {
            return $this->errorResponse("Something went wrong", $th, 400);
        }
    }

    public function userLogin(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                /* Validate Data */
                $validation = [
                    'email' => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
                    'password' => ['required'],
                    'device_token' => ['required'],
                    'device_type' => ['required', 'in:I,A']
                ];
                $validation_messages = [
                    'email.required' => 'The E-Mail field is required.',
                    'email.regex' => 'Enter valid email address.',
                    'password.required' => 'The Password field is required.',
                    'device_token.required' => 'The Device Token field is required.',
                    'device_type.required' => 'The Device Type field is required.',
                    'device_type.in' => 'The Device Type should be either I or A.',
                ];
                $validator = Validator::make($request->all(), $validation, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            "status" => "fail",
                            // 'errors' => $validator->getMessageBag(),
                            'message' => $validator->errors()->first(),
                        ],
                        400
                    );
                }

                if (isset($data['remember']) || !empty($data['remember'])) {
                    $remember = true;
                } else {
                    $remember = false;
                }

                if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
                    $login = array(
                        'login_key' => $this->getLoginKey(Auth::user()->id),
                        'device_token' => $data['device_token'],
                        'device_type' => $data['device_type']
                    );

                    $updateUser = User::where(['id' => Auth::user()->id, 'email' => $data['email']])->update($login);
                    if ($updateUser) {
                        $resData = [];
                        $userData = User::where(['id' => Auth::user()->id, 'email' => Auth::user()->email])->first();

                        $token = $userData->createToken('user')->accessToken;
                        $resData = $userData;
                        $resData['token'] = $token;
                        return $this->successResponse("USER LOGGED IN SUCCESSFULLY", $resData, 200);
                    }
                } else {
                    return $this->errorResponse("Wrong Credentials", [], 400);
                }
            }
        } catch (Throwable $th) {
            return $this->errorResponse("Something went wrong", $th, 400);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                /* Validate Data */
                $validation = [
                    'email' => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
                ];
                $validation_messages = [
                    'email.required' => 'The E-Mail field is required.',
                    'email.regex' => 'Enter valid email address.',
                ];
                $validator = Validator::make($request->all(), $validation, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            "status" => "fail",
                            // 'errors' => $validator->getMessageBag(),
                            'message' => $validator->errors()->first(),
                        ],
                        400
                    );
                }

                // Check if email exists or not
                $user = User::where(['email' => $data['email']])->first();
                if ($user) {
                    $markdown = 'api.email.forgotPassword';
                    $token = $token = Str::random(64);

                    DB::table('password_resets')->insert([
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => Carbon::now()
                    ]);
                    if (Mail::to($data['email'])->send(new ForgotPassword($markdown, $user, $token))) {
                        $res['token'] = $token;
                        $res['url'] = "http://127.0.0.1:8000/api/v1/reset-password";
                        return $this->successResponse("Please check your mail.", $res);
                    }
                } else {
                    return $this->errorResponse("Wrong Credentials", [], 400);
                }
            }
        } catch (Throwable $th) {
            return response()->json(
                [
                    "status" => "fail",
                    // 'errors' => $validator->getMessageBag(),
                    'message' => $th,
                ],
                400
            );
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();

                /* Validate Data */
                $validation = [
                    'email' => ['required', 'regex:/(.+)@(.+)\.(.+)/i'],
                    'password' => [
                        'required',
                        'min:10',
                        'regex:/[a-z]/',     // must contain at least one lowercase letter
                        'regex:/[A-Z]/',     // must contain at least one uppercase letter
                        'regex:/[0-9]/',     // must contain at least one digit
                        'regex:/[@$!%*#?&]/' // must contain a special character
                    ],
                    'cnf_password' => ['required', 'same:password'],
                    'token' => ['required']
                ];
                $validation_messages = [
                    'email.required' => 'The E-Mail field is required.',
                    'email.regex' => 'Enter valid email address.',
                    'password.required' => 'The Password field is required.',
                    'password.regex' => 'Password must contains at least one lowercase, one uppercase, one digit and a special character.',
                    'cnf_password.required' => 'The Confirm Password field is required.',
                    'cnf_password.same' => 'The Password and Confirm Password should be same.',
                    'token.required' => 'The Token field is required.',
                ];
                $validator = Validator::make($request->all(), $validation, $validation_messages);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            "status" => "fail",
                            // 'errors' => $validator->getMessageBag(),
                            'message' => $validator->errors()->first(),
                        ],
                        200
                    );
                }

                $resetPassData = DB::table('password_resets')->where(['token' => $data['token'], 'email' => $data['email']])->first();

                if (!$resetPassData) {
                    return $this->errorResponse("Invalid Token", [], 400);
                }

                $user = User::where('email', $data['email'])->update(['password' => Hash::make($request->password)]);
                if ($user) {
                    $resetPassData = DB::table('password_resets')->where(['token' => $data['token']])->delete();
                    return $this->getSingleResponse("PASSWORD RESET SUCCESSFULLY.");
                }
            }
        } catch (Throwable $th) {
            return $this->errorResponse("Something went wrong", $th, 400);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        try {
            /* Validate Data */
            $validation = [
                'name' => ['nullable'],
                'email' => ['nullable', 'regex:/(.+)@(.+)\.(.+)/i', 'unique:users,email,' . $user->id],
                'mobile' => ['nullable', 'numeric', 'min:10', 'unique:users,mobile,' . $user->id],
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
            $input = $request->all();
            if ($validator->fails()) {
                return response()->json(
                    [
                        "status" => "fail",
                        'errors' => $validator->getMessageBag(),
                        // 'message' => $validator->errors()->first(),
                    ],
                    400
                );
            }

            if(Auth::check()){
                //Check if User already exists
                // $usersCount = User::where('email', $input['email'])->count();

                // if ($usersCount > 0) {
                //     return $this->errorResponse('This email is already exists with another account.');
                // } else {
                    if(!isset($input['name']) || empty($input['name'])){
                        $input['name'] = $user->name;
                    }
                    if(!isset($input['email']) || empty($input['email'])){
                        $input['email'] = $user->email;
                    }
                    if(!isset($input['mobile']) || empty($input['mobile'])){
                        $input['mobile'] = $user->mobile;
                    }
                    if(!isset($input['avatar']) || empty($input['avatar'])){
                        $input['avatar'] = $user->avatar;
                    }
                    if ($request->has('avatar')) {
                        $profilePic = time() . '.' . $input['avatar']->extension();
                        if (! Storage::disk('public')->exists("/users/avatars")) {
                            Storage::disk('public')->makeDirectory("/users/avatars"); //creates directory
                        }
                        if (Storage::disk('public')->exists("/users/$user->avatar")) {
                            Storage::disk('public')->delete("/users/$user->avatar");
                        }
                        $request->avatar->storeAs("users/avatars", $profilePic, 'public');

                        $input['avatar'] = "users/avatars/$profilePic";
                    }

                    $updateUser = User::where(['id' => $user->id])->update(['name' => $input['name'], 'email' => $input['email'], 'mobile' => $input['mobile'], 'avatar' => $input['avatar']]);
                    if($updateUser){
                        $user = User::where(['id' => $user->id])->first()->toArray();
                        return $this->getSingleResponse("USER PROFILE UPDATED SUCCESSFULLY!!!", $user, 200);
                    }
                // }
            }else{
                return $this->successResponse("You don't have permission");
            }
        } catch (Throwable $th) {
            return $this->errorResponse("Something went wrong", $th, 400);
        }
    }
}
