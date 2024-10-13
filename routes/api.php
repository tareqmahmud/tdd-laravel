<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoController;

Route::apiResource('todos', TodoController::class);


Route::apiResource('tasks', TaskController::class);

Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
