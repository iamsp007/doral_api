<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Referral;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use URL;
use App\Mail\ReferralAcceptedMail;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        $status="0";
        if ($type==="2"){
            $status="1";
        }elseif ($type==="3"){
            $status="3";
        }
        $companies = Company::where('status','=',$status)
            ->whereHas('roles',function ($q){
                $q->where('name','=','referral');
            })
            ->get();
        $data = [
            'companies' => $companies
        ];
        return $this->generateResponse(true, 'Companies listing!', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];
            // check name and email for Company
            $CompanyMatch = ['name' => $Company['name'], 'email' => $Company['email']];
            $CompanyData = Company::where($CompanyMatch)->first();
            if ($CompanyData) {
                throw new Exception("Company already in available");
            }
            $data = array(
                'name' => $Company['name'],
                'referal_id' => $Company['referral_id'],
                'email' => $Company['email'],
                'status' => 'Pending',
                'password' => Hash::make('test123')
            );
            $id = Company::insert($data);
            if ($id) {
                $status = true;
                $message = 'Company store properly';
            }
            $data = [
                'Company_id' => $id
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];
            // check name and email for Company
            $CompanyMatch = ['email' => $Company['email']];
            $CompanyData = Company::where($CompanyMatch)->first();
            if ($CompanyData) {
                //Check Password eith existing password
                $chkPassword = Hash::check($Company['password'], $CompanyData->password);
                if (!$chkPassword) {
                    throw new Exception("Company email / password not match");
                }
                $CompanyMatch['status'] = 'Active';
                $CompanyData = Company::where($CompanyMatch)->first();
                if ($CompanyData) {
                    $status = true;
                    $message = "Welcome in Doral";
                } else {
                    throw new Exception("Company not active now!");
                }
            } else {
                throw new Exception("Company not available");
            }
            $data = [
                'Company' => $CompanyData
            ];
            $status = true;
            $message = $message;
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }

        $response = [
            'status' => $status,
            'message' => $message
        ];

        return response()->json($response, 200);
    }

    public function loginToken(Request $request)
    {
        /*$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);*/

        //Post data
        $request = json_decode($request->getContent(), true);
        $Company = $request['data'];
        // check name and email for Company
        $CompanyMatch = ['email' => $Company['email'], 'password' => $Company['password']];
        if (!Auth::guard('Company')->attempt($CompanyMatch))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        dd("Auth Match");
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addMinute(1);
        $token->save();
        $data = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return $this->generateResponse(true, 'Login Successfully!', $data);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $company = Company::with('referral')->find($id);
        if ($company){
            return $this->generateResponse(true,'Company Information',$company,200);
        }
        return $this->generateResponse(false,'No Company Found',null,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $Company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $Company)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $Company = Company::find($Company);
            if (!$Company) {
                throw new Exception("Company information is not found");
            }
            $data = [
                'Company' => $Company
            ];
            $status = true;
            $message = "Compnay information";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $Company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request = json_decode($request->getContent(), true);
       
        
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            //$Company = Company::findOrFail($request['id']);
           // $Company = Company::update($request->all());
            $Company = Company::where('id', $request['id'])
                ->update($request);
            $data = [
                'Company' => $request
            ];
            $status = true;
            $message = "Compnay updated Succesfully";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $Company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $Company)
    {
        $Company = Company::findOrFail($Company);
        $Company->delete();

        return response()->json(null, 204);
    }


    /**
     * update Company profile save
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProfile(Request $request)
    {
        $status = false;
        $message = 'Something wrong';
        $data = array();
        $url = '';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];
            $url = request()->getHttpHost() . "/api/Company/resetpassword?email=" . urlencode($Company['email']);

            // Need to understand all validation
            // Check Phone
            if (isset($Company['phone']) && !empty($Company['phone'])) {
            } else {
                throw new Exception("phone required");
            }

            $data = array(
                'name' => isset($Company['name']) ? $Company['name'] : '',
                'address1' => isset($Company['address1']) ? $Company['address1'] : '',
                'address2' => isset($Company['address2']) ? $Company['address2'] : '',
                'zip' => isset($Company['zip']) ? $Company['zip'] : '',
                'email' => $Company['email'],
                'phone' => isset($Company['phone']) ? $Company['phone'] : '',
                'npi' => isset($Company['npi']) ? $Company['npi'] : '',
                'np_id' => isset($Company['np_id']) ? $Company['np_id'] : 1,
                'referal_id' => $Company['referal_id'],
                'password' => Hash::make($Company['password']),
                'verification_comment' => isset($Company['verification_comment']) ? $Company['verification_comment'] : ''
            );
            $updateRecord = Company::where('id', $Company['Company_id'])
                ->update($data);
            if ($updateRecord) {
                // Send Email with Email Template and url
                $url = request()->getHttpHost() . "/api/Company/resetpassword?email=" . urlencode($Company['email']);
                $status = true;
                $message = 'Profile Updated successfully';
            }
            $data = [
                'Company_id' => $updateRecord
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Send email for reset password process
     *
     * @param  \App\Models\Company  $Company
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $status = 0;
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];

            // Email address
            $CompanyMatch = ['email' => $Company['email']];
            $CompanyData = Company::where($CompanyMatch)->first();
            if (!$CompanyData) {
                throw new Exception("Company not available");
            }

            // Send Email with resetpassword link
            $status = 1;
            $message = "Reset password link sent your email address";
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }

        $response = [
            'status' => $status,
            'message' => $message
        ];

        return response()->json($response, 201);
    }

    /**
     * Confirm Password with reset password process
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmPassword(Request $request)
    {
        $status = 0;
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];

            // Email address
            $CompanyMatch = ['email' => $Company['email']];
            $CompanyData = Company::where($CompanyMatch)->first();
            if (!$CompanyData) {
                throw new Exception("Company not available");
            }

            if ($Company['password'] != $Company['confirm_password']) {
                throw new Exception("Password not match");
            }
            $password = md5($Company['password']);
            $data = array(
                'password' => $password
            );
            $updateRecord = Company::where('email', $Company['email'])
                ->update($data);
            if ($updateRecord) {
                $status = 1;
                $message = 'Password Reset';
            }
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }

        $response = [
            'status' => $status,
            'message' => $message
        ];

        return response()->json($response, 201);
    }

    /**
     * update Company status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        //echo "1";
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $Company = $request['data'];


            $data = array(
                'status' => $Company['status']
            );
            
            $updateRecord = Company::where('id', $Company['Company_id'])
                ->update($data);
            if ($updateRecord) {
                $status = true;
                $message = 'Status updated';
            }
            $data = [
                'Company_id' => $Company['Company_id'],
                'Company_status' => $Company['status']
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            //dd($e);
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}
