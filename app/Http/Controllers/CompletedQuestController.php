<?php

namespace App\Http\Controllers;

use App\Models\CompletedQuest;
use Illuminate\Http\Request;

class CompletedQuestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/completedQuests/getAll",
     *     summary="Restituisce tutte le missioni completate da un giocatore",
     *     tags={"CompletedQuests"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="query",
     *         description="ID del giocatore",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista delle missioni completate",
     * @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The level field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errore di validazione"
     *     )
     * )
     *
     */

    public function getAll(Request $request)
    {
        $request->validate([
            'playerId' => 'required|integer|exists:players,id',
        ]);

        $completedQuests = CompletedQuest::where('playerId', $request->playerId)->get();

        return response()->json($completedQuests);
    }

    /**
     * @OA\Post(
     *     path="/api/completedQuests/create",
     *     summary="Crea una nuova missione completata per un giocatore",
     *     tags={"CompletedQuests"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"playerId", "name"},
     *             @OA\Property(property="playerId", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Missione del Drago")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Missione completata creata",
     * @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The level field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errore di validazione"
     *     )
     * )
     */

    public function create(Request $request)
    {
        $request->validate([
            'playerId' => 'required|integer|exists:players,id',
            'name' => 'required|string|max:255',
        ]);

        $completedQuest = CompletedQuest::create([
            'playerId' => $request->playerId,
            'name' => $request->name,
        ]);

        return response()->json($completedQuest, 201);
    }
}
