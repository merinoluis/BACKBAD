<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\RolController;

Route::get('/roles', [RolController::class, 'index']);
Route::post('/roles', [RolController::class, 'store']);
