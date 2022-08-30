<?php

namespace App\Http\Controllers;

use App\Models\Certificate;

use App\Models\StateLicense;
use App\Models\BoardCertificate;
use Illuminate\Http\Request;
use Exception;

class CertificateController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()
    // {
    //     $status = false;
    //     $data = [];
    //     $message = "Certificates are not available.";
    //     try {
    //         $response = Certificate::with(['user', 'ageRanges', 'stateLicenses', 'boardCertificates'])->where('user_id', auth()->user()->id)->get();
    //         if (!$response) {
    //             throw new Exception($message);
    //         }
    //         $status = true;
    //         $message = "All Certificates.";
    //         return $this->generateResponse($status, $message, $response, 200);
    //     } catch (\Exception $e) {
    //         $status = false;
    //         $message = $e->getMessage()." ".$e->getLine();
    //         return $this->generateResponse($status, $message, $data, 200);
    //     }
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'federal_dea_id' => 'required'
    //         ]);
    //         $certificate = new Certificate();
    //         $certificate->user_id = $request->user()->id;
    //         $certificate->medicare_enrolled = $request->medicare_enrolled;
    //         $certificate->medicare_state = $request->medicare_state;
    //         $certificate->medicare_number = $request->medicare_number;
    //         $certificate->medicaid_enrolled = $request->medicaid_enrolled;
    //         $certificate->medicaid_state = $request->medicaid_state;
    //         $certificate->medicaid_number = $request->medicaid_number;
    //         $certificate->federal_dea_id = $request->federal_dea_id;

    //         if ($certificate->save()){
    //             $this->ageRanges($request, $certificate);
    //             $this->stateLicenses($request, $certificate);
    //             $this->boardCertificates($request, $certificate);
    //             $status = true;
    //             $message = "Certificate details saved.";
    //             return $this->generateResponse($status, $message, $certificate, 200);
    //         }
    //         return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
    //     } catch (\Exception $e) {
    //         $status = false;
    //         $message = $e->getMessage();
    //         return $this->generateResponse($status, $message, []);
    //     }
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function ageRanges($request, $certificate)
    // {
    //     $records = [];
    //     collect($request->age_ranges)->each(function ($item, $key) use (&$records, &$request, &$certificate) {
    //         $record = [
    //             'certificate_id' => $certificate->id,
    //             'age_range_treated' => $item['age_range_treated']
    //         ];
    //         $records[] = $record;
    //     });
    //     AgeRange::insert($records);
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function stateLicenses($request, $certificate)
    // {
    //     $records = [];
    //     collect($request->state_licenses)->each(function ($item, $key) use (&$records, &$request, &$certificate) {
    //         $record = [
    //             'certificate_id' => $certificate->id,
    //             'license_state' => $item['license_state'],
    //             'license_number' => $item['license_number']
    //         ];
    //         $records[] = $record;
    //     });
    //     StateLicense::insert($records);
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function boardCertificates($request, $certificate)
    // {
    //     $records = [];
    //     collect($request->board_certificates)->each(function ($item, $key) use (&$records, &$request, &$certificate) {
    //         $record = [
    //             'certificate_id' => $certificate->id,
    //             'certifying_board' => $item['certifying_board'],
    //             'status' => $item['status']
    //         ];
    //         $records[] = $record;
    //     });
    //     BoardCertificate::insert($records);
    // }
}
