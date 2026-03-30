<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success.', int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }

    protected function error(string $message = 'Something went wrong.', int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
