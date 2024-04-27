<?php

namespace App\Http\Controllers;

use App\Helper\TodoResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CategoryController extends Controller
{

    /**
     * @OA\Schema(
     *     schema="Categories",
     *     type="object",
     *     required={"id", "name"},
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string")
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/category/create",
     *     summary="Create or Update a new Category",
     *     tags={"Category"},
     *     security={{"BearerAuth":{}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"name", "action"},
     *             @OA\Property(property="name", type="string", maxLength=255, description="Category name"),
     *             @OA\Property(property="action", type="string", maxLength=255, description="Category action")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category created OR updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Category already exist",
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
        try {
            if (!JWTAuth::getToken()) {
                TodoResponse::error('Token not provided', 401);
            }
            $isAdmin = JWTAuth::parseToken()->authenticate()->role; // Get the role of the user
            if ($isAdmin != 'Admin') {
                TodoResponse::error('You are not authorized to create category', 401);
            }
            $inputData = $request->only('name', 'action');

            $rules = [
                'name' => 'required|string',
                'action' => 'required|string|in:POST,GET',
            ];
            $errorCodes = [
                'name.required' => 'Name is required',
                'name.string' => 'Name must be a string',
                'action.required' => 'Action is required',
                'action.string' => 'Action must be a string',
            ];
            $validator = Validator::make($inputData, $rules, $errorCodes);
            if ($validator->fails()) {
                TodoResponse::error($validator->errors()->first(), 400);
            } else {
                $response = Category::createOrGetCategory($inputData);
                return $response;
            }
        } catch (\Exception $e) {
            TodoResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/category/update",
     *     summary="Update or Delete a new Category",
     *     tags={"Category"},
     *     security={{"BearerAuth":{}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"id", "name", "action"},
     *             @OA\Property(property="id", type="string", maxLength=255, description="Category Id"),
     *             @OA\Property(property="name", type="string", maxLength=255, description="Category name"),
     *             @OA\Property(property="action", type="string", maxLength=255, description="Category action")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated OR deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Category does not exist",
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

    public function categoryProcess(Request $request)
    {
        try {
            if (!JWTAuth::getToken()) {
                TodoResponse::error('Token not provided', 401);
            }
            $isAdmin = JWTAuth::parseToken()->authenticate()->role; // Get the role of the user
            if ($isAdmin != 'Admin') {
                TodoResponse::error('You are not authorized to create category', 401);
            }

            $inputData = $request->only('id', 'name', 'action');

            $rules = [
                'id' => 'required|string',
                'name' => 'required|string',
                'action' => 'required|string|in:PATCH,DELETE',
            ];

            $errorCodes = [
                'id.required' => 'Id is required',
                'id.string' => 'Id must be a string',
                'name.required' => 'Name is required',
                'name.string' => 'Name must be a string',
                'action.required' => 'Action is required',
                'action.string' => 'Action must be a string',
            ];
            $validator = Validator::make($inputData, $rules, $errorCodes);
            if ($validator->fails()) {
                TodoResponse::error($validator->errors()->first(), 400);
            } else {
                $response = Category::UpdateOrDeleteCategory($inputData);
                return $response;
            }
        } catch (\Exception $e) {
            TodoResponse::error($e->getMessage(), 500);
        }
    }
}
