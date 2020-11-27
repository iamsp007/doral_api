<?php

namespace App\Http\Controllers\Auth;


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
    public function login(Request $request)
    {
        $data = array();
        //validation
        $request->validate([
            'username' => 'required|string|email',
            'password' => 'required|string'
        ]);

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

    public function register(Request $request)
    {
        $request->validate([
            'fName' => 'required|string',
            'lName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
            'dob' => 'required|date',
            'phone' => 'required|numeric'
        ]);
        $user = new User;
        $user->first_name = $request->fName;
        $user->last_name = $request->lName;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->dob = $request->dob;
        $user->phone = $request->phone;
        $user->save();

        return $this->generateResponse(true, 'Login Successfully!', [
            'message' => 'Successfully created user!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
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
        $user = User::gethUserUsingEmail($input['email']);
        $userid = $user->id;
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
