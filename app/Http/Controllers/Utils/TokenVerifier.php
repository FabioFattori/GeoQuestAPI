<?php

namespace App\Http\Controllers\Utils;

use Crypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Void_;

abstract class TokenVerifier
{
    static public function verifyToken(): bool
    {

        if (env("DEBUG", true)) {
            return true;
        }

        //validate csrf token
        $token = request()->input("csrf_token");
        if ($token == null) {
            return false;
        }
        $token = Crypt::decryptString($token);
        return $token == csrf_token();
    }

    // if the token is valid do nothing, if not return a json response with 403 status
    static public function verifyTokenAndRespond(): JsonResponse|null
    {
        if (!self::verifyToken()) {
            return response()->json([
                "message" => "Invalid CSRF token",
                "status" => 403
            ], 403);
        }

        return null;
    }
}