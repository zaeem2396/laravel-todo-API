<?php

/**
 * @OA\Info(
 *     title="laravel-todo",
 *     version="1.0",
 *     description="API Description"
 * )
 */

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/', [Controller::class, 'index']);
Route::post('/create', [UserController::class, 'create']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/cache', function () {

    Artisan::call('cache:clear');

    return 'Cache cleared!';
    // Artisan::call('make:controller', [
    //     'name' => 'UserController'
    // ]);

    // return 'UserController created successfully!';

});
