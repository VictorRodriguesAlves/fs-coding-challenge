<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('chat.index');
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');