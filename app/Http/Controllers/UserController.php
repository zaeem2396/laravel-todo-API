<?php

namespace App\Http\Controllers;

use App\Helper\TodoResponse;
use App\Models\{Users, Task};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/create",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", maxLength=255, description="User's name"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, description="User's email"),
     *             @OA\Property(property="password", type="string", minLength=6, description="User's password"),
     *             @OA\Property(property="password_confirmation", type="string", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
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
    public function create(Request $request)
    {
        $inputData = $request->only('name', 'email', 'password', 'password_confirmation');
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ];
        $errorCode = [
            'name.required' => 'Name field cannot be blank',
            'email.required' => 'Email field cannot be blank',
            'password.required' => 'Password field cannot be blank',
            'password_confirmation.required' => 'Password confirmation field cannot be blank',
        ];
        $validateUserdata = Validator::make($inputData, $rules, $errorCode);
        if ($validateUserdata->fails()) {
            TodoResponse::error($validateUserdata->errors()->all(), 400);
        } else {
            $userResponse = Users::createUser($inputData);
            return $userResponse;
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/upadteUser",
     *     summary="Update user data",
     *     tags={"Profile"},
     *     security={{"BearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", maxLength=255, description="User's name"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, description="User's email"),
     *             @OA\Property(property="password", type="string", minLength=6, description="User's password"),
     *             @OA\Property(property="password_confirmation", type="string", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User data updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     * )
     */


    public function update(Request $request)
    {
        try {
            $authenticatedUser = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            TodoResponse::error($e->getMessage(), 401);
        }

        $inputData = $request->only('name', 'email', 'password', 'password_confirmation');
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ];
        $errorCode = [
            'name.required' => 'Name field cannot be blank',
            'email.required' => 'Email field cannot be blank',
            'password.required' => 'Password field cannot be blank',
            'password_confirmation.required' => 'Password confirmation field cannot be blank',
        ];
        $validateUserData = Validator::make($inputData, $rules, $errorCode);

        if ($validateUserData->fails()) {
            TodoResponse::error($validateUserData->errors()->all(), 400);
        }

        $inputData['id'] = $authenticatedUser->id;

        $userResponse = Users::updateUser($inputData);
        return $userResponse;
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     tags={"Profile"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function getProfile()
    {
        try {
            $authenticatedUser = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            TodoResponse::error($e->getMessage(), 401);
        }
        $userResponse = Users::getUser($authenticatedUser->id);
        return $userResponse;
    }

    /**
     * @OA\Get(
     *     path="/api/getTaskList",
     *     summary="Get user Task",
     *     tags={"Users"},
     *     security={{"BearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Task list fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="user_id", type="integer", example="1"),
     *                 @OA\Property(property="title", type="string", example="Task title"),
     *                 @OA\Property(property="description", type="string", example="Task description"),
     *                 @OA\Property(property="status", type="string", example="Completed | Pending"),
     *                 @OA\Property(property="due_date", type="date", example="01-01-2000"),
     *                 @OA\Property(property="category_id", type="string",  example="4"),
     *                 @OA\Property(property="created_at", type="date",  example="01-01-2000"),
     *                 @OA\Property(property="updated_at", type="date",  example="01-01-2000"),
     *                 @OA\Property(property="user_name", type="string",  example="John doe"),
     *                 @OA\Property(property="task_type", type="string",  example="Urgent | Work | Personal"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function getTasks()
    {
        try {
            $authenticatedUser = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            TodoResponse::error($e->getMessage(), 401);
        }
        $userResponse = Task::getTasks($authenticatedUser->id);
        return $userResponse;
    }
}
