<?php

namespace App\Helpers;

use GuzzleHttp\Cookie\CookieJar as GuzzleHttpCookie;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Routing\Controller as BaseController;
Use \Carbon\Carbon;

class Helper extends BaseController
{

    const WEB_REDIRECT = 515;

    protected $client;

    protected $hangoutWebHook;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generateResponse($status = false, $message = NULL,  $data = array(), $statusCode = 200, $error = array(), $url = '')
    {
        $response["status"] = $status;
        $response["code"] = $statusCode;
        $response["message"] = $message;
        $response["data"] = $data;

        return response()->json($response, $statusCode);
    }

    public function errorLog(\Throwable $e)
    {
        $request = $_REQUEST;
        $error = $e->getMessage() . "\n" . $e->getFile() . " (line : " . $e->getLine() . ")\n" . $e->getTraceAsString() . "\n\nHTTP_USER_AGENT : " . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : " ") . "\n\nRequestData : " . json_encode($request) . "\n\nREQUEST_URI : " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        Log::error($error);
    }

    public function slackLog($message)
    {
        if (env('APP_ENV') !== "production") {
            Log::channel('slack')->critical($message);
        }
    }

    public static function urlCorrector($url)
    {
        $pattern = '!([^:])(//)!';
        return preg_replace($pattern, "$1/", $url);
    }
    /**
     * Current Date
     */
    public static function curretntDate()
    {
        $date = date('Y-m-d');
        return $date;
    }
    /**
     * Current Date and time
     */
    public static function curretntDateTime()
    {
        $date = date('Y-m-d H:m:s');
        return $date;
    }
}
