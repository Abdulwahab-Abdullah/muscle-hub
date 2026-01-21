<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($message, $data = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => __("messages.$message"),
            'data' => $data
        ], $code);
    }

    public static function error($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => __("messages.$message")
        ], $code);
    }
}
