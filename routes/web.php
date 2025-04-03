<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('api/documentation');
});

Route::prefix("api")->group(
    function () {
        Route::post('/user', [UserController::class, 'create']);
    }
);
