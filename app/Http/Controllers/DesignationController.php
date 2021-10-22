<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Exception;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = array();
        try {
            
            $designation = Designation::where([['role_id', '=',4],['name','!=','Field Visitor']])->get();
          
            if (!$designation) {
                throw new Exception("No designation are registered");
            }
            $data = [
                'designation' => $designation
            ];
            return $this->generateResponse(true, 'designation listing!', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
    }
}
