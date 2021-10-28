<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        if (isset($request['phone']) && !empty($request['phone'])) {
            $where = ['phone' => $request['phone']];
            $field = 'phone';
        } else if(isset($request['email']) && !empty($request['email'])) {
            $where = ['email' => $request['email']];
            $field = 'email';
        }
        
        $isRegistered = false;
        $message = '';
        if(User::where($where)->first()) {
            $isRegistered = true;
            $message = 'This ' . $field . ' number already registered!';
        }

        return $this->generateResponse(true, $message, $isRegistered);
    }
}
