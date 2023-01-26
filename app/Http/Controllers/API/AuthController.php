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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
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
