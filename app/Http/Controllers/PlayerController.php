<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    /**
    * @OA\Post(
    *    path="/api/player",
    *    summary="Create a new player",
    *    description="Creates a new player with a name",
    *    operationId="createPlayer",
    *    tags={"Player"},
    *    @OA\RequestBody(
    *        required=true,
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *                type="object",
    *                required={"name"},
    *                @OA\Property(
    *                    property="name",
    *                    type="string",
    *                    description="Player's name",
    *                    example="John Doe"
    *                )
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=201,
    *        description="Player created successfully",
    *        @OA\JsonContent(
    *            @OA\Property(property="message", type="string", example="Player created successfully"),
    *            @OA\Property(property="player", type="object",
    *                @OA\Property(property="id", type="integer", example=1),
    *                @OA\Property(property="name", type="string", example="John Doe"),
    *                @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
    *                @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
    *            )
    *        )
    *    ),
    *    @OA\Response(
    *        response=422,
    *        description="Validation error",
    *        @OA\JsonContent(
    *            @OA\Property(property="message", type="string", example="The given data was invalid."),
    *            @OA\Property(property="errors", type="object",
    *                @OA\Property(property="name", type="array",
    *                    @OA\Items(type="string", example="The name field is required.")
    *                )
    *            )
    *        )
    *    )
    * )
    */
    public function create(Request $request) : \Illuminate\Http\JsonResponse
    {
        return PlayerController::create($request);
    }

    public static function _create(Request $request) : \Illuminate\Http\JsonResponse
    {
        $request->validate([
            "playerName"=> "required|string|max:255|unique:players,name",
        ]);
        
        $player = Player::create([
            "name"=> $request->playerName
        ]);
        
        return response()->json([
            "message"=> "Player created successfully",
            "player"=> $player,
        ], 201);
    }

    /**
     * @OA\Get(
     *   path="/api/player/all",
     *  summary="Get all players",
     *  description="Returns a list of all players",
     *  operationId="getAllPlayers",
     * tags={"Player"},
     * @OA\Response(
     *      response=200,
     *     description="A list of players",
     *    @OA\JsonContent(
     *        type="array",
     *       @OA\Items(
     *           type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *        @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *       )
     *   )
     *  ),
     * 
     */
    public function getAll() : \Illuminate\Http\JsonResponse
    {
        $players = Player::all();
        return response()->json(
            $players
        , 200);
    }
}
