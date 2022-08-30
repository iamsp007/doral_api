<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $arrayName = [
            'terms-and-condition' => 'https://app.doralhealthconnect.com/terms-and-conditions',
            'privacy-and-policy' => 'https://app.doralhealthconnect.com/privacy-and-policy'
        ];

        return $this->generateResponse(true,'Pages list',$arrayName,200);
    }
}
