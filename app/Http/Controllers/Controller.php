<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function createResponseAPI($status, $msg, $name, $data)
    {
        return response()->json([
            "message" => $msg,
            "$name" => $data
        ], $status);
    }

    public function createResponseValidate($errors)
    {
        return response()->json([
            "message" => "Invalid Field",
            "errors" => $errors
        ], 422);
    }

    public function forbiddenAccess()
    {
        return response()->json([
            "message" => "Forbidden access"
        ], 403);
    }

}
