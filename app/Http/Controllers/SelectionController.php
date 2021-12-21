<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankRouting;
use App\Models\City;
use App\Models\Designation;
use App\Models\Selection;
use App\Models\State;
use App\Models\Malpractice;
use App\Models\PhysicianSpeciality;

class SelectionController extends Controller
{
    public function index(Selection $selection)
    {
        $certifying_board = $selection->where('name','Certifying Board')->orderBy('name','asc')->get();

        $application_status = $selection->where('name','Application Status')->orderBy('name','asc')->get();
        
        $relationships = $selection->where('name','Relationship')->orderBy('name','asc')->get();

        $address_type = $selection->where('name','Address Type')->orderBy('name','asc')->get();

        $legal_entity = $selection->where('name','Legal entity')->orderBy('name','asc')->get();

        $states = State::orderBy('state','asc')->get();

        $cities = City::orderBy('city','asc')->get();
        
        $reason_for_leaving = $selection->where('name','Reason For Leaving')->orderBy('name','asc')->get();
        
        $designation = Designation::where('role_id',4)->get();

        $banks = Bank::where('status','1')->with('bankRouting')->orderBy('name','asc')->get();
     
	    $state_license_category = $selection->where('name','State License Category')->orderBy('name','asc')->get();
        $malpractices = Malpractice::get();

        $physicianSpeciality = PhysicianSpeciality::get();
        $data = [
            'certifying_board' => $certifying_board,
            'application_status' => $application_status,
            'relationships' => $relationships,
            'address_type' => $address_type,
            'legal_entity' => $legal_entity,
            'states' => $states,
            'cities' => $cities,
            'designation' => $designation,
            'reason_for_leaving' => $reason_for_leaving,
            'state_license_category' => $state_license_category,
            'banks' => $banks,
            'malpractices' => $malpractices,
            'physicianSpeciality' => $physicianSpeciality,
        ];
        
        return $this->generateResponse(true,'Selection list',$data,200);
    }
}