<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function status(Request $request)
    {
        // Twilio posts: MessageStatus, MessageSid, ErrorCode, ErrorMessage
        $sid = $request->input('MessageSid') ?? $request->input('SmsSid');
        $status = $request->input('MessageStatus');

        if (!$sid) return response('ok');

        $sms = SmsMessage::where('twilio_sid', $sid)->first();
        if ($sms) {
            $data = [
                'status' => $status,
                'error_code'    => $request->input('ErrorCode'),
                'error_message' => $request->input('ErrorMessage'),
            ];
            if ($status === 'delivered') $data['delivered_at'] = now();
            $sms->update($data);
        } else {
            Log::warning('Twilio status for unknown SID', ['sid'=>$sid, 'status'=>$status]);
        }

        // Twilio expects a 200 quickly
        return response('ok');
    }
}
