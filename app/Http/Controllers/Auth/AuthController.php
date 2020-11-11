<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {

        $credentials = request(['email', 'password']);
//         print_r($credentials);die;
        if (!Auth::guard('web')->attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
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

        return $this->generateResponse(true, 'Login Successfully!',$data);
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

        return $this->generateResponse(true, 'Login Successfully!',[
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
