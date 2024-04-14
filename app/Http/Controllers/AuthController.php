<?php

namespace App\Http\Controllers;

use App\Helper\TodoResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            $token = Auth::attempt($credentials);
            if(!$token){
                TodoResponse::error('Unauthorized', 401);
            }
            return response()->json([
                'user' => Auth::user(),
                'authorization' => [
                    'access_token' => $token,
                    'token_type' => 'bearer'
                ]
            ]);
        } catch (JWTException $e) {
            TodoResponse::error('System error occured', 500);
        }
    }
}
