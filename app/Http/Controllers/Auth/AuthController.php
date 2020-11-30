<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateDeviceTokenRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\referral;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {
        try {
            $username = $request->username;
            $password = $request->password;
            $field = 'email';
            if (is_numeric($request->username)) {
                $field = 'phone';
            }
            //$credentials = [$field => $username, 'password' => $password];
            $credentials = [$field => $username, 'password' => $password, 'status' => 'active'];
            // print_r($credentials);die;
            if (!Auth::attempt($credentials)) {
                return $this->generateResponse(false, $field . ' or Password are Incorrect!');
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
            // update device token and type
            if ($request->has('device_token')) {
                $users = User::find($user->id);
                if ($users) {
                    $users->device_token = $request->device_token;
                    $users->device_type = $request->device_type;
                    $users->save();
                }
            }

            return $this->generateResponse(true, 'Login Successfully!', $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function register(RegistrationRequest $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
            'dob' => 'required|date',
            'phone' => 'required|numeric'
        ]);
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->dob = $request->dob;
        $user->phone = $request->phone;
        //        $user->hasPermissionTo('Create', 'web');
        $user->assignRole($request->type)->syncPermissions(['Create', 'update']);
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
        return $this->generateResponse(true, 'user detail', $request->user());
    }

    public function forgotPassword(Request $request)
    {
        $data = [];
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $response = \Password::sendResetLink($request->only('email'));
                switch ($response) {
                    case \Password::RESET_LINK_SENT:
                        $message = trans($response);
                        return $this->generateResponse(true, $message, $data);
                    case \Password::INVALID_USER:
                        $message = trans($response);
                        return $this->generateResponse(true, $message, $data);
                }
            } catch (\Swift_TransportException $ex) {
                $message = $ex->getMessage();
                return $this->generateResponse(false, $message, $data);
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                return $this->generateResponse(false, $message, $data);
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $data = array();
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            $message =  $validator->errors()->first();
            return $this->generateResponse(false, $message, $data);
        } else {
            try {
                $user = User::gethUserUsingEmail($input['email']);
                if (!$user) {
                    throw new Exception("Email not match with database");
                }
                $userid = $user->id;
                if ((Hash::check(request('old_password'), $user->password)) == false) {
                    $message = "Check your old password.";
                    return $this->generateResponse(false, $message, $data);
                } else if ((Hash::check(request('new_password'), $user->password)) == true) {
                    $message = "Please enter a password which is not similar then current password.";
                    return $this->generateResponse(false, $message, $data);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $message = "Password updated successfully.";
                    return $this->generateResponse(true, $message, $data);
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $message = $ex->errorInfo[2];
                } else {
                    $message = $ex->getMessage();
                }
                return $this->generateResponse(false, $message, $data);
            }
        }
    }
}
