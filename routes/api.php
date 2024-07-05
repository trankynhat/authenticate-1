<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::post('register/guest', [AuthController::class, 'registerGuest']);
Route::post('register/organization', [AuthController::class, 'registerOrganization']);
Route::post('login', [AuthController::class, 'login']);
Route::post('getAllUsers', [AuthController::class, 'getAllUsers']);
// routes/api.php


