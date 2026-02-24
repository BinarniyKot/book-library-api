<?php

use Illuminate\Support\Facades\Route;

Route::get('/', static fn() => redirect('/api'));
Route::get('/api', static fn() => view('welcome'));
