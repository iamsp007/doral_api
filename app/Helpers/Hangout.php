<?php
namespace GlobalGarner\Utils\Helpers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class Hangout {

    protected $hangoutWebHook;

    public function __construct()
    {
        $this->client = new Client();
    }


    public function setHangoutWebHook( $webHook )
    {
        if (filter_var($webHook, FILTER_VALIDATE_URL)) {

            $this->hangoutWebHook =  $webHook;

            return $this;

        }
        else {
            throw new \Exception('Invalid webhook URL');
        }
    }

    public function getHangoutWebHook()
    {
        if( $this->hangoutWebHook ) {
            return $this->hangoutWebHook;
        }

        return  config('utilities.hangout_log_webhook');
    }

    public function hangoutLog($message)
    {
        $url = $this->getHangoutWebHook();
        $methodType = "POST";
        $body['text'] = $message;

        return $this->executeApi($url, $methodType, $body);
    }

    public function executeApi($url, $methodType, $body = NULL, $headers = NULL)
    {
        try {
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';

            $options['headers'] = $headers;

            if( $methodType == 'GET' ) {
                $request = $this->client->request( $methodType, $url, $options );
                $response = $request->getBody()->getContents();
            }

            if ( $methodType == 'POST' ) {
                $options['body'] = json_encode($body);

                $request = $this->client->request( $methodType, $url, $options );
                $response = $request->getBody()->getContents();
            }

            return json_decode( $response, true);
        }
        catch ( ClientException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
