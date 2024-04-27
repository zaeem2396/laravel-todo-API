<?php

namespace App\Http\Controllers;

use App\Helper\TodoResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CategoryController extends Controller
{
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
