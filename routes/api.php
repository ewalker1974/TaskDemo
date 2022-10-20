<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1.0'], function (): void {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user:uid}/tasks', [UserController::class, 'userTasks']);
    Route::resource('tasks', TaskController::class);
    Route::get('/tasks/{task:uid}/changes', [TaskController::class, 'taskChanges']);
});
