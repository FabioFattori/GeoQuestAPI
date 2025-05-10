<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\EquippableItemBlueprint;
use App\Models\EquippableItem;
use App\Models\Rarity;

class EquippableItemsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/equippableItems",
     *     summary="Get all equippable items",
     *     description="Returns a list of all equippable items",
     *     operationId="getAllEquippableItems",
     *     tags={"EquippableItems"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of equippable items",
     *         @OA\JsonContent(type="string", example="equippableItems list stringify"),
     *     ),
     * )
     */
    public function getAll()
    {
        return response()->json(EquippableItem::all());
    }

    /**
     * @OA\Get(
     *     path="/api/equippableItems/{id}",
     *     summary="Get equippable item by ID",
     *     description="Returns a single equippable item by its ID",
     *     operationId="getEquippableItemById",
     *     tags={"EquippableItems"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equippable item",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equippable item details",
     *         @OA\JsonContent(type="string", example="equippableItem details stringify"),
     *     ),
     * )
     */
    public function getById($id)
    {
        return response()->json(EquippableItem::find($id));
    }

    /**
     * 
     * @OA\Post(
     *     path="/api/equippableItems",
     *     summary="Create a random equippable item",
     *     description="Creates a random equippable item based on the given level and ownerId",
     *     operationId="createRandomItem",
     *     tags={"EquippableItems"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"level", "ownerId"},
     *             @OA\Property(property="level", type="integer", example=5),
     *             @OA\Property(property="ownerId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="rarityId", type="integer", example=1),
     *             @OA\Property(property="blueprintId", type="integer", example=1),
     *             @OA\Property(property="ownerId", type="integer", example=1),
     *             @OA\Property(property="randomFactor", type="number", format="float", example=0.5),
     *             @OA\Property(property="damage", type="number", format="float", example=10.5),
     *             @OA\Property(property="health", type="number", format="float", example=20.5),
     *             @OA\Property(property="getRarity", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Rare"),
     *                 @OA\Property(property="hexColor", type="string", example="#FF0000"),
     *                 @OA\Property(property="multiplier", type="number", format="float", example=1.5),
     *                 @OA\Property(property="levelRequiredToDrop", type="integer", example=1)
     *             ),
     *             @OA\Property(property="getBlueprint", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sword"),
     *                 @OA\Property(property="description", type="string", example="A sharp sword"),
     *                 @OA\Property(property="type", type="string", example="weapon"),
     *                 @OA\Property(property="baseDamage", type="integer", example=10),
     *                 @OA\Property(property="baseHealth", type="integer", example=20),
     *                 @OA\Property(property="requiredLevel", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No available rarity or blueprint for the given level")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     * )
     */
    public function createRandomItem(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|integer|min:1',
            'ownerId' => 'integer|exists:players,id',
        ]);

        $randomRarity = Rarity::getPossibleRaritiesGivenLevel($request->level)->random();
        $randomBlueprint = EquippableItemBlueprint::getPossibleBlueprintsGivenLevel($request->level)->random();

        if (!$randomRarity || !$randomBlueprint) {
            return response()->json(['error' => 'No available rarity or blueprint for the given level'], 400);
        }
        $equippableItem = EquippableItemBlueprint::createEquippableItem($randomRarity->id, $randomBlueprint->id, $request->ownerId);

        return response()->json(
            $equippableItem,
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/inventory",
     *     summary="Recupera l'inventario del giocatore",
     *     description="Ritorna gli oggetti equipaggiabili di un giocatore filtrati per tipo (weapon, armor, rune).",
     *     operationId="getInventory",
     *     tags={"EquippableItems"},
     *     @OA\Parameter(
     *         name="ownerId",
     *         in="query",
     *         description="ID del giocatore (owner)",
     *         required=true,
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Tipo di oggetto equipaggiabile",
     *         required=true,
     *         @OA\Schema(type="integer", enum={1,2,3}, example="1 = weapon, 2 = armor, 3 = rune")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista di oggetti equipaggiabili",
     *         @OA\JsonContent(
     *             type="string",
     *             example="Lista di oggetti equipaggiabili in formato JSON")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errore di validazione"
     *     )
     * )
     */
    public function getInventory(Request $request)
    {
        $request->validate([
            'ownerId' => 'required|integer|exists:players,id',
            'type' => 'required|integer|in:1,2,3',
        ]);
        $player = Player::find($request->ownerId);

        $equippableItems = EquippableItem::with(['blueprint', 'rarity'])
            ->where('ownerId', $request->ownerId)
            ->where('id', '!=', $player->helmetId)
            ->where('id', '!=', $player->weaponId)
            ->where('id', '!=', $player->runeId)
            ->whereHas('blueprint', function ($query) use ($request) {
                $query->where('type', (int) $request->type);
            })
            ->get();
        return response()->json($equippableItems->values());
    }

    /**
     * @OA\Put(
     *     path="/api/equippableItems/{id}",
     *     summary="Update an equippable item",
     *     description="Updates an existing equippable item",
     *     operationId="updateEquippableItem",
     *     tags={"EquippableItems"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equippable item to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ownerId"},
     *             @OA\Property(property="ownerId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equippable item updated successfully",
     *         @OA\JsonContent(type="string", example="equippableItem updated stringify"),
     *     ),
     * )
     */
    public function updateEquippableItem(Request $request, $id)
    {
        $request->validate([
            'ownerId' => 'required|integer|exists:players,id',
        ]);

        $equippableItem = EquippableItem::find($id);
        if (!$equippableItem) {
            return response()->json(['error' => 'Equippable item not found'], 404);
        }

        $equippableItem->update($request->only(['ownerId']));

        return response()->json($equippableItem);
    }
}
