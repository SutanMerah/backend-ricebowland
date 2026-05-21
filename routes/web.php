<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

Route::post('/orders',[OrderController::class,'store']);
Route::get('/orders',[OrderController::class,'index']);