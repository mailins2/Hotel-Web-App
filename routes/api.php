<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KhachHangController;

Route::apiResource('khachhangs', KhachHangController::class);
