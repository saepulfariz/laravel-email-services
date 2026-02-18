<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Services\EmailService;

class EmailServiceController extends Controller
{
    public function send(Request $request, EmailService $emailService)
    {
        //  API KEY VALIDATION
        if ($request->query('apikey') !== env('MAIL_API_KEY')) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid API Key'
            ], 401);
        }

        $request->validate([
            'to' => 'required|string',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        // Save to DB first
        $emailLog = EmailLog::create([
            'to' => $request->to,
            'cc' => $request->cc,
            'bcc' => $request->bcc,
            'reply_to' => $request->reply,
            'subject' => $request->subject,
            'body' => $request->body,
            'type' => $request->type,
            'attachments' => $request->attachments,
            'status' => 'pending'
        ]);

        try {

            $emailService->send($emailLog);

            $emailLog->update([
                'status' => 'sent'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully',
                'log_id' => $emailLog->id
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'log_id' => $emailLog->id
            ], 500);
        }
    }
}
