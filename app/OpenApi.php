<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Email Service API",
    description: "API untuk mengirim email Direct dan Queue"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Current Server"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Server"
)]
class OpenApi {}
