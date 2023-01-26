<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * Method sendResponse
     *
     * @param $result
     * @param string|null $message
     * @param int $code
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function sendResponse($result = null, string $message = null, int $code = 200)
    {
    	$response = [
            'success' => true,
            'message' => $message ?? __('The request has been successful.'),
        ];

        if (!$result == null) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }

    /**
     * Method sendError
     *
     * @param $error
     * @param string|null $message
     * @param int $code
     * @param Throwable|null $e
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function sendError($error = null, string $message = null, int $code = 500, Throwable $e = null)
    {
    	$response = [
            'success' => false,
            'message' => $message ?? __('Internal Server Error'),
        ];

        if($error != null){
            $response['errors'] = $error;
        }

        // Send Email by Error
        // if ($e) {
        //     Utils::sendExceptionEmail($e);
        // }

        return response()->json($response, $code);
    }
}
