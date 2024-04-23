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
     * 
     * * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate a user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, description="User's email"),
     *             @OA\Property(property="password", type="string", minLength=6, description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User loggedIn successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
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
     * 
     * * @OA\Post(
     *     path="/api/refreshToken",
     *     summary="Refresh token",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Access token",
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", format="token", description="Access token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        $token = $request->input('token');
        try {
            JWTAuth::setToken($token);
            $refreshToken = JWTAuth::refresh($token);
            $data = [
                'authorization' => [
                    'access_token' => $refreshToken,
                    'token_type' => 'bearer',
                ],
            ];
            TodoResponse::success('Token refreshed successfully', $data);
        } catch (JWTException $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), [], __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occurred', 500);
        }
    }
}
