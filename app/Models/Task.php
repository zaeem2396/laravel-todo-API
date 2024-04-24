<?php

namespace App\Models;

use App\Helper\DateTime;
use App\Helper\TodoResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'due_date',
        'category_id'
    ];

    public static function createTask(array $data)
    {
        try {
            $task = self::create($data);
            if ($task) {
                TodoResponse::success('Task created successfully', 200);
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $data, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }

    public static function updateTask(array $data, $id, $user_id)
    {
        try {
            $task = self::where('id', $id)->where('user_id', $user_id)->first();
            if ($task) {
                $task->update($data);
                TodoResponse::success('Task updated successfully', 200);
            } else {
                TodoResponse::error('Task not found', 404);
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $data, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }

    public static function deleteTask($id)
    {
        try {
            $task = self::find($id);
            if ($task) {
                $task->delete();
                TodoResponse::success('Task deleted successfully', 200);
            } else {
                TodoResponse::error('Task not found', 404);
            }
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), $id, __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }

    public static function getTasks($user_id = null, $task_id = null)
    {
        try {
            $tasks = DB::table('tasks')
                ->join('users', 'users.id', '=', 'tasks.user_id')
                ->join('categories', 'categories.id', '=', 'tasks.category_id')
                ->select('tasks.*', 'users.name as user_name', 'categories.name as task_type');

            if ($user_id)
                $tasks = $tasks->where('user_id', '=', $user_id);

            if ($task_id)
                $tasks = $tasks->where('tasks.id', '=', $task_id);
            $tasks = $tasks->get();
            $data = [
                'code' => 200,
                'task_list' => $tasks,

            ];
            TodoResponse::success('Tasks retrieved successfully', $data);
        } catch (\Exception $e) {
            TodoResponse::errorLog($_SERVER['REQUEST_METHOD'], URL::full(), [], __FILE__, $e->getLine(), __METHOD__, $e->getMessage(), DateTime::formatDateTime());
            TodoResponse::error('System error occured', 500);
        }
    }
}
