<?php

namespace App\Http\Controllers;

use App\Helper\DateTime;
use App\Helper\TodoResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Handle login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                TodoResponse::error('Unauthorized', 401);
            }
            $data = [
                'user' => Auth::user(),
                'authorization' => [
                    'access_token' => $token,
                    'token_type' => 'bearer'
                ]
            ];
            TodoResponse::success('LoggedIn', $data);
        } catch (JWTException $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $credentials, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }

    /**
     * Handle token refresh.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        try {
            $refreshToken = JWTAuth::parseToken()->refresh();
            $data = [
                'authorization' => [
                    'access_token' => $refreshToken,
                    'token_type' => 'bearer'
                ]
            ];
            TodoResponse::success('Refresh Token', $data);
        } catch (JWTException $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), [], __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }
}
