<?php

use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:' . config('books.throttle_per_minute') . ',1')->group(function (): void {
    Route::apiResource('books', BookController::class);
});
