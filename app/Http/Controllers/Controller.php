<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function returnStatus($module, $type = null)
    {

        if ($type === 'exception') {

            $message =  $module->getMessage();
            $line = $module->getLine();
            $status = 404;

            return response()->json(['message' => $message, 'line' => $line], $status);
        } else {

            $return =  $module ? "Action has been done successfully" : "Network Error.";
            $status = $module ? 200 : 400;

            return response()->json(['message' => $return, 'status' => $status]);
        }
    }


    public function returnData($data, $status = null)
    {

        if (empty($data)) {

            $return =  "No Data Found";
            $status = 404;

            return response()->json(['message' => $return, 'status' => $status]);
        } else {
            $status = 200;
            return response()->json($data, $status);
        }
    }
}
