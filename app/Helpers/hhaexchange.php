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
