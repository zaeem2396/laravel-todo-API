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
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refreshToken', [AuthController::class, 'refreshToken']);

// Task Routes
Route::group(['prefix' => 'task'], function () {
    Route::post('/create', [TaskController::class, 'create']);
    Route::post('/update', [TaskController::class, 'update']);
    Route::post('/delete', [TaskController::class, 'delete']);
    Route::post('/taskList', [TaskController::class, 'list']);
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
