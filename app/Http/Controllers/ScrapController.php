<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScrapController extends Controller
{
    public function npiScrap(Request $request)
    {
        $input = $request->all();

        $curl = curl_init();
        $number = '';
        if (isset($input['number'])) {
            $number = $input['number'];
        }

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://npiregistry.cms.hhs.gov/api/?number=' . $number . '&version=2.1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: TS017b4e40=01cffab1d389839456a31cd734ae20094f282cbd3689efd9c00c12c707c55fad15d5044a097d503c4fb63d07c90416640179ec581b'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}
