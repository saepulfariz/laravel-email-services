<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class EmailServiceController extends Controller
{
    public function send(Request $request)
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

        try {

            $to = explode(';', $request->to);
            $cc = $request->cc ? explode(';', $request->cc) : [];
            $bcc = $request->bcc ? explode(';', $request->bcc) : [];
            $reply = $request->reply ? explode(';', $request->reply) : [];

            Mail::send([], [], function ($message) use ($request, $to, $cc, $bcc, $reply) {

                $message->to($to)
                    ->subject($request->subject)
                    ->html($request->body);
                // ->setBody($request->body, 'text/html');

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($bcc)) {
                    $message->bcc($bcc);
                }

                if (!empty($reply)) {
                    $message->replyTo($reply);
                }

                //  Attachments via URL
                if ($request->attachments) {
                    foreach ($request->attachments as $file) {

                        $fileContent = Http::get($file['url'])->body();

                        $message->attachData(
                            $fileContent,
                            $file['filename'],
                            ['mime' => $file['mime']]
                        );
                    }
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
