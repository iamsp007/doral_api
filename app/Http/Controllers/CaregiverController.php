<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\employee as ModelsEmployee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CaregiverController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function actionStore(Request $request)
    {
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            
            // User Add
            $user = new User;
            $user->first_name = $employee['first_name'];
            $user->last_name = $employee['last_name'];
            $user->email = $employee['email'];
            $user->password = Hash::make('test123');
            $user->dob = $employee['dob'];
            $user->phone = $employee['phone'];
            $user->type = 'admin';
            $user->save();
            $userId = $user->id;

            if ($record->id) {
                $status = true;
                $message = 'Employee store properly';
            }
            $data = [
                'Employee_id' => $record->id
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    
}
