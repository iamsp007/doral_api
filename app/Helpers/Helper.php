<?php

namespace App\Helpers;

use GuzzleHttp\Cookie\CookieJar as GuzzleHttpCookie;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class Helper
{

    const WEB_REDIRECT = 515;

    protected $client;

    protected $hangoutWebHook;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generateResponse($status = false, $message = NULL, $statusCode = 200, $data = array(), $error = array(), $url = '')
    {
        $response["status"] = $status;
        $response["message"] = $message;
        $response["data"] = $data;
        $response["error"] = $error;

        if (self::WEB_REDIRECT === $statusCode) {
            return redirect($url);
        }
        return response()->json($response, $statusCode);
    }

    public function ggPaymentNotify($paymentResponseData)
    {
        try {
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';
            $options = array('headers' => $headers, 'body' => json_encode($paymentResponseData));

            $paymentGG = $this->client->request("POST", config('utilities.gg_payment_notify_url'), $options);

            return $paymentGG;
        } catch (ClientException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getPaymentGenerateBill($postData)
    {
        try {
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';
            $options = array('headers' => $headers, 'body' => json_encode($postData));

            $billPayment = $this->client->request("POST", config('utilities.payment_generate_bill'), $options);
            $response = $billPayment->getBody()->getContents();

            return json_decode($response, true);
        } catch (ClientException $e) {
            throw new \Exception($e->getMessage());
        }
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
}
