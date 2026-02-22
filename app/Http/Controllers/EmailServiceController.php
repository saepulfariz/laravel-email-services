<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use App\Services\EmailService;
use OpenApi\Attributes as OA;

class EmailServiceController extends Controller
{

    #[OA\Post(
        path: "/api/email-services",
        tags: ["Email"],
        summary: "Send Email via",
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


    #[OA\Post(
        path: "/api/d/email-services",
        tags: ["Email Direct"],
        summary: "Send Email Direct",
        parameters: [
            new OA\Parameter(
                name: "apikey",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "type",
                in: "query",
                required: false,
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
                        example: "email2@domain.com"
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
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Email sent successfully"
            )
        ]
    )]

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
            'token' => $request->query('apikey'),
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
                'success' => true,
                'message' => 'Email sent successfully',
                'log_id' => $emailLog->id
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'log_id' => $emailLog->id
            ], 500);
        }
    }
}
