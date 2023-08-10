<?php

use Illuminate\Support\Facades\Route;
use RickDBCN\FilamentEmail\Http\Controllers\PostmarkController;

Route::get('/drivers/postmark', [PostmarkController::class, 'handle'])->name('postmark.handle');
