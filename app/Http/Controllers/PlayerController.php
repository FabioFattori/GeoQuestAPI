<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Enums\EquippableItemType;
use App\Http\Controllers\LeagueController;

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
     *                required={"playerName"},
     *                @OA\Property(
     *                    property="playerName",
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
     *            @OA\Property(property="player", type="string", example="stringified player object")
     *        )
     *    )
     * )
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        return self::_create($request);
    }

    public static function _create(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            "playerName" => "required|string|max:255|unique:players,name",
        ]);

        $player = Player::create([
            "name" => $request->playerName
        ]);

        return response()->json([
            "message" => "Player created successfully",
            "player" => $player,
        ], 201);
    }

    /**
     * @OA\Get(
     *   path="/api/player/all",
     *  summary="Get all players",
     *  description="Returns a list of all players",
     *  operationId="getAllPlayers",
     *  tags={"Player"},
     * @OA\Response(
     *      response=200,
     *     description="A list of players",
     *    @OA\JsonContent(
     *        type="array",
     *       @OA\Items(
     *           @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="John Doe")
     *         )
     *      )
     *   )
     *  ),
     * 
     */
    public function getAll(): \Illuminate\Http\JsonResponse
    {
        $players = Player::all();
        return response()->json(
            $players
            ,
            200
        );
    }

    /**
     * @OA\Get(
     *   path="/api/player/{id}",
     *  summary="Get a player by ID",
     *  description="Returns a player by ID",
     *  operationId="getPlayerById",
     *  tags={"Player"},
     * @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="ID of the player to retrieve",
     *      @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Player found",
     *      @OA\JsonContent(type="string", example="Player stringified object")
     * ),
     * @OA\Response(
     *      response=404,
     *      description="Player not found"
     * )
     *)
     */
    public function getById($id): \Illuminate\Http\JsonResponse
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json([
                "message" => "Player not found",
            ], 404);
        }

        return response()->json($player, 200);
    }

    /**
     * @OA\Delete(
     *   path="/api/player/{id}",
     *  summary="Delete a player by ID",
     *  description="Deletes a player by ID",
     *  operationId="deletePlayerById",
     *  tags={"Player"},
     * @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="ID of the player to delete",
     *      @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Player deleted successfully"
     * ),
     * @OA\Response(
     *      response=404,
     *      description="Player not found"
     * )
     *)
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {

        $player = Player::find($id);

        if (!$player) {
            return response()->json([
                "message" => "Player not found",
            ], 404);
        }

        $player->delete();

        return response()->json([
            "message" => "Player deleted successfully",
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/player/{id}",
     *     summary="Update a player",
     *     description="Aggiorna le informazioni di un giocatore esistente, come nome, livello, esperienza raccolta, vittorie e battaglie totali.",
     *     tags={"Player"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del giocatore da aggiornare",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Nuovo nome del giocatore",
     *                     example="john doe"
     *                 ),
     *                 @OA\Property(
     *                     property="level",
     *                     type="integer",
     *                     description="Nuovo livello del giocatore",
     *                     example=10
     *                 ),
     *                 @OA\Property(
     *                     property="experienceCollected",
     *                     type="integer",
     *                     description="Esperienza raccolta dal giocatore",
     *                     example=1000
     *                 ),
     *                 @OA\Property(
     *                     property="nWonBattles",
     *                     type="integer",
     *                     description="Numero di battaglie vinte dal giocatore",
     *                     example=50
     *                 ),
     *                 @OA\Property(
     *                     property="nBattles",
     *                     type="integer",
     *                     description="Numero totale di battaglie del giocatore",
     *                     example=75
     *                 ),
     *                @OA\Property(
     *                    property="helmetId",
     *                   type="integer",
     *                   description="ID del casco equipaggiato dal giocatore",
     *                   example=1
     *                ),
     *                @OA\Property(
     *                   property="runeId",
     *                  type="integer",
     *                  description="ID della runa equipaggiata dal giocatore",
     *                  example=2
     *                ),
     *                @OA\Property(
     *                   property="weaponId",
     *                  type="integer",
     *                 description="ID dell'arma equipaggiata dal giocatore",
     *                  example=3
     *                ),
     *                @OA\Property(
     *                   property="currentHealth",
     *                  type="integer",
     *                 description="Salute attuale del giocatore",
     *                  example=10
     *               )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Giocatore aggiornato con successo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Player updated successfully"),
     *             @OA\Property(property="player", type="object", 
     *                 @OA\Property(property="id", type="integer", example=22),
     *                 @OA\Property(property="name", type="string", example="john doe"),
     *                 @OA\Property(property="level", type="integer", example=10),
     *                 @OA\Property(property="experienceCollected", type="integer", example=1000),
     *                 @OA\Property(property="nWonBattles", type="integer", example=50),
     *                 @OA\Property(property="nBattles", type="integer", example=75),
     *                 @OA\Property(property="currentHealth", type="integer", example=10),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Giocatore non trovato",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Player not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $player = Player::find($id);

        if (!$player) {
            return response()->json([
                "message" => "Player not found",
            ], 404);
        }

        $request->validate([
            "name" => "string|max:255|unique:players,name," . $id,
            "level" => "integer|min:1",
            "experienceCollected" => "integer|min:0",
            "nWonBattles" => "integer|min:0",
            "nBattles" => "integer|min:0",
            "helmetId" => "integer|exists:equippableItems,id",
            "runeId" => "integer|exists:equippableItems,id",
            "weaponId" => "integer|exists:equippableItems,id",
            "currentHealth" => "integer|min:0",
        ]);

        // get the helmet,rune and weapon and checks that they are not already owned by another player
        if ($request->helmetId) {
            if(self::checkEquippableItem($request->helmetId, EquippableItemType::ARMOR)){
                return response()->json([
                    "message" => "Helmet already owned by another player or the item is not an helmet",
                ], 400);
            }
        }

        if ($request->runeId) {
            if(self::checkEquippableItem($request->runeId, EquippableItemType::RUNE)){
                return response()->json([
                    "message" => "Rune already owned by another player or the item is not a rune",
                ], 400);
            }
        }

        if ($request->weaponId) {
            if(self::checkEquippableItem($request->weaponId, EquippableItemType::WEAPON)){
                return response()->json([
                    "message" => "Weapon already owned by another player or the item is not a weapon",
                ], 400);
            }
        }

        $player->update([
            "name" => $request->name ?? $player->name,
            "level" => $request->level ?? $player->level,
            "experienceCollected" => $request->experienceCollected ?? $player->experienceCollected,
            "nWonBattles" => $request->nWonBattles ?? $player->nWonBattles,
            "nBattles" => $request->nBattles ?? $player->nBattles,
            "maxLevel" => $request->level > $player->maxLevel ? $request->level : $player->maxLevel,
            "helmetId" => $request->helmetId ?? $player->helmetId,
            "runeId" => $request->runeId ?? $player->runeId,
            "weaponId" => $request->weaponId ?? $player->weaponId,
            "currentHealth" => $request->currentHealth ?? $player->currentHealth,
        ]);

        return response()->json([
            "message" => "Player updated successfully",
            "player" => $player,
        ], 200);
    }

    private function checkEquippableItem($itemId, $type)
    {
        $item = \App\Models\EquippableItem::find($itemId);
        if ($item && $item->ownerId != null && $item->blueprint->type == $type) {
            return true;
        }
        return false;
    }

   /**
 * @OA\Get(
 *     path="/api/league",
 *     summary="Get player ranking and league list",
 *     description="Restituisce la lista ordinata dei giocatori per win rate e la posizione del player specificato.",
 *     operationId="getLeagueList",
 *     tags={"Player"},
 *     @OA\Parameter(
 *         name="playerId",
 *         in="query",
 *         required=true,
 *         description="ID del giocatore di cui calcolare la posizione",
 *         @OA\Schema(type="integer", example=42)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Classifica restituita con successo",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="players",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Mario"),
 *                     @OA\Property(property="nBattles", type="integer", example=50),
 *                     @OA\Property(property="nWonBattles", type="integer", example=30),
 *                     @OA\Property(property="win_rate", type="number", format="float", example=0.6)
 *                 )
 *             ),
 *             @OA\Property(property="position", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errore di validazione",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="playerId",
 *                     type="array",
 *                     @OA\Items(type="string", example="The selected playerId is invalid.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function getLeagueList(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(
            [
                "playerId" => "required|integer|exists:players,id",
            ]
            );

        $players = LeagueController::calculatePositions();

            // now get the player position in the list
        $position = $players->search(function ($item) use ($request) {
            return $item->id == $request->playerId;
        });

        if ($position !== false) {
    $position += 1; // perché è 0-based
} else {
    $position = null; // non trovato
}
        return response()->json([
            "players" => $players,
            "position" => $position,
        ], 200);
    }
}
