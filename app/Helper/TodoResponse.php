<?php

namespace App\Helper;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodoResponse
{
    /**
     * Responds with a success message and data.
     *
     * @param string $message The success message.
     * @param mixed $data The data to include in the response.
     * @param int $status The HTTP status code.
     * @return void
     */
    public static function success(string $message, $data, int $status = Response::HTTP_OK): void
    {
        response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status)->send();
        exit; // Terminate the request
    }

    /**
     * Responds with an error message.
     *
     * @param array|string $errors The error message(s).
     * @param int $status The HTTP status code.
     * @return void
     */
    public static function error($errors, int $status = Response::HTTP_BAD_REQUEST): void
    {
        response()->json([
            'status' => false,
            'message' => $errors,
            'code' => $status,
        ], $status)->send();
        exit; // Terminate the request
    }

    public static function errorLog($http_request, $url, $params, $line, $method, $error, $timestamp): void
    {
        $errorLog = [
            'http_request' => $http_request,
            'url' => $url,
            'params' => $params,
            'line' => $line,
            'method' => $method,
            'error' => $error,
            'timestamp' => $timestamp,
        ];
        DB::table('error_logs')->insert(['error' => json_encode($errorLog)]);
    }
}
