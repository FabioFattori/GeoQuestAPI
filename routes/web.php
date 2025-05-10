<?php

use App\Http\Controllers\CompletedPoiController;
use App\Http\Controllers\CompletedQuestController;
use App\Http\Controllers\EquippableItemsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RarityController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\UsableItemController;
use App\Http\Controllers\UserController;
use App\Models\CompletedQuest;
use App\Models\EquippableItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return redirect('api/documentation');
});

Route::prefix('api')->group(function () {
    Route::post("/user/login", [UserController::class, 'login']);
    Route::post("/user/logout", [UserController::class, 'logout']);
    Route::post("/user/checkToken", [UserController::class, "checkToken"]);
});

Route::prefix("api")->group(
    function () {
        // users
        Route::post('/user', [UserController::class, 'create']);
        Route::get("/user/all", [UserController::class, 'getAll']);
        Route::get("/user/{id}", [UserController::class, 'getById']);
        Route::delete("/user/{id}", [UserController::class, 'delete']);

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
        Route::put("/equippableItems/{id}", [EquippableItemsController::class, 'updateEquippableItem']);
        Route::get("/inventory", [EquippableItemsController::class, 'getInventory']);

        // rarities
        Route::get("/rarities", [RarityController::class, 'getAll']);

        //usable items
        Route::get("/usableItems/getAll", [UsableItemController::class, 'getAll']);
        Route::get("/usableItems/getUsableItemsOfUser", [UsableItemController::class, 'getUsableItemsOfUser']);
        Route::post("/usableItems/createRandomUsableItem", [UsableItemController::class, 'createRandomItem']);
        Route::delete("/usableItems/{id}", [UsableItemController::class, 'destroy']);
        Route::get("/usableItems/{id}", [UsableItemController::class, 'getById']);
        Route::put("/usableItems/{id}", [UsableItemController::class, 'update']);

        // completed quests
        Route::get("/completedQuests/getAll", [CompletedQuestController::class, 'getAll']);
        Route::post("/completedQuests/create", [CompletedQuestController::class, 'create']);

        // collected pois
        Route::get("/collectedPois/getAll", [CompletedPoiController::class, 'getAll']);
        Route::post("/collectedPois/create", [CompletedPoiController::class, 'create']);

        // league
        Route::get("/league/canGetReward", [LeagueController::class, 'canGetReward']);
        Route::post("/league/getReward", [LeagueController::class, 'getReward']);
        Route::get("/league", [PlayerController::class, 'getLeagueList']);
    }
)->middleware(['auth:sanctum']);
