<?php

namespace App\Http\Requests\API;

use App\Http\Requests\API\ApiRequest;

class LoginRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\RequestBody(required=true, request="login_request",
     *      @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", example="admin@zeus.vision", title="required|email"),
     *              @OA\Property(property="password", type="string", example="", title="required|string"),
     *      ),
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", example="admin@zeus.vision", title="required|email"),
     *              @OA\Property(property="password", type="string", example="", title="required|string"),
     *          )
     *      ),
     * )
     *
     * @OA\RequestBody(request="login_response_422", description="Error: Unprocessable Entity",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean", example=false),
     *          @OA\Property(property="message", example="The given data was invalid."),
     *          @OA\Property(property="errors",
     *              @OA\Property(property="email", example={"The email field is required."}),
     *              @OA\Property(property="password", example={"The password field is required."}),
     *          )
     *      )
     * )
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }
}
