<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateDeviceTokenRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {
        $data = array();
        //validation
//        $request->validate([
//            'username' => 'required|string|email',
//            'password' => 'required|string',
//            'device_token' => 'required',
//            'device_type' => 'required',
//        ]);

        $credentials = request(['email', 'password']);
//         print_r($credentials);die;
        if (!Auth::guard('web')->attempt($credentials))
            return $this->generateResponse(false, 'Unauthorized',null,401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addMinute(1);
        $token->save();
        $data=[
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user' => $user,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        // update device token and type
        $users = User::find($user->id);
        if ($users){
            $users->device_token=$request->device_token;
            $users->device_type=$request->device_type;
            $users->save();
        }

        return $this->generateResponse(true, 'Login Successfully!',$data);
        try {
            $login = json_decode($request->getContent(), true);
            $username = $login['username'];
            $password = $login['password'];

            $user = User::login($request);
            // Check user exist into database or not
            if (!$user) {
                throw new Exception("Login Fail, please check your credential");
            }
            // Check user password
            if (!Hash::check($password, $user->password)) {
                throw new Exception("Login Fail, pls check password");
            }
            $credentials = ['email' => $username, 'password' => $password, 'status' => 'active'];
            // print_r($credentials);die;
            if (!Auth::attempt($credentials)) {
                throw new Exception("Login Fail, pls check email/password, Or check Account status");
            }
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addMinute(1);
            $token->save();
            $data = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];
            return $this->generateResponse(true, 'Login Successfully!', $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    public function register(RegistrationRequest $request)
    {

        $user = new User;
        $user->first_name = $request->fName;
        $user->last_name = $request->lName;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->dob = $request->dob;
        $user->phone = $request->phone;
//        $user->hasPermissionTo('Create', 'web');
        $user->assignRole($request->type)->syncPermissions(['Create','update']);
        $user->save();

        return $this->generateResponse(true, 'Login Successfully!', [
            'message' => 'Successfully created user!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->generateResponse(true, 'Successfully logged out');
    }

    public function user(Request $request)
    {
        return $this->generateResponse(true,'user detail',$request->user());
    }
}
