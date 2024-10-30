<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    private const SUCCESS_STATUS = 'success';
    private const ERROR_STATUS = 'error';

    public static function success($data = [], $message = 'success') : JsonResponse
    {
        return self::responseJson($data, $message);
    }

    public static function error($message = 'error', $statusCode = 422) : JsonResponse
    {
        return self::responseJson([], $message, self::ERROR_STATUS, $statusCode);
    }

    public static function notFoundError($resourceName = 'Resource') : JsonResponse
    {
        return self::responseJson([], $resourceName . ' not found', self::ERROR_STATUS, 404);
    }

    public static function unauthorizedError()
    {
        return self::responseJson([], 'Unauthorized', self::ERROR_STATUS, 401);
    }

    private static function responseJson($data, $message = 'success', $status = self::SUCCESS_STATUS, $statusCode = 200) : JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
            'status_code' => $statusCode
        ]);
    }
}
