<?php

namespace App\Http\Controllers;

use App\Helper\DateTime;
use App\Helper\TodoResponse;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $inputData = $request->only('user_id', 'title', 'description', 'status', 'due_date', 'category_id');
            $rules = [
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:pending,completed',
                'due_date' => 'required|date',
                'category_id' => 'required|exists:categories,id',
            ];
            $errorCode = [
                'user_id.required' => 'User ID is required',
                'user_id.exists' => 'Invalid User ID',
                'title.required' => 'Title is required',
                'title.string' => 'Title must be a string',
                'title.max' => 'Title must be less than 255 characters',
                'description.required' => 'Description is required',
                'description.string' => 'Description must be a string',
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status',
                'due_date.required' => 'Due date is required',
                'due_date.date' => 'Invalid due date',
                'category_id.required' => 'Task category is required',
                'category_id.exists' => 'Invalid category ID',
            ];
            $validateTaskData = Validator::make($inputData, $rules, $errorCode);
            if ($validateTaskData->fails()) {
                TodoResponse::error($validateTaskData->errors()->all(), 400);
            } else {
                $taskResponse = Task::createTask($inputData);
                return $taskResponse;
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $inputData, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $inputData = $request->only('id', 'user_id', 'title', 'description', 'status', 'due_date', 'category_id');
            $rules = [
                'id' => 'required|exists:tasks,id',
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:pending,completed',
                'due_date' => 'required|date',
                'category_id' => 'required|exists:categories,id',
            ];
            $errorCode = [
                'id.required' => 'Task ID is required',
                'user_id.required' => 'User ID is required',
                'user_id.exists' => 'Invalid User ID',
                'title.required' => 'Title is required',
                'title.string' => 'Title must be a string',
                'title.max' => 'Title must be less than 255 characters',
                'description.required' => 'Description is required',
                'description.string' => 'Description must be a string',
                'status.required' => 'Status is required',
            ];
            $validateUpdatetaskData = Validator::make($inputData, $rules, $errorCode);
            if ($validateUpdatetaskData->fails()) {
                TodoResponse::error($validateUpdatetaskData->errors()->all(), 400);
            } else {
                $taskResponse = Task::updateTask($inputData, $inputData['id']);
                return $taskResponse;
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $inputData, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error($e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $inputData = $request->only('id');
            $rules = [
                'id' => 'required|exists:tasks,id',
            ];
            $errorCode = [
                'id.required' => 'Task ID is required',
                'id.exists' => 'Invalid Task ID',
            ];
            $validateUpdatetaskData = Validator::make($inputData, $rules, $errorCode);
            if ($validateUpdatetaskData->fails()) {
                TodoResponse::error($validateUpdatetaskData->errors()->all(), 400);
            } else {
                $taskResponse = Task::deleteTask($inputData['id']);
                return $taskResponse;
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $inputData, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error($e->getMessage(), 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $taskResponse = Task::getTasks();
            return $taskResponse;
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), [], __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error($e->getMessage(), 500);
        }
    }
}
