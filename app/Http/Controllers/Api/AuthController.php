<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Users\PasswordIncorrectException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePassword;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\UpdateAuth;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $remember = $request->has('remember');

        if (! $token = auth('api')->attempt($credentials, $remember)) {
            return response()->json(['message' => 'Wrong email or password'], 401);
        }

//        if (! auth('api')->user()->isActive()) {
//            return response()->json(['message' => 'Account is not active'], 401);
//        }

        return $this->respondWithUserToken($token, auth('api')->user());
    }

    /**
     * @param UpdateAuth $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAuth $request)
    {
        $data = $request->validated();
        auth()->user()->update($data);

        return response()->json(['message' => trans('message.update_profile_success')]);
    }

    /**
     * @param ChangePassword $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws PasswordIncorrectException
     */
    public function changePassword(ChangePassword $request)
    {
        if (Hash::check($request->password_current, auth()->user()->password) == false) {
            throw new PasswordIncorrectException();
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => trans('message.change_password_success')]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(new UserResource(auth('api')->user()));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    protected function respondWithUserToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'email' => $user->email,
            'name' => $user->name
        ]);
    }
}
