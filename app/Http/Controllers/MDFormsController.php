<?php

namespace App\Http\Controllers;

use App\Models\MDForms;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MDFormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        try {
            $mdforms = MDForms::all()->toArray();
            if (!$mdforms) {
                throw new Exception("No mdforms are registered");
            }
            $data = [
                'mdforms' => $mdforms
            ];
            return $this->generateResponse(true, 'mdforms listing!', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
    }
}
