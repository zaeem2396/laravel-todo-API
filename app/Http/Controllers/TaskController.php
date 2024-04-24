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
     * @OA\Schema(
     *     schema="Task",
     *     type="object",
     *     required={"id", "title", "description", "status", "due_date", "category_id"},
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="description", type="string"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="due_date", type="string", format="date-time"),
     *     @OA\Property(property="category_id", type="integer")
     * )
     */

    /**
     * @OA\Post(
     *     path="/api/task/create",
     *     summary="Create a new task",
     *     tags={"Task"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"user_id", "title", "description", "status", "due_date", "category_id"},
     *             @OA\Property(property="user_id", type="string", maxLength=255, description="User's id"),
     *             @OA\Property(property="title", type="string", maxLength=255, description="task title"),
     *             @OA\Property(property="description", type="string", minLength=255, description="task description"),
     *             @OA\Property(property="status", type="string", description="task status"),
     *             @OA\Property(property="due_date", type="date", description="task due date"),
     *             @OA\Property(property="category_id", type="string", description="category id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task created successfully",
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

    /**
     * @OA\Patch(
     *     path="/api/task/update",
     *     summary="Update a task",
     *     tags={"Task"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"id", "user_id", "title", "description", "status", "due_date", "category_id"},
     *             @OA\Property(property="id", type="string", maxLength=255, description="Task id"),
     *             @OA\Property(property="user_id", type="string", maxLength=255, description="User's id"),
     *             @OA\Property(property="title", type="string", maxLength=255, description="task title"),
     *             @OA\Property(property="description", type="string", maxLength=255, description="task description"),
     *             @OA\Property(property="status", type="string", description="task status"),
     *             @OA\Property(property="due_date", type="date", description="task due date"),
     *             @OA\Property(property="category_id", type="string", description="category id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
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

    /**
     * @OA\Delete(
     *     path="/api/task/delete",
     *     summary="Delete a task",
     *     tags={"Task"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", maxLength=255, description="Task id")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
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

    /**
     * @OA\Get(
     *     path="/api/task/taskList",
     *     summary="Get all tasks",
     *     tags={"Task"},
     *     @OA\Response(
     *         response=200,
     *         description="Tasks retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="app/Http/Controllers/TaskController"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while fetching tasks")
     *         )
     *     )
     * )
     */

    public function list()
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
