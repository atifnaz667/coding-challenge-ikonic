<?php

use App\Http\Controllers\CommonConnectionsController;
use App\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\SentRequestController;
use App\Http\Controllers\ReceivedRequestController;

Route::resource('suggestion', SuggestionController::class);
Route::get('getCounts', [SuggestionController::class,'getCounts'])->name('getCounts');
Route::resource('recived_request', ReceivedRequestController::class);
Route::resource('send_request', SentRequestController::class);
Route::resource('connection', ConnectionController::class);
Route::resource('common_connection', CommonConnectionsController::class);
