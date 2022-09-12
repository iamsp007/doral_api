<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input =  $request->all();

        if (isset($input["avatar"]) && !empty($input["avatar"])) {
             $uploadFolder = 'users';
            $image = $input['avatar'];
            $image_uploaded_path = $image->store($uploadFolder, 'public');

            $input['avatar'] = basename($image_uploaded_path);
        }
        $user = Auth::user();
      
        $user->update([
            'avatar' => $input['avatar']
        ]);

        return $this->generateResponse(true,'Your profile Updated Successfully!',$user,200);
    }
}
