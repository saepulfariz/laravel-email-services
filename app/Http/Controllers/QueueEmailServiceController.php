<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Jobs\SendEmailJob;

use OpenApi\Attributes as OA;

class QueueEmailServiceController extends Controller
{


    #[OA\Post(
        path: "/api/q/email-services",
        tags: ["Email Queue"],
        summary: "Send Email via Queue",
        parameters: [
            new OA\Parameter(
                name: "apikey",
                in: "query",
                required: true,
                description: "API Key untuk autentikasi",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "type",
                in: "query",
                required: false,
                description: "Jenis mailer (default smtp)",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["smtp", "public"],
                    default: "smtp"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["to", "subject", "body"],
                properties: [
                    new OA\Property(
                        property: "to",
                        type: "string",
                        example: "email1@domain.com;email2@domain.com"
                    ),
                    new OA\Property(
                        property: "subject",
                        type: "string",
                        example: "Hello World"
                    ),
                    new OA\Property(
                        property: "body",
                        type: "string",
                        example: "Body Message"
                    ),
                    new OA\Property(
                        property: "cc",
                        type: "string",
                        example: "email2@domain.com"
                    ),
                    new OA\Property(
                        property: "bcc",
                        type: "string",
                        example: "email2@domain.com"
                    ),
                    new OA\Property(
                        property: "reply",
                        type: "string",
                        example: "email2@domain.com"
                    ),
                    new OA\Property(
                        property: "attachments",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "filename",
                                    type: "string",
                                    example: "gambar.jpg"
                                ),
                                new OA\Property(
                                    property: "url",
                                    type: "string",
                                    example: "https://example.com/file.jpg"
                                ),
                                new OA\Property(
                                    property: "mime",
                                    type: "string",
                                    example: "image/jpeg"
                                ),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Email queued successfully"
            ),
            new OA\Response(
                response: 401,
                description: "Invalid API Key"
            )
        ]
    )]

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
            'token' => $request->query('apikey'),
            'to' => $request->to,
            'cc' => $request->cc,
            'bcc' => $request->bcc,
            'reply_to' => $request->reply,
            'subject' => $request->subject,
            'body' => $request->body,
            'attachments' => $request->attachments,
            'status' => 'pending',
            'type' => $request->type ?? 'smtp'
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
