<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Selection;
use App\Models\State;
use Illuminate\Http\Request;

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

        $data = [
            'certifying_board' => $certifying_board,
            'application_status' => $application_status,
            'relationships' => $relationships,
            'address_type' => $address_type,
            'legal_entity' => $legal_entity,
            'states' => $states,
            'cities' => $cities,
        ];
        
        return $this->generateResponse(true,'Selection list',$data,200);
    }
}
