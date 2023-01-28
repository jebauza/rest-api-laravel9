<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\API\UserResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ApiController;

class AuthController extends ApiController
{
    /**
     * Register api
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="/register",
     *      tags={"Auth"},
     *      summary="Register",
     *      description="Register api.",
     *
     *      @OA\RequestBody(required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","c_password"},
     *              @OA\Property(property="name", type="string", example="Admin Zeus", title="required|string"),
     *              @OA\Property(property="email", type="string", example="admin@zeus.vision", title="required|email|unique:users,email"),
     *              @OA\Property(property="password", type="string", example="", title="required|string"),
     *              @OA\Property(property="c_password", type="string", example="", title="required|same:password")
     *          ),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name","email","password","c_password"},
     *                  @OA\Property(property="name", type="string", example="Admin Zeus", title="required|string"),
     *                  @OA\Property(property="email", type="string", example="admin@zeus.vision", title="required|email|unique:users,email"),
     *                  @OA\Property(property="password", type="string", example="", title="required|string"),
     *                  @OA\Property(property="c_password", type="string", example="", title="required|same:password")
     *              )
     *          ),
     *      ),
     *
     *      @OA\Response(response=200, description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", example="Successful authentication."),
     *              @OA\Property(property="data", description="these are the fields of the request",
     *                  @OA\Property(property="token_type", example="Bearer"),
     *                  @OA\Property(property="expires_at", example="2022-03-02 17:10:15"),
     *                  @OA\Property(property="access_token", example="11|qJdB9zfmvWUEnKhxNExfYogIA7CZvUx7MI9GvcHl"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(response=422, description="Error: Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", example="The given data was invalid."),
     *              @OA\Property(property="errors",
     *                  @OA\Property(property="name", example={"The name field is required."}),
     *                  @OA\Property(property="email", example={"The email field is required."}),
     *                  @OA\Property(property="password", example={"The password field is required."}),
     *                  @OA\Property(property="c_password", example={"The c_password field is required."}),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(response=500, ref="#/components/requestBodies/response_500"),
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), __("The given data was invalid."), 422);
        }

        try {
            DB::beginTransaction();

            $inputs = $request->only('name', 'email', 'password');
            $inputs['password'] = Hash::make($inputs['password']);
            $newUser = User::create($inputs);

            DB::commit();
            return $this->sendResponse(new UserResource($newUser), __('User register successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(null, $e->getMessage(), 500, $e);
        }
    }

    /**
     * Login api
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="/login",
     *      tags={"Auth"},
     *      summary="Login",
     *      description="Login api.",
     *
     *      @OA\RequestBody(ref="#/components/requestBodies/login_request"),
     *
     *      @OA\Response(response=200, description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", example="Successful authentication."),
     *              @OA\Property(property="data", description="these are the fields of the request",
     *                  @OA\Property(property="token_type", example="Bearer"),
     *                  @OA\Property(property="expires_at", example="2022-03-02 17:10:15"),
     *                  @OA\Property(property="access_token", example="11|qJdB9zfmvWUEnKhxNExfYogIA7CZvUx7MI9GvcHl"),
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(response=403, ref="#/components/requestBodies/response_403"),
     *
     *      @OA\Response(response=422, ref="#/components/requestBodies/login_response_422"),
     *
     *      @OA\Response(response=500, ref="#/components/requestBodies/response_500"),
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            // $credentials['is_active'] = true;

            DB::beginTransaction();

            // Auth::guard('web')
            if(!Auth::attempt($credentials, true)) {
                return $this->sendError(null, __('auth.failed'), 403);
            }

            $authUser = auth()->user();
            if ($authUser->tokens()->count() > 10) {
                $authUser->tokens->first()->delete();
            }

            $access_token = $authUser->createToken($authUser->email)->plainTextToken;
            $data = [
                'token_type' => 'Bearer',
                'expires_at' => now()->addMinutes(intval(config('sanctum.expiration')))->toDateTimeString(),
                'access_token' => $access_token,
            ];

            $lifetime = strtotime("+1 year"); // one year
            Config::set('session.lifetime', $lifetime);

            DB::commit();
            return $this->sendResponse($data, __('Successful authentication.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(null, $e->getMessage(), 500, $e);
        }
    }

    /**
     * Get authUser api
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *      path="/me",
     *      operationId="/me",
     *      tags={"Auth"},
     *      summary="User Auth",
     *      description="Get authenticated user.",
     *      security={{"api_key": {}}},
     *
     *      @OA\Response(response=200, description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", example="Authenticated user."),
     *              @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *          )
     *      ),
     *
     *      @OA\Response(response=401, ref="#/components/requestBodies/response_401"),
     *
     *      @OA\Response(response=500, ref="#/components/requestBodies/response_500"),
     * )
     */
    public function authUser(Request $request)
    {
        try {
            $authUser = $request->user();
            return $this->sendResponse(new UserResource($authUser), __('Authenticated user.'));
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500, $e);
        }
    }

    /**
     * Logout api
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *      path="/logout",
     *      operationId="/logout",
     *      tags={"Auth"},
     *      summary="Logout",
     *      description="Logout api.",
     *      security={{"api_key": {}}},
     *
     *      @OA\Response(response=200, description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", example="Session has been closed successfully."),
     *          )
     *      ),
     *
     *      @OA\Response(response=401, ref="#/components/requestBodies/response_401"),
     *
     *      @OA\Response(response=500, ref="#/components/requestBodies/response_500"),
     * )
     */
    public function logout(Request $request)
    {
        try {
            // $request->user()->currentAccessToken()->delete();
            if ($access_token = PersonalAccessToken::findToken($request->bearerToken())) {
                $access_token->delete();
            }

            auth()->guard('web')->logout();
            return $this->sendResponse(null, __('Session has been closed successfully.'));
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500, $e);
        }
    }
}
