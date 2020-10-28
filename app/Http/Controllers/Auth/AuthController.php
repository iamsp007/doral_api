<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);
        // print_r($credentials);die;
        if (!Auth::attempt($credentials))
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
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return $this->generateResponse(true, 'Login Successfully!',$data);
    }

    public function register(Request $request)
    {
        $this->validate($request,[
            'fName' => 'required|string',
            'lName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'dob' => 'required|date',
            'phone' => 'required|numeric',
            'employee_id' => 'required',
            'patient_id' => 'required',
        ]);
        $user = new User;
        $user->first_name = $request->fName;
        $user->last_name = $request->lName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->dob = $request->dob;
        $user->phone = $request->phone;
        $user->employee_id = $request->employee_id;
        $user->patient_id = $request->patient_id;
        $user->save();

        return $this->generateResponse(true, 'Login Successfully!',[
            'message' => 'Successfully created user!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
