<?php

/**
 * @OA\Info(
 *     title="laravel-todo",
 *     version="1.0",
 *     description="API Description"
 * )
 * * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Controller, AuthController, TaskController, UserController};
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/', [Controller::class, 'index']);

// User Routes
Route::post('/create', [UserController::class, 'create']);
Route::patch('/upadteUser', [UserController::class, 'update']);
Route::get('/profile', [UserController::class, 'getProfile']);
Route::get('/getTaskList', [UserController::class, 'getTasks']);
// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refreshToken', [AuthController::class, 'refreshToken']);

// Task Routes
Route::group(['prefix' => 'task'], function () {
    Route::post('/create', [TaskController::class, 'create']);
    Route::patch('/update', [TaskController::class, 'update']);
    Route::delete('/delete', [TaskController::class, 'delete']);
    Route::get('/taskList', [TaskController::class, 'list']);
});

Route::middleware(['check.jwt'])->group(function () {
    // routes here
});








Route::get('/cache', function () {

    Artisan::call('cache:clear');

    return 'Cache cleared!';
    // Artisan::call('make:controller', [
    //     'name' => 'UserController'
    // ]);

    // return 'UserController created successfully!';

});
