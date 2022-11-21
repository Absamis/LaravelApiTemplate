<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function actionResponse($data = [], $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }
    public function processResponse($code, $message, $data = null, $error = null)
    {
        return response()->json(["code" => $code, "message" => $message, "data" => $data, "errors" => $error]);
    }
}
