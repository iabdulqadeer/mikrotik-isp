<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Http\CurlClient;


class TwilioSmsService
{
    protected Client $client;
    protected string $from;

    public function __construct(?Client $client = null)
    {
        // $sid   = config('services.twilio.sid', env('TWILIO_ACCOUNT_SID'));
        // $token = config('services.twilio.token', env('TWILIO_AUTH_TOKEN'));
        // $this->from = config('services.twilio.from', env('TWILIO_FROM'));


        $sid   = "ACfc09713fca411677d6d8282e8e475d85";
        $token = "7cd78c693eb43cb53bd16ba8063b938f";
        $this->from = "+12085987223";

       // only disable SSL checks in local/dev
        $httpClient = null;
        if (app()->environment('local')) {
            $curlOptions = [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false];
            $httpClient = new CurlClient($curlOptions);
        }

        $this->client = new Client($sid, $token, null, null, $httpClient);
    }

    public function send(string $to, string $body): array
    {
        $msg = $this->client->messages->create($to, [
            'from' => $this->from,
            'body' => $body,
            'statusCallback' => route('webhooks.twilio.status'),
        ]);

        return [
            'sid'     => $msg->sid,
            'status'  => (string) $msg->status, // queued/sent
            'to'      => $msg->to,
            'from'    => $msg->from,
            'date'    => now(),
        ];
    }
}
