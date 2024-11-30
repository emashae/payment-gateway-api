<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

// Route for testing and returning authenticated user (if authenticated)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Create transaction
Route::post('/transactions', [TransactionController::class, 'createTransaction']);

// Retrieve transaction by ID 
Route::get('transactions/{id}', [TransactionController::class, 'retrieveTransaction']);

