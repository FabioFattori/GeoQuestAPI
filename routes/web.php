<?php

use App\Http\Controllers\EquippableItemsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RarityController;
use App\Http\Controllers\UsableItemController;
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
        Route::get("/user/{id}", [UserController::class, 'getById']);
        Route::delete("/user/{id}", [UserController::class, 'delete']);
        Route::post("/user/login", [UserController::class, 'login']);
        Route::post("/user/logout", [UserController::class, 'logout']);
        Route::post("/user/checkToken",[UserController::class,"checkToken"]);

        // players
        Route::post("/player", [PlayerController::class, 'create']);
        Route::get("/player/all", [PlayerController::class, 'getAll']);
        Route::get("/player/{id}", [PlayerController::class, 'getById']);
        Route::delete("/player/{id}", [PlayerController::class, 'delete']);
        Route::put("/player/{id}", [PlayerController::class, 'update']);

        // equippable items
        Route::get("/equippableItems", [EquippableItemsController::class, 'getAll']);
        Route::get("/equippableItems/{id}", [EquippableItemsController::class, 'getById']);
        Route::post("/equippableItems", [EquippableItemsController::class, 'createRandomItem']);

        // rarities
        Route::get("/rarities", [RarityController::class, 'getAll']);

        //usable items
        Route::get("/usableItems/getAll", [UsableItemController::class, 'getAll']);
        Route::get("/usableItems/getUsableItemsOfUser", [UsableItemController::class, 'getUsableItemsOfUser']);
        Route::post("/usableItems/createRandomUsableItem", [UsableItemController::class, 'createRandomItem']);
    }
)->middleware(['auth:sanctum']);
