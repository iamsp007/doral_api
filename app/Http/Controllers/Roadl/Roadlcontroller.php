<?php

namespace App\Http\Controllers\Roadl;

use App\Http\Controllers\Controller;
use App\Models\PatientRequest;
use App\Models\RoadlRequestTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Roadlcontroller extends Controller
{
    public function index(Request $request)
    {
        $available = PatientRequest::whereHas('roadlRequestTo', function($q) {
            $q->where('status', '1');
        })->with(['detail', 'patient','roadlRequestTo'])->whereNotNull('parent_id')->where(['status' => '1', 'type_id' => $request->role_id])->groupBy('parent_id')->orderBy('id','desc')->get();

        $active = PatientRequest::with(['detail','patient','roadlRequestTo'])->whereNotNull('parent_id')->where(['status' => '2', 'clincial_id' => Auth::user()->id])->groupBy('parent_id')->orderBy('id','desc')->get();

        $response = [
            'active' => $active,
            'available' => $available
        ];

        return $this->generateResponse(true, 'Road information', $response);
    }

    public function hideRoadlRequest(Request $request)
    {
        $patientRequest = RoadlRequestTo::where('id', $request['roadl_request_to_id'])->update(['status' => '0']);

        return $this->generateResponse(true, 'Update roadl request status', $patientRequest);
    }
}

