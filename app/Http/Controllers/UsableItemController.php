<?php

namespace App\Http\Controllers;

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
        $userId = $request->input('ownerId');
        if (!$userId) {
            return response()->json(['error' => 'ownerId is required'], 400);
        }
        $player = Player::find($userId);
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        return response()->json($player->getUsableItems()->get(), 200);
    }

        /**
     * @OA\Post(
     *     path="/api/usableItems/createRandomUsableItem",
     *     summary="Crea un oggetto utilizzabile casuale",
     *     description="Genera un oggetto utilizzabile casuale in base al livello specificato. Se viene fornito ownerId, assegna l'oggetto al giocatore. Altrimenti, l'oggetto non sarà assegnato a nessuno.",
     *     operationId="createRandomUsableItem",
     *     tags={"UsableItems"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dati per creare un oggetto utilizzabile casuale",
     *         @OA\JsonContent(
     *             required={"level"},
     *             @OA\Property(property="level", type="integer", description="Livello del giocatore per determinare la rarità dell'oggetto"),
     *             @OA\Property(property="ownerId", type="integer", nullable=true, description="ID del giocatore a cui assegnare l'oggetto (opzionale)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Oggetto utilizzabile casuale creato con successo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Random usable item created successfully"),
     *             @OA\Property(property="usableItem", type="object", 
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="healthRecovery", type="integer"),
     *                 @OA\Property(property="imagePath", type="string"),
     *                 @OA\Property(property="rarityId", type="integer"),
     *                 @OA\Property(property="ownerId", type="integer", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errore di validazione: campo richiesto mancante o non valido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The level field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Giocatore non trovato o nessun oggetto utilizzabile disponibile per la rarità specificata.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Player not found"),
     *         )
     *     )
     * )
     */

    public function createRandomItem(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|integer',
            'ownerId' => 'nullable|integer',
        ]);
    
        $player = null;
        if ($request->filled('ownerId')) {
            $player = Player::find($request->input('ownerId'));
    
            if (!$player) {
                return response()->json(['error' => 'Player not found'], 404);
            }
        }
    
        // Estrai una rarità compatibile col livello passato
        $randomRarity = Rarity::getPossibleRaritiesGivenLevel($request->input('level'))->random();
    
        // Prendi un UsableItem casuale con quella rarità
        $randomUsableItemTemplate = UsableItem::where('rarityId', $randomRarity->id)
            ->inRandomOrder()
            ->first();
    
        if (!$randomUsableItemTemplate) {
            return response()->json(['error' => 'No usable items found for the given rarity'], 404);
        }
    
        // Crea una nuova istanza di usableItem, assegnandola eventualmente al player
        $newUsableItem = UsableItem::create([
            'name' => $randomUsableItemTemplate->name,
            'description' => $randomUsableItemTemplate->description,
            'healthRecovery' => $randomUsableItemTemplate->healthRecovery,
            'imagePath' => $randomUsableItemTemplate->imagePath,
            'rarityId' => $randomUsableItemTemplate->rarityId,
            'ownerId' => $player ? $player->id : null,
        ]);
    
        return response()->json([
            'message' => 'Random usable item created successfully',
            'usableItem' => $newUsableItem,
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/usableItems/{id}",
     *     summary="Elimina un oggetto utilizzabile",
     *     description="Elimina un oggetto utilizzabile specificato dall'ID.",
     *     operationId="deleteUsableItem",
     *     tags={"UsableItems"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dell'oggetto utilizzabile da eliminare",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Oggetto utilizzabile eliminato con successo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Oggetto utilizzabile non trovato",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $usableItem = UsableItem::find($id);
        if (!$usableItem) {
            return response()->json(['message' => 'Usable item not found'], 404);
        }
        $usableItem->delete();
        return response()->json(['message' => 'Usable item deleted successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/usableItems/{id}",
     *     summary="Get a usable item by ID",
     *     description="Retrieve a specific usable item by its ID.",
     *     operationId="getUsableItemById",
     *     tags={"UsableItems"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the usable item to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usable item found",
     * @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usable item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item not found")
     *         )
     *     )
     * )
     */
    public function getById($id) : JsonResponse
    {
        $usableItem = UsableItem::find($id);
        if (!$usableItem) {
            return response()->json(['message' => 'Usable item not found'], 404);
        }
        return response()->json($usableItem, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/usableItems/{id}",
     *     summary="Update a usable item",
     *     description="Update the details of a specific usable item by its ID.",
     *     operationId="updateUsableItem",
     *     tags={"UsableItems"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the usable item to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Details to update the usable item",
     *         @OA\JsonContent(
     *             @OA\Property(property="ownerId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usable item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usable item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usable item not found")
     *         )
     *    )
        * )
    */
     
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'ownerId' => 'integer|exists:players,id',
        ]);
        $usableItem = UsableItem::find($id);
        if (!$usableItem) {
            return response()->json(['message' => 'Usable item not found'], 404);
        }
        $usableItem->update($request->only(['ownerId']));
        return response()->json($usableItem, 200);
    }

}
