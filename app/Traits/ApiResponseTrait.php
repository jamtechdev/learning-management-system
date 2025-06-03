<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Exception;

trait ApiResponseTrait
{
    /**
     * Generate a JSON response for a general error.
     *
     * @param int    $code
     * @param string $message
     *
     * @return JsonResponse
     */
    public function errorHandler(int $code = 403, string $message = null, $data = null): JsonResponse
    {
        $error = [
            'version' => 'v1',
            'code' => $code,
            'message' => $message,
            'data' => $data ?? (object) [], // Ensures "data" is always present, even if null
        ];

        return response()->json($error, $code);
    }

    /**
     * Generate a JSON response for an unauthorized error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function unauthorizedHandler(string $message = 'Your credentials are incorrect or your account has been blocked by the server administrator.'): JsonResponse
    {
        $response = [
            'version' => 'v1',
            'code' => 401,
            'message' => $message,
        ];
        return response()->json($response, 401);
    }
    /**
     * Generate a JSON response for a success message.
     *
     * @param mixed  $data
     * @param int    $code
     * @param string $message
     *
     * @return JsonResponse
     */
    public function successHandler($data, int $code = 200, string $message = null): JsonResponse
    {
        $response = [
            'message' => $message,
            'version' => 'v1',
            'code' => $code,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }
    public function okHandler(int $code = 200, string $message = null): JsonResponse
    {
        $error = [
            'version' => 'v1',
            'message' => $message,
            'code' => $code,
        ];

        return response()->json($error, $code);
    }
    /**
     * Generate a JSON response for an internal server error.
     *
     * @param Exception $e
     * @param bool      $isStripe
     *
     * @return JsonResponse
     */
    public function serverErrorHandler(Exception|\Throwable $e, bool $isStripe = false): JsonResponse
    {

        $error = [
            'debug' => (config('app.env') !== 'production') ? [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'code' => $e->getCode(),
            ] : null,
            'message' => (!$isStripe) ? "Unable to process your request at this time because the server encountered an unexpected condition." : $e->getMessage(),
            'version' => 'v1',
            'code' => 500,
        ];

        // Log the debug message for debugging purposes
        logger('Debug message: ' . $e->getMessage());

        return response()->json($error, 500);
    }

    /**
     * Generate a JSON response for input validation errors.
     *
     * @param array|null $validationErrors
     *
     * @return JsonResponse
     */
    public function validationErrorHandler(object $validationErrors = null): JsonResponse
    {
        $error = [
            "validation_error" => $validationErrors,
            'message' => 'Validation failed. Please check your input.',
            'version' => 'v1',
            'code' => 422,
        ];

        return response()->json($error, 422);
    }

    /**
     * Generate a JSON response for a "not found" error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function notFoundHandler(string $message = 'Resource not found. Please check back later or try a different search.'): JsonResponse
    {
        $response = [
            'data' => [],
            'version' => 'v1',
            'code' => 404,
            'message' => $message,
        ];
        return response()->json($response, 404);
    }
}
