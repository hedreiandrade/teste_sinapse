<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas RESTful para usuários
Route::apiResource('users', UserController::class);


