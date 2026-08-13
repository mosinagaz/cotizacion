<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('ufv/get-month/{yearMonth}', [\App\Http\Controllers\UfvController::class, 'getByMonth']);
Route::get('ufv/get-year/{year}', [\App\Http\Controllers\UfvController::class, 'getByYear']);

Route::get('dolar/get-month/{yearMonth}', [\App\Http\Controllers\DolarController::class, 'getByMonth']);
Route::get('dolar/get-year/{year}', [\App\Http\Controllers\DolarController::class, 'getByYear']);

Route::get('dolar-ref/get-month/{yearMonth}', [\App\Http\Controllers\DolarRefController::class, 'getByMonth']);
Route::get('dolar-ref/get-year/{year}', [\App\Http\Controllers\DolarRefController::class, 'getByYear']);

Route::apiResource('ufv', \App\Http\Controllers\UfvController::class);
Route::apiResource('dolar', \App\Http\Controllers\DolarController::class);
Route::apiResource('dolar-ref', \App\Http\Controllers\DolarRefController::class);
