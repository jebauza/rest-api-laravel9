<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Zeus-Laravel9",
 *      description="This Api is private. To use it, contact us to generate your credentials.",
 *      @OA\Contact(
 *          email="admin@zeus.vision"
 *      )
 * )
 *
 * @OA\ExternalDocumentation(
 *     description="Zeus-Laravel9",
 *     url=""
 * )
 *
 * @OA\Server(
 *      url="{protocol}://www.laravel9-zeus-base-project.local/api",
 *      description="local server",
 *      @OA\ServerVariable(
 *          serverVariable="protocol",
 *          enum={"https", "http"},
 *          default="https"
 *      )
 * )
 *
 * @OA\Server(
 *      url="{protocol}://laravel9-zeus-base-project.vision/api/api",
 *      description="development server",
 *      @OA\ServerVariable(
 *          serverVariable="protocol",
 *          enum={"https", "http"},
 *          default="https"
 *      )
 * )
 *
 * @OA\Server(
 *      url="{protocol}://laravel9-zeus-base-project.zeus.vision/api/api",
 *      description="production server",
 *      @OA\ServerVariable(
 *          serverVariable="protocol",
 *          enum={"https", "http"},
 *          default="https"
 *      )
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="api_key",
 *      type="apiKey",
 *      in="header",
 *      description="Enter token in format (Bearer {token})",
 *      name="Authorization"
 * )
 *
 * @OA\Parameter(parameter="x_localization", name="X-localization",
 *      in="header",
 *      description="Set language parameter",
 *      @OA\Schema(
 *          enum={"es","en","ru"},
 *          type="string",
 *          example="es"
 *      )
 * )
 *
 */
abstract class ApiController extends Controller
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
    /**
     * @OA\RequestBody(request="response_200", description="OK",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", example=true),
     *          @OA\Property(property="message", example="The request has been successful.")
     *      )
     * )
     *
     * @OA\RequestBody(request="response_201", description="Successful created",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", example="Server message")
     *      )
     * )
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
    /**
     * @OA\RequestBody(request="response_400", description="Error: Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", example=false),
     *              @OA\Property(property="message", example="Solicitud Incorrecta")
     *          )
     * )
     *
     * @OA\RequestBody(request="response_401", description="Error: Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", example="Unauthenticated.")
     *          )
     * )
     *
     * @OA\RequestBody(request="response_403", description="Error: Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", example=false),
     *              @OA\Property(property="message", example="You do not have permissions for the requested resources")
     *          )
     * )
     *
     * @OA\RequestBody(request="response_404", description="Error: Not Found",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", example=false),
     *          @OA\Property(property="message", example="No Encontrado")
     *      )
     * )
     *
     * @OA\RequestBody(request="response_500", description="Error: Internal Server Error",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", example=false),
     *          @OA\Property(property="message", example="Internal Server Error")
     *      )
     * )
     *
     * @OA\RequestBody(request="response_503", description="Error: Service Unavailable",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", example=false),
     *          @OA\Property(property="message", example="Service Unavailable")
     *      )
     * )
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
