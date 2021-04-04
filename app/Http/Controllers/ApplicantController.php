<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ApplicantReference;
use App\Models\Education;
use App\Models\WorkHistory;
use App\Models\Attestation;
use App\Models\BankAccount;
use App\Models\Security;
use App\Models\UploadDocuments;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use DB;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = false;
        $data = [];
        $message = "Applicants are not available.";
        try {
            $response = Applicant::with(['references', 'state', 'city'])->where('user_id', auth()->user()->id)->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Step one a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepOne(Request $request)
    {
        try {
            $request->validate([
                'applicant_name' => 'required',
                'ssn' => 'required',
                'phone' => 'required',
                'date' => 'required'
            ]);
            $applicant = new Applicant();
            $applicant->user_id = $request->user()->id;
            $applicant->applicant_name = $request->applicant_name;
            $applicant->other_name = $request->other_name;
            $applicant->ssn = $request->ssn;
            $applicant->phone = $request->phone;
            $applicant->home_phone = $request->home_phone;
            $applicant->date = $request->date;
            $applicant->us_citizen = $request->us_citizen;
            $applicant->immigration_id = $request->immigration_id;

            if ($applicant->save()){
                $status = true;
                $message = "Success! Please complete step two.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Step two a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepTwo(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'address_life' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->address_line_1 = $request->address_line_1;
            $applicant->address_line_2 = $request->address_line_2;
            $applicant->city = $request->city;
            $applicant->state = $request->state;
            $applicant->zip = $request->zip;
            $applicant->address_life = $request->address_life;

            if ($applicant->save()){
                $status = true;
                $message = "Success! Please complete step three.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Step three a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepThree(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'reference.*.reference_name' => 'required',
                'reference.*.reference_address' => 'required',
                'reference.*.reference_phone' => 'required',
                'reference.*.reference_relationship' => 'required',
                'bonded' => 'required',
                'refused_bond' => 'required',
                'convicted_crime' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->bonded = $request->bonded;
            $applicant->refused_bond = $request->refused_bond;
            $applicant->convicted_crime = $request->convicted_crime;

            if ($applicant->save()){
                $records = [];
                collect($request->reference)->each(function ($item, $key) use (&$records, &$request) {
                    $record = [
                        'applicant_id' => $request->applicant_id,
                        'reference_name' => $item['reference_name'],
                        'reference_address' => $item['reference_address'],
                        'reference_phone' => $item['reference_phone'],
                        'reference_relationship' => $item['reference_relationship']
                    ];
                    $records[] = $record;
                });
                ApplicantReference::insert($records);
                $status = true;
                $message = "Success! Please complete step four.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Step four a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepFour(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'emergency_name' => 'required',
                'emergency_address' => 'required',
                'emergency_phone' => 'required',
                'emergency_relationship' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->emergency_name = $request->emergency_name;
            $applicant->emergency_address = $request->emergency_address;
            $applicant->emergency_phone = $request->emergency_phone;
            $applicant->emergency_relationship = $request->emergency_relationship;

            if ($applicant->save()){
                $status = true;
                $message = "Successfully completed all steps.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * All step a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allStepTogether(Request $request)
    {
        try {
            $request->validate([
                'applicant_name' => 'required',
                'ssn' => 'required',
                'phone' => 'required',
                'date' => 'required',
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'address_life' => 'required',
                'reference.*.reference_name' => 'required',
                'reference.*.reference_address' => 'required',
                'reference.*.reference_phone' => 'required',
                'reference.*.reference_relationship' => 'required',
                'bonded' => 'required',
                'refused_bond' => 'required',
                'convicted_crime' => 'required',
                'emergency_name' => 'required',
                'emergency_address' => 'required',
                'emergency_phone' => 'required',
                'emergency_relationship' => 'required'
            ]);
            $applicant = new Applicant();

            $applicant->user_id = $request->user()->id;
            $applicant->applicant_name = $request->applicant_name;
            $applicant->other_name = $request->other_name;
            $applicant->ssn = $request->ssn;
            $applicant->phone = $request->phone;
            $applicant->home_phone = $request->home_phone;
            $applicant->date = $request->date;
            $applicant->us_citizen = $request->us_citizen;
            $applicant->immigration_id = $request->immigration_id;

            $applicant->address_line_1 = $request->address_line_1;
            $applicant->address_line_2 = $request->address_line_2;
            $applicant->city = $request->city;
            $applicant->state = $request->state;
            $applicant->zip = $request->zip;
            $applicant->address_life = $request->address_life;

            $applicant->bonded = $request->bonded;
            $applicant->refused_bond = $request->refused_bond;
            $applicant->convicted_crime = $request->convicted_crime;

            $applicant->emergency_name = $request->emergency_name;
            $applicant->emergency_address = $request->emergency_address;
            $applicant->emergency_phone = $request->emergency_phone;
            $applicant->emergency_relationship = $request->emergency_relationship;

            if ($applicant->save()){

                $records = [];
                collect($request->reference)->each(function ($item, $key) use (&$records, &$applicant) {
                    $record = [
                        'applicant_id' => $applicant->id,
                        'reference_name' => $item['reference_name'],
                        'reference_address' => $item['reference_address'],
                        'reference_phone' => $item['reference_phone'],
                        'reference_relationship' => $item['reference_relationship'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $records[] = $record;
                });
                ApplicantReference::insert($records);
                $status = true;
                $message = "Success! details has been saved";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addressLife()
    {
        $status = true;
        $message = "Address Life";
        $data = config('common.address_life');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function relationship()
    {
        $status = true;
        $message = "Relationship";
        $data = config('common.relationship');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ageRangeTreated()
    {
        $status = true;
        $message = "Age Range Treated";
        $data = config('common.age_range_treated');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ccm()
    {
        $status = true;
        $message = "CCM";
        $data = config('common.ccm_reading');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clinicianServices()
    {
        $status = true;
        $message = "Services";
        $data = config('common.clinician_services');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function certifyingBoard()
    {
        $status = true;
        $message = "Certifying Board";
        $data = config('common.certifying_board');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function certifyingBoardStatus()
    {
        $status = true;
        $message = "Certifying Board Status";
        $data = config('common.certifying_board_status');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function workGapReasons()
    {
        $status = true;
        $message = "Work Gap Reasons";
        $data = config('common.work_gap_reasons');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bankAccountTypes()
    {
        $status = true;
        $message = "Bank Account Types";
        $data = config('common.bank_account_types');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendTaxDocumentsTo()
    {
        $status = true;
        $message = "Send Tax Documents To";
        $data = config('common.send_tax_documents_to');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function legalEntities()
    {
        $status = true;
        $message = "Legal Entities";
        $data = config('common.legal_entities');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function securityQuestions()
    {
        $status = true;
        $message = "Security Questions";
        $data = config('common.security_question');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Education a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function education(Request $request)
    {
        try {
            $request->validate([
                'medical_institute_name' => 'required',
                'medical_institute_address' => 'required',
                'medical_institute_city' => 'required',
                'medical_institute_state' => 'required',
                'medical_institute_year_started' => 'required',
                'medical_institute_year_completed' => 'required',
                'residency_institute_name' => 'required',
                'residency_institute_address' => 'required',
                'residency_institute_city' => 'required',
                'residency_institute_state' => 'required',
                'residency_institute_year_started' => 'required',
                'residency_institute_year_completed' => 'required'
            ]);
            $education = new Education();
            $education->user_id = $request->user()->id;

            $education->medical_institute_name = $request->medical_institute_name;
            $education->medical_institute_address = $request->medical_institute_address;
            $education->medical_institute_city = $request->medical_institute_city;
            $education->medical_institute_state = $request->medical_institute_state;
            $education->medical_institute_year_started = $request->medical_institute_year_started;
            $education->medical_institute_year_completed = $request->medical_institute_year_completed;

            $education->residency_institute_name = $request->residency_institute_name;
            $education->residency_institute_address = $request->residency_institute_address;
            $education->residency_institute_city = $request->residency_institute_city;
            $education->residency_institute_state = $request->residency_institute_state;
            $education->residency_institute_year_started = $request->residency_institute_year_started;
            $education->residency_institute_year_completed = $request->residency_institute_year_completed;

            $education->fellowship_institute_name = $request->fellowship_institute_name;
            $education->fellowship_institute_address = $request->fellowship_institute_address;
            $education->fellowship_institute_city = $request->fellowship_institute_city;
            $education->fellowship_institute_state = $request->fellowship_institute_state;
            $education->fellowship_institute_year_started = $request->fellowship_institute_year_started;
            $education->fellowship_institute_year_completed = $request->fellowship_institute_year_completed;

            if ($education->save()){
                $status = true;
                $message = "Successfully stored education data.";
                return $this->generateResponse($status, $message, $education, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEducation()
    {
        $status = false;
        $data = [];
        $message = "Educations are not available.";
        try {
            $response = Education::with(['user', 'medicalInstituteState', 'medicalInstituteCity', 'residencyInstituteState', 'residencyInstituteCity', 'fellowshipInstituteState', 'fellowshipInstituteCity'])->where('user_id', auth()->user()->id)->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWorkHistories()
    {
        $status = false;
        $data = null;
        $message = "Work histories are not available.";
        try {
            $data = WorkHistory::with(['user', 'country', 'state', 'city'])->where('user_id', auth()->user()->id)->get();
            if (!$data) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Work Histories.";
            return $this->generateResponse($status, $message, $data, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Education a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function workHistory(Request $request)
    {
        try {
            $request->validate([
                'work_history.*.company_name' => 'required',
                'work_history.*.position' => 'required',
                'work_history.*.country' => 'required',
                'work_history.*.state' => 'required',
                'work_history.*.city' => 'required',
                'work_history.*.start_date' => 'required',
                'work_history.*.end_date' => 'required',
            ]);
            $records = [];
            collect($request->work_history)->each(function ($item, $key) use (&$records, &$request) {
                $diff = null;
                if ($key > 0) {
                    $x = $key - 1;
                    $earlier = new \DateTime(date('Y-m-d H:i:s', strtotime($request->work_history[$x]['end_date'])));
                    $later = new \DateTime(date('Y-m-d H:i:s', strtotime($request->work_history[$key]['start_date'])));
                    $diff = $later->diff($earlier)->format("%a");
                }
                $record = [
                    'user_id' => $request->user()->id,
                    'company_name' => $item['company_name'],
                    'position' => $item['position'],
                    'country' => $item['country'],
                    'state' => $item['state'],
                    'city' => $item['city'],
                    'start_date' => date('Y-m-d H:i:s', strtotime($item['start_date'])),
                    'end_date' => date('Y-m-d H:i:s', strtotime($item['end_date'])),
                    'work_gap_days' => $diff,
                    'work_gap_reason' => isset($item['work_gap_reason']) ? $item['work_gap_reason'] : null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $records[] = $record;
            });
            WorkHistory::insert($records);
            $status = true;
            $message = "Success! Work history saved.";
            return $this->generateResponse($status, $message, null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAttestations()
    {
        $status = false;
        $data = null;
        $message = "Attestations are not available.";
        try {
            $data = Attestations::with('user')->where('user_id', auth()->user()->id)->get();
            if (!$data) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Work Histories.";
            return $this->generateResponse($status, $message, $data, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Education a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attestation(Request $request)
    {
        try {
            $request->validate([
                'attestation.*.statement' => 'required',
            ]);
            $records = [];
            collect($request->attestation)->each(function ($item, $key) use (&$records, &$request) {
                $record = [
                    'user_id' => $request->user()->id,
                    'answer' => isset($item['answer']) ? $item['answer'] : null,
                    'statement' => $item['statement'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $records[] = $record;
            });
            Attestation::insert($records);
            $status = true;
            $message = "Success! Attestation saved.";
            return $this->generateResponse($status, $message, null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBankAccount()
    {
        $status = false;
        $data = null;
        $message = "Bank account is not available.";
        try {
            $data = BankAccount::with(['user', 'state', 'city'])->where('user_id', auth()->user()->id)->get();
            if (!$data) {
                throw new Exception($message);
            }
            $status = true;
            $message = "Bank Account.";
            return $this->generateResponse($status, $message, $data, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Bank account a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bankAccount(Request $request)
    {
        try {
            $request->validate([
                'account_name' => 'required',
                'account_type' => 'required',
                'routing_number' => 'required',
                'account_number' => 'required',
                'address_line_1' => 'required',
                'address_line_2' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zip' => 'required',
                'send_tax_documents_to' => 'required',
                'legal_entity' => 'required',
                'tax_payer_id_number' => 'required'
            ]);
            $bank = new BankAccount();
            $bank->user_id = $request->user()->id;

            $bank->account_name = $request->account_name;
            $bank->account_type = $request->account_type;
            $bank->routing_number = $request->routing_number;
            $bank->account_number = $request->account_number;
            $bank->address_line_1 = $request->address_line_1;
            $bank->address_line_2 = $request->address_line_2;
            $bank->state = $request->state;
            $bank->city = $request->city;
            $bank->zip = $request->zip;
            $bank->send_tax_documents_to = $request->send_tax_documents_to;
            $bank->legal_entity = $request->legal_entity;
            $bank->tax_payer_id_number = $request->tax_payer_id_number;

            if ($bank->save()){
                $status = true;
                $message = "Successfully stored bank account data.";
                return $this->generateResponse($status, $message, $bank, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSecurities()
    {
        $status = false;
        $data = null;
        $message = "Security data is not available.";
        try {
            $data = Security::with('user')->where('user_id', auth()->user()->id)->get();
            if (!$data) {
                throw new Exception($message);
            }
            $status = true;
            $message = "Security question and answer.";
            return $this->generateResponse($status, $message, $data, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Bank account a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function security(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(),[
                'avatar' => 'required|mimes:jpg,png|max:20000',
                'welcome_message' => 'required',
                'security_question' => 'required',
                'security_answer' => 'required',
                'background_check' => 'required',
                'diclosure_agreement' => 'required',
                'ocg_agreement' => 'required',
                'authorization' => 'required'
            ]);
            if ($validator->fails()){
                return $this->generateResponse(false, $validator->errors()->first(), null, 200);
            }
            $security = new Security();
            $security->user_id = $request->user()->id;

            $security->security_question = $request->security_question;
            $security->security_answer = $request->security_answer;
            $security->background_check = $request->background_check;
            $security->diclosure_agreement = $request->diclosure_agreement;
            $security->ocg_agreement = $request->ocg_agreement;
            $security->authorization = $request->authorization;

            if ($security->save()){
                $uploadFolder = 'users';
                $image = $request->file('avatar');
                $image_uploaded_path = $image->store($uploadFolder, 'public');
                $uploadedImageResponse = [
                    "image_name" => basename($image_uploaded_path),
                    "image_url" => \Storage::disk('public')->url($image_uploaded_path),
                    "mime" => $image->getClientMimeType()
                ];
                $user = $request->user();
                $user->avatar = $uploadedImageResponse['image_name'];
                $user->welcome_message = $request->welcome_message;
                $user->save();
                $status = true;
                $message = "Successfully stored security data.";
                return $this->generateResponse($status, $message, $security, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function documentVerification(Request $request)
    {
        try {
            $keys=array_keys($request->allFiles());
            foreach ($keys as $key) {
                $fileKeys =explode('_',$key);
                $validator = \Validator::make($request->all(),[
                    $key=>'max:10000|mimes:pdf,xls,png,jpg,jpeg'
                ]);

                if ($validator->fails()){
                    return $this->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
                }

                $type=$fileKeys[1];
                if (is_numeric($type)){
                    $file=$request->file($key);
                    $uploadFolder = 'documents/'.auth()->user()->id.'/'.$fileKeys[count($fileKeys)-1];
                    $file_uploaded_path = $file->store($uploadFolder, 'public');
                    $uploadedFileResponse = [
                        "file_name" => basename($file_uploaded_path),
                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
                        "mime" => $file->getClientMimeType()
                    ];
                    $documents = UploadDocuments::where('user_id', auth()->user()->id)
                        ->where('type','=',$type)->first();
                    if ($documents===null){
                        $documents = new UploadDocuments();
                    }
                    $documents->user_id = auth()->user()->id;
                    $documents->file_name = $uploadedFileResponse['file_name'];
                    $documents->type = $type;
                    $documents->save();
                }
            }
            $documents = UploadDocuments::where('user_id', auth()->user()->id)->get();

            return $this->generateResponse(true,'Documents uploaded',$documents,200);
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }

        try {

//            if($request->file('document.*.id_proof')){
//                foreach (array_filter($request->file('document.*.id_proof')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/id_proof';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '1';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.degree_proof')){
//                foreach (array_filter($request->file('document.*.degree_proof')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/degree_proof';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '2';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.medical_report')){
//                foreach (array_filter($request->file('document.*.medical_report')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/medical_report';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '3';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.insurance_report')){
//                foreach (array_filter($request->file('document.*.insurance_report')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/insurance_report';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '4';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.social_security')){
//                foreach (array_filter($request->file('document.*.social_security')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/social_security';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '5';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.professional_referrance')){
//                foreach (array_filter($request->file('document.*.professional_referrance')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/professional_referrance';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '6';
//                    $documents->save();
//                }
//            }
//
//            if($request->file('document.*.nyc_nurse_certificate')){
//                foreach (array_filter($request->file('document.*.nyc_nurse_certificate')) as $file) {
//                    $uploadFolder = 'documents/'.auth()->user()->id.'/nyc_nurse_certificate';
//                    $file_uploaded_path = $file->store($uploadFolder, 'public');
//                    $uploadedFileResponse = [
//                        "file_name" => basename($file_uploaded_path),
//                        "file_url" => \Storage::disk('public')->url($file_uploaded_path),
//                        "mime" => $file->getClientMimeType()
//                    ];
//                    $documents = new UploadDocuments();
//                    $documents->user_id = auth()->user()->id;
//                    $documents->file_name = $uploadedFileResponse['file_name'];
//                    $documents->type = '7';
//                    $documents->save();
//                }
//            }

            $documents = UploadDocuments::where('user_id', auth()->user()->id)->get();

            return $this->generateResponse(true,'Documents uploaded',$documents,200);
        }catch (\Exception $exception){

            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function getDocuments()
    {
        try {
            $documents = UploadDocuments::where('user_id', auth()->user()->id)->get();
            return $this->generateResponse(true, 'All Documents', $documents, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function removeDocument(Request $request)
    {
        try {
            $documents = UploadDocuments::where([
                'id' => $request->id,
                'user_id' => auth()->user()->id
            ])->delete();
            $documents = UploadDocuments::where('user_id', auth()->user()->id)->get();
            return $this->generateResponse(true, 'Document removed', $documents, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function getClinicianList($status_id = 0)
    {
        $status = false;
        $data = [];
        $message = "Applicants are not available.";
        try {
            $response = User::with(['applicant.references', 'applicant.state', 'applicant.city', 'education.medicalInstituteState', 'education.medicalInstituteCity', 'education.residencyInstituteState', 'education.residencyInstituteCity', 'education.fellowshipInstituteState', 'education.fellowshipInstituteCity', 'professional.medicareState', 'professional.medicaidState', 'professional.ageRanges', 'professional.stateLicenses.licenseState', 'professional.boardCertificates', 'attestation', 'background.country', 'background.state', 'background.city', 'deposit.state', 'deposit.city', 'documents'])->where('status','=',$status_id)->whereHas('roles', function($q) {
                    $q->where('name','=', 'clinician');
                })->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    public function getClinicianDetail($userId)
    {
        $status = false;
        $data = [];
        $message = "Applicant detail not available.";
        try {
            $response = User::with(['applicant.references', 'applicant.state', 'applicant.city', 'education.medicalInstituteState', 'education.medicalInstituteCity', 'education.residencyInstituteState', 'education.residencyInstituteCity', 'education.fellowshipInstituteState', 'education.fellowshipInstituteCity', 'professional.medicareState', 'professional.medicaidState', 'professional.ageRanges', 'professional.stateLicenses.licenseState', 'professional.boardCertificates', 'attestation', 'background.country', 'background.state', 'background.city', 'deposit.state', 'deposit.city', 'documents'])->findOrFail($userId);
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    public function getClinicianData(Request $request) {
        $requestData = $request->all();
        $status = false;
        $data = [];
        $message = "Applicants are not available.";
        try {
            $response = User::with(['applicant.references', 'applicant.state', 'applicant.city', 'education.medicalInstituteState', 'education.medicalInstituteCity', 'education.residencyInstituteState', 'education.residencyInstituteCity', 'education.fellowshipInstituteState', 'education.fellowshipInstituteCity', 'professional.medicareState', 'professional.medicaidState', 'professional.ageRanges', 'professional.stateLicenses.licenseState', 'professional.boardCertificates', 'attestation', 'background.country', 'background.state', 'background.city', 'deposit.state', 'deposit.city', 'documents'])->where('status','=',$requestData['status'])->whereHas('roles', function($q) {
                    $q->where('name','=', 'clinician');
                })
                ->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%'.$requestData['searchTerm'].'%')
                ->get();

            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    public function storeApplicantDetail(Request $request)
    {
        $applicant = Applicant::where('user_id', $request->user()->id)->first();

        if (!$applicant) {
            $applicant = new Applicant();
            $applicant->user_id = $request->user()->id;
        }

        $key = $request->key;

        $applicant->$key = $request->$key;
        $applicant->phone = $request->phone ?? null;
        $applicant->home_phone = $request->home_phone ?? null;
        $applicant->save();

        return $this->generateResponse(true, $key.' detail added.', $applicant, 200);
    }

    public function getApplicantDetails()
    {
        $status = false;
        $data = [];
        $message = "Applicant is not available.";
        try {
            $response = Applicant::with('documents')->where('user_id', auth()->user()->id)->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "Applicant details.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }
}
