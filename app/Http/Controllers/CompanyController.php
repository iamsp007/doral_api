<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;
use App\Models\referral;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
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
            $companies = company::all()->toArray();
            if (!$companies) {
                throw new Exception("No Companies are registered");
            }
            $data = [
                'companies' => $companies
            ];
            return $this->generateResponse(true, 'Companies listing!', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
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
            $company = $request['data'];
            // check name and email for company
            $companyMatch = ['name' => $company['name'], 'email' => $company['email']];
            $companyData = company::where($companyMatch)->first();
            if ($companyData) {
                throw new Exception("Company already in available");
            }
            $data = array(
                'name' => $company['name'],
                'referal_id' => $company['refferal_id'],
                'email' => $company['email'],
                'status' => 'Pending',
            );
            $id = company::insert($data);
            if ($id) {
                $status = true;
                $message = 'Company store properly';
            }
            $data = [
                'company_id' => $id
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
            $company = $request['data'];
            // check name and email for company
            $companyMatch = ['email' => $company['email']];
            $companyData = company::where($companyMatch)->first();
            if ($companyData) {
                //Check Password eith existing password
                $chkPassword = Hash::check($company['password'], $companyData->password);
                if(!$chkPassword){
                    throw new Exception("Company email / password not match");
                }
                $companyMatch['status'] = 'Active';
                $companyData = company::where($companyMatch)->first();
                if ($companyData) {
                    $status = true;
                    $message = "Welcome in Doral";
                } else {
                    throw new Exception("Company not active now!");
                }
            } else {
                throw new Exception("Company not available");
            }
            $data = [
                'company' => $companyData
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
        $company = $request['data'];
        // check name and email for company
        $companyMatch = ['email' => $company['email'], 'password' => $company['password']];        
        if (!Auth::guard('company')->attempt($companyMatch))
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
        $data=[
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return $this->generateResponse(true, 'Login Successfully!',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(company $company)
    {
        return company::find($company);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(company $company)
    {
        $company = company::findOrFail($company);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, company $company)
    {
        $company = company::findOrFail($company);
        $company->update($request->all());

        return response()->json($company, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(company $company)
    {
        $company = company::findOrFail($company);
        $company->delete();

        return response()->json(null, 204);
    }


    /**
     * update company profile save
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
            $company = $request['data'];
            $url = request()->getHttpHost() . "/api/company/resetpassword?email=" . urlencode($company['email']);

            // Need to understand all validation
            // Check Phone
            if (isset($company['phone']) && !empty($company['phone'])) {
            } else {
                throw new Exception("phone required");
            }

            $data = array(
                'name' => isset($company['name']) ? $company['name'] : '',
                'address1' => isset($company['address1']) ? $company['address1'] : '',
                'address2' => isset($company['address2']) ? $company['address2'] : '',
                'zip' => isset($company['zip']) ? $company['zip'] : '',
                'email' => $company['email'],
                'phone' => isset($company['phone']) ? $company['phone'] : '',
                'npi' => isset($company['npi']) ? $company['npi'] : '',
                'np_id' => isset($company['np_id']) ? $company['np_id'] : 1,
                'referal_id' => $company['referal_id'],
                'password' => Hash::make($company['password']),
                'verification_comment' => isset($company['verification_comment']) ? $company['verification_comment'] : ''
            );
            $updateRecord = company::where('id', $company['company_id'])
                ->update($data);
            if ($updateRecord) {
                // Send Email with Email Template and url
                $url = request()->getHttpHost() . "/api/company/resetpassword?email=" . urlencode($company['email']);
                $status = true;
                $message = 'Profile Updated successfully';
            }
            $data = [
                'company_id' => $updateRecord
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
     * @param  \App\Models\company  $company
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $status = 0;
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $company = $request['data'];

            // Email address
            $companyMatch = ['email' => $company['email']];
            $companyData = company::where($companyMatch)->first();
            if (!$companyData) {
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
            $company = $request['data'];

            // Email address
            $companyMatch = ['email' => $company['email']];
            $companyData = company::where($companyMatch)->first();
            if (!$companyData) {
                throw new Exception("Company not available");
            }

            if ($company['password'] != $company['confirm_password']) {
                throw new Exception("Password not match");
            }
            $password = md5($company['password']);
            $data = array(
                'password' => $password
            );
            $updateRecord = company::where('email', $company['email'])
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
     * update company status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $company = $request['data'];

            // Check status
            /*$checkStatus = ["Approve", "Reject", "Pending", "Active"];
            if (!in_array($company['status'], $checkStatus)) {
                throw new Exception("Something wrong in Status");
            }*/
            $data = array(
                'status' => $company['status']
            );
            $updateRecord = company::where('id', $company['company_id'])
                ->update($data);
            if ($updateRecord) {
                $status = true;
                $message = 'Status updated';
            }
            $data = [
                'company_id' => $updateRecord
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}
