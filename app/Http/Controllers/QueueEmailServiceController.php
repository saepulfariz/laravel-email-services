<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Jobs\SendEmailJob;

class QueueEmailServiceController extends Controller
{


    public function send(Request $request)
    {
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
            'attachments' => $request->attachments,
            'status' => 'pending'
        ]);

        // Dispatch to queue
        SendEmailJob::dispatch($emailLog);

        return response()->json([
            'status' => true,
            'message' => 'Email queued successfully',
            'log_id' => $emailLog->id
        ]);
    }
}
