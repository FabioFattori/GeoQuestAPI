<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('api/documentation');
});

Route::prefix("api")->group(
    function () {
        // users
        Route::post('/user', [UserController::class, 'create']);
        Route::get("/user/all", [UserController::class, 'getAll']);

        // players
        Route::post("/player", [UserController::class, 'create']);
        Route::get("/player/all", [UserController::class, 'getAll']);
    }
);
