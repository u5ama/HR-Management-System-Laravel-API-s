<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyProfileResource;
use App\Http\Resources\StaffProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | Auth Controller
     |--------------------------------------------------------------------------
     |
     | This controller handles authenticating and registering users for the application and
     | redirecting them to your home screen. The controller uses a trait
     | to conveniently provide its functionality to your applications.
     |
     */

    /**
     * Login
     *
     * Check that the login is working. Add the required email and password. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with an error, and a response listing the failed messages.
     *
     * @response 401 scenario="Invalid Email / Password" {"status": "false", "message": "Invalid Email / Password"}
     * @response 401 scenario="Status Not Active" {"status": "false", "message": "User is not Active. Contact Admin."}
     * @responseField The response of API will be user object with Auth Token.
     */

    public function login(Request $request)
    {
        try {
            $validator_array = [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 401);
            }

            $credentials = $request->only('email', 'password');

            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email / password',
                ]);
            }

            $user = User::where(['email'=> $request->email, 'user_status' => 'active'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account NotActive / Deactivated. Contact Admin.'
                ]);
            }

//            $ttl = ($request->remember_me === true) ? env('JWT_REMEMBER_TTL') : env('JWT_TTL');

            $user = Auth::user();
            $user->token = $token;

            if ($user->user_type == 'company'){
                $data = new CompanyProfileResource($user);
            }else{
                $data = new StaffProfileResource($user);
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            $error = ['field'=>'login_attempt','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Register
     *
     * Check that the Register is working. Add the required fields for user registration. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with an error, and a response listing the validation errors.
     *
     * @response 401 scenario="Invalid Email OR Email Already Taken." {"status": "false", "message": "Email already Taken."}
     * @responseField The response of API will be user object with Auth Token.
     *
     */

    public function register(Request $request)
    {
        try {
            $validator_array = [
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 401);
            }
            if ($request->user_type !== '' && $request->user_type !== 'staff')
            {
                $user = User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_status' => 'active',
                    'user_type' => 'company',
                ]);
            }else{
                return response()->json(['success' => false, 'message' => 'Company Not Created. Use proper user type.'], 401);
            }

            $token = JWTAuth::fromUser($user);
            $user->token = $token;

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully!',
                'data' => $user,
                'type' => 'bearer',
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            $error = ['field'=>'register_attempt','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Logout
     *
     * Check that the Logout is working. Add Token for User Authentication. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with an error, and a response listing the errors.
     *
     * @response 401 scenario="Invalid Token."{"status": "false", "message": "Token is not set, please retry action or login"}
     * @responseField The response of API will be {"status": "true", "message": "User Logout Successfully!"}.
     *
     */

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (! isset($token) ) {
            return response()->json([
                'success' => false,
                'message' => 'Token is not set, please retry action or login.'
            ]);
        }
        try {

            JWTAuth::setToken($token)->invalidate(true);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

        } catch (JWTException $e)
        {
            $message = $e->getMessage();
            $error = ['field'=>'logout_attempt','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Forgot Password
     *
     * Check that the Forgot Password is working. Add the required field i.e. for the password recovery. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with an error, and a response listing the errors.
     *
     * @response 401 scenario="Invalid Email. Email Not Found." {"status": "false", "message": "Invalid Email. Email Not Found."}
     * @responseField The response of API will be message 'Reset link is send successfully, Please check your inbox.'.
     *
     */

    public function forgotPassword(Request $request)
    {
        try {
            $validator_array = [
                'email' => 'required|string|email|max:255',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 401);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Email. Email Not Found.'
                ]);
            }
            $token = Password::getRepository()->create($user);
            $array = [
                'name'                   => $user->name,
                'actionUrl'              => route('reset-password', [$token]),
                'mail_title'             => "Password Reset",
                'reset_password_subject' => "Reset your password",
                'main_title_text'        => "Password Reset",
            ];
//            Mail::to($request->input('email'))->send(new ResetPasswordEmail($array));
            return response()->json([
                'success' => false,
                'message' => 'Reset link is send successfully, please check your inbox.'
            ],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            $error = ['field'=>'forgot_password_attempt','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Reset Password
     *
     * Check that the Reset Password is working. Add the required field for password change. If everything is okay, you'll get a 200 OK response.
     *
     * Otherwise, the request will fail with an error, and a response listing the validation errors.
     *
     * @response 401 scenario="New Password is Required." {"status": "false", "message": "New Password is Required."}
     * @responseField The response of API will be message 'Password Reset Successfully!'.
     *
     */

    public function resetPassword(Request $request)
    {
        try {
            $validator_array = [
                'token' => 'required',
                'new_password' => 'required',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if($validator->fails()){
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 401);
            }
            $new_password = $request->input('new_password');
            $tokens = DB::table('password_resets')->select('email', 'token')->get();

            if(count($tokens) > 0){
                foreach($tokens as $token){
                    if(Hash::check($request->input('token'), $token->token)){
                        $user = User::where('email', $token->email)->first();
                        if($user){
                            $user->password = bcrypt($new_password);
                            $user->update();
                            DB::table('password_resets')->where('email', $user->email)->delete();

                            return response()->json([
                                'success' => true,
                                'message' => "Password Reset Successfully!",
                            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

                        } else{
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid Email. Email Not Found.',
                            ], 403);
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            $error = ['field'=>'reset_password_attempt','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
