<?php

use App\Helpers\Helper;
use App\Jobs\SendEmailJob;
use App\Models\Demographic;
use App\Models\PatientEmergencyContact;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;

if (!function_exists('curlCall')) {
        function curlCall($data, $method)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('patientDetailAuthentication.AppUrl'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
            $xml = new \SimpleXMLElement($response);
            return json_decode(json_encode((array)$xml), TRUE);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    if (!function_exists('searchVisits')) {
        
        function searchVisits($input)
        {
            $patientID = '';
            if (isset($input['patientId'])) {
                $patientID = '<PatientID>' . $input['patientId'] . '</PatientID>';
            }
            $data = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><SearchVisits xmlns="https://www.hhaexchange.com/apis/hhaws.integration">' . authentication(). '<SearchFilters><StartDate>' . $input['from_date'] .'</StartDate><EndDate>' . $input['to_date']  . '</EndDate>'.$patientID.'</SearchFilters></SearchVisits></SOAP-ENV:Body></SOAP-ENV:Envelope>';
        
            $method = 'POST';
            return curlCall($data, $method);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    if (!function_exists('getScheduleInfo')) {
        function getScheduleInfo($visitorID)
        {
            $data = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><GetScheduleInfo xmlns="https://www.hhaexchange.com/apis/hhaws.integration">' . authentication(). '<ScheduleInfo><ID>' . $visitorID . '</ID></ScheduleInfo></GetScheduleInfo></SOAP-ENV:Body></SOAP-ENV:Envelope>';

            $method = 'POST';

            return curlCall($data, $method);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    if (!function_exists('getCaregiverDemographics')) {
        function getCaregiverDemographics($cargiver_id)
        {
            $data = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><GetCaregiverDemographics xmlns="https://www.hhaexchange.com/apis/hhaws.integration">' . authentication(). '<CaregiverInfo><ID>' . $cargiver_id . '</ID></CaregiverInfo></GetCaregiverDemographics></SOAP-ENV:Body></SOAP-ENV:Envelope>';

            $method = 'POST';
            return curlCall($data, $method);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    if (!function_exists('authentication')) {
        
        function authentication($input = '')
        {
            if (isset($input['AppName']) && isset($input['AppSecret']) && isset($input['AppKey'])) {
                $appName = $input['AppName'];
                $appSecret = $input['AppSecret'];
                $appKey = $input['AppKey'];
            } else {
                $appName = config('patientDetailAuthentication.AppName');
                $appSecret = config('patientDetailAuthentication.AppSecret');
                $appKey = config('patientDetailAuthentication.AppKey');
            }

            return '<Authentication><AppName>' . $appName . '</AppName><AppSecret>' . $appSecret . '</AppSecret><AppKey>' . $appKey. '</AppKey></Authentication>';
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    if (!function_exists('storeUser')) {
        function storeUser($demographics, $doral_id)
        {      
            $user = new User();
            
            if ($demographics['NotificationPreferences'] && isset($demographics['NotificationPreferences']['Email'])) {
                $email = $demographics['NotificationPreferences']['Email'];
                    
                $userDuplicateEmail = User::where('email', $email)->first();
        
                if (empty($userDuplicateEmail)) {
                    $user->email = $email;
                } 
            }
            
            $phone_number = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
            $tele_phone = $demographics['Address']['Phone2'] ? $demographics['Address']['Phone2']: '';

            if ($phone_number != '') {
            
                // $userDuplicatePhone = User::where('phone', setPhone($phone_number))->first();
            
                // if (empty($userDuplicatePhone)) {
                //     $user->phone = setPhone($phone_number);
                //     $user->phone_verified_at = now();
                //     $status = '0';
                // } else {
                //     $status = '4';
                // }
                $status = '0';
            } else {
                $status = '4';
            }
        
            $first_name = ($demographics['FirstName']) ? $demographics['FirstName'] : '';
            $password = str_replace("-", "@",$doral_id);
                
            $user->first_name = $first_name;
            $user->last_name = ($demographics['LastName']) ? $demographics['LastName'] : '';
            $user->password = setPassword($password);
            $user->phone = setPhone($phone_number);
            $user->phone_verified_at = now();
            $user->status = $status;
            $user->gender = setGender($demographics['Gender']);
            
            $user->dob = dateFormat($demographics['BirthDate']);

            if ($tele_phone != '') {
                $user->tele_phone = setPhone($tele_phone);
            }
        
            $user->save();
            $user->assignRole('patient')->syncPermissions(Permission::all());
            $url = route('partnerEmailVerified', base64_encode($user->id));
            $details = [
                'name' => $user->first_name,
                'href' => $url,
            ];

            if (isset($user->email)) {
                SendEmailJob::dispatch($user->email,$details,'WelcomeEmail');
            }
            return $user->id;
        }
    }

    if (!function_exists('storeDemographic')) {
        function storeDemographic($demographics, $user_id, $company_id, $doral_id, $action = '')
        {
            $demographic = new Demographic();
            
            $demographic->doral_id = $doral_id;
            $demographic->user_id = $user_id;
            $demographic->company_id = $company_id;
        
            if ($action == 'caregiver-check') {
                $demographic->flag = '2';  
            }
            $demographic->service_id = config('constant.OccupationalHealth');
            $demographic->patient_id = $demographics['ID'] ? $demographics['ID'] : '';
            $demographic->ethnicity = $demographics['Ethnicity'] && $demographics['Ethnicity']['Name'] ? $demographics['Ethnicity']['Name'] : '';

            $language = $demographics['Language1'] ? $demographics['Language1'] : '';

            $addressData = [];
            if ($demographics['Address']) {
                $address = $demographics['Address'];
                $zip = '';
                if(isset($address['Zip5']) && $address['Zip5'] != ''){
                    $zip = $address['Zip5'];
                } else if(isset($address['Zip4']) && $address['Zip4'] != ''){
                    $zip = $address['Zip4'];
                }

                $addressData = [
                    'address1' => $address['Street1'] ? $address['Street1'] : '',
                    'address2' => $address['Street2'] ? $address['Street2'] : '',
                    'city' => $address['City'] ? $address['City'] : '',
                    'state' => $address['State'] ? $address['State'] : '',
                    'zip_code' => $zip,
                ];
            }

            $notificationPreferencesData = [];
            if ($demographics['NotificationPreferences']) {

                $notificationPreferences = $demographics['NotificationPreferences'];
                $notificationPreferencesData = [
                    'method' => ($notificationPreferences['Method'] && $notificationPreferences['Method']['Name']) ? $notificationPreferences['Method']['Name'] : '',
                    'email' => $notificationPreferences['Email'] ? $notificationPreferences['Email'] : '',
                    'mobile_or_SMS' => $notificationPreferences['MobileOrSMS'] ? $notificationPreferences['MobileOrSMS'] : '',
                    'voice_message' => $notificationPreferences['VoiceMessage'] ? $notificationPreferences['VoiceMessage'] : '',
                ];
            }
            $demographic->country_of_birth = $demographics['CountryOfBirth'] ? $demographics['CountryOfBirth'] : '';
            $demographic->employee_type =  $demographics['EmployeeType'] ? $demographics['EmployeeType'] : '';
            $demographic->marital_status = ($demographics['MaritalStatus'] && $demographics['MaritalStatus']['Name']) ? $demographics['MaritalStatus']['Name'] : '';
                    
            $demographic->notification_preferences = $notificationPreferencesData;
                
            $demographic->ssn = setSsn($demographics['SSN'] ? $demographics['SSN'] : '');
            $demographic->address = $addressData;
            $demographic->status = 'Active';
            $demographic->language = $language;
            $demographic->type = '1';

            $demographic->save();

            getAddressLatlngAttribute($addressData, $user_id);
        }
    }

    if (!function_exists('storeEmergencyContact')) {
        function storeEmergencyContact($demographics, $user_id)
        {
            if ($demographics['EmergencyContacts']['EmergencyContact']) {
                foreach ($demographics['EmergencyContacts']['EmergencyContact'] as $emergencyContact) {
                    if($emergencyContact['Name']) {
                        $relationship = '';
                        if ($emergencyContact['Relationship'] && $emergencyContact['Relationship']['Name']) {
                            $relationship = $emergencyContact['Relationship']['Name'];
                        }
                    
                        $patientEmergencyContact = new PatientEmergencyContact();
        
                        $patientEmergencyContact->user_id = $user_id;
                        $patientEmergencyContact->name = $emergencyContact['Name'];
                        $patientEmergencyContact->relation = $relationship;
                    
                        $patientEmergencyContact->phone1 = setPhone($emergencyContact['Phone1'] ? $emergencyContact['Phone1'] : '');
                        $patientEmergencyContact->phone2 = setPhone($emergencyContact['Phone2'] ? $emergencyContact['Phone2'] : '');
                        
                        if ($emergencyContact['Address']) {
                            $addressData = [
                                'address1' => ($emergencyContact['Address']) ? $emergencyContact['Address'] : ''
                            ];
        
                            $patientEmergencyContact->address = $addressData;
                        }
                        $patientEmergencyContact->save();
                    }
                }
            }
        }
    }

     /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    if (!function_exists('getAddressLatlngAttribute')) {
        function getAddressLatlngAttribute($addressData, $user_id)
        {
            $address='';
            if ($addressData['address1']){
                $address.= $addressData['address1'];
            }
            if ($addressData['city']){
                $address.=', '.$addressData['city'];
            }
            if ($addressData['state']){
                $address.=', '.$addressData['state'];
            }
        
            if ($addressData['zip_code']){
                $address.=', '.$addressData['zip_code'];
            }

            if ($address){
                $helper = new Helper();
                $response = $helper->getLatLngFromAddress($address);
                if ($response->status === "OK"){
                    $latlong =  $response->results[0]->geometry->location;

                    User::find($user_id)->update([
                        'latitude' => $latlong->lat,
                        'longitude' => $latlong->lng,
                    ]);
                }
            }
        }
    }