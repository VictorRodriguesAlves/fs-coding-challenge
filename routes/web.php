<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('chat.index');
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');