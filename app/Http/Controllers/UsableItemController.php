<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\TokenVerifier;
use App\Models\Player;
use App\Models\Rarity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\UsableItem;

class UsableItemController extends Controller
{
    /**
     * @AO\Get(
     *    path="/api/usableItems/getAll",
     *   summary="Get all usable items",
     *  description="Returns a list of all usable items",
     *  operationId="getAllUsableItems",
     * tags={"UsableItems"},
     * @OA\Response(
     *        response=200,
     *       description="A list of usable items",
     *      @OA\JsonContent(type="string", example="usableItems list stringify"),
     *   ),
     * )
     */
    public function getAll(): JsonResponse
    {
        $r = TokenVerifier::verifyTokenAndRespond();
        if ($r) {
            return $r;
        }
        return response()->json(UsableItem::all(), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/usableItems/getUsableItemsOfUser",
     *     summary="Ottieni gli oggetti utilizzabili di un giocatore",
     *     description="Recupera gli oggetti utilizzabili per un giocatore specifico dato l'ID del proprietario.",
     *     operationId="getUsableItemsOfUser",
     *     tags={"UsableItems"},
     *     @OA\Parameter(
     *         name="ownerId",
     *         in="query",
     *         description="ID del giocatore di cui ottenere gli oggetti utilizzabili.",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista degli oggetti utilizzabili trovati per il giocatore.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="type", type="string"),
     *                     @OA\Property(property="quantity", type="integer")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errore nella richiesta: `ownerId` è richiesto.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="ownerId is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Giocatore non trovato con l'ID fornito.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Player not found")
     *         )
     *     )
     * )
     */
    public function getUsableItemsOfUser(Request $request): JsonResponse
    {
        $r = TokenVerifier::verifyTokenAndRespond();
        if ($r) {
            return $r;
        }
        $userId = $request->input('ownerId');
        if (!$userId) {
            return response()->json(['error' => 'ownerId is required'], 400);
        }
        $player = Player::find($userId);
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        return response()->json($player->usableItems, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/usableItems/createRandomUsableItem",
     *     summary="Crea un oggetto utilizzabile casuale per un giocatore",
     *     description="Genera e assegna un oggetto utilizzabile casuale a un giocatore in base al suo livello e alla rarità disponibile. Se l'oggetto esiste già, ne incrementa la quantità.",
     *     operationId="createRandomUsableItems",
     *     tags={"UsableItems"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dati per creare un oggetto utilizzabile casuale",
     *         @OA\JsonContent(
     *             required={"ownerId"},
     *             @OA\Property(property="ownerId", type="integer", description="ID del giocatore a cui assegnare l'oggetto utilizzabile")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Oggetto utilizzabile casuale creato e assegnato con successo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Random usable item created successfully"),
     *             @OA\Property(property="usableItem", type="object", 
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="rarityId", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errore nella richiesta: `ownerId` è richiesto.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="ownerId is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Giocatore non trovato o nessun oggetto utilizzabile trovato per la rarità specificata.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Player not found"),
     *         )
     *     )
     * )
     */
    public function createRandomItem(Request $request): JsonResponse
    {
        $r = TokenVerifier::verifyTokenAndRespond();
        if ($r) {
            return $r;
        }

        $request->validate([
            'ownerId' => 'required|integer',
        ]);

        $player = Player::find($request->input('ownerId'));
        if ($player->count() == 0) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        $player = $player->first();

        $randomRarity = Rarity::getPossibleRaritiesGivenLevel($player->level)->random();
        $randomUsableItem = UsableItem::where('rarityId', $randomRarity->id)->inRandomOrder();
        if ($randomUsableItem->count() == 0 || $randomUsableItem->count() == 0) {
            return response()->json(['error' => 'No usable items found for the given rarity'], 404);
        }
        $randomUsableItem = $randomUsableItem->first();
        $randomUsableItem = $randomUsableItem->first();

        // Check if the player already has the item
        $existingItem = $player->usableItems()->where('id', $randomUsableItem->id)->first();
        if ($existingItem) {
            // If the item already exists, increment the quantity
            $existingItem->pivot->quantity++;
            $existingItem->pivot->save();
        } else {
            // If the item doesn't exist, attach it to the player with quantity 1
            $player->usableItems()->attach($randomUsableItem->id, ['quantity' => 1]);
        }
        return response()->json([
            'message' => 'Random usable item created successfully',
            'usableItem' => $randomUsableItem,
        ], 201);
    }
}
