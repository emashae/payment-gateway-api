<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

// Route for testing and returning authenticated user (if authenticated)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Creating a transaction
Route::post('/transactions', [TransactionController::class, 'createTransaction']);

// Fetching transaction status by ID 
Route::get('/transactions/{transactionId}', [TransactionController::class, 'getTransactionStatus']);
