<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TestController extends Controller
{
    #[OA\Get(
        path: "/api/test",
        summary: "Test endpoint",
        tags: ["Test"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success"
            )
        ]
    )]
    public function index()
    {
        return response()->json(['message' => 'OK']);
    }
}
