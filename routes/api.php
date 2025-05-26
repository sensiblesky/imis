<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Admin\DashboardController;
use App\Http\Controllers\API\Chats\ChatApiController;

Route::get('/user', fn(Request $request) => $request->user());
// Route::middleware(['web', 'auth'])->get('/system/usage', [DashboardController::class, 'showSystemUsage']);
Route::middleware(['web', 'auth', \App\Http\Middleware\Base\CustomCors::class])->get('/system/usage', [DashboardController::class, 'showSystemUsage']);
Route::middleware(['web', 'auth'])->get('/chat/updates', [ChatApiController::class, 'fetchUpdates']);
Route::middleware(['web', 'auth'])->get('/chat/conversations/{conversation_id}', [ChatApiController::class, 'loadConversation']);

