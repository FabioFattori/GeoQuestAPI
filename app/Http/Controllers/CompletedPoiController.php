<?php

namespace App\Http\Controllers;

use App\Models\CollectedPoi;
use Illuminate\Http\Request;

class CompletedPoiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/collectedPois/getAll",
     *     summary="Restituisce tutti i POI raccolti da un giocatore",
     *     tags={"CompletedPois"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="query",
     *         description="ID del giocatore",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista dei POI raccolti",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The level field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errore di validazione"
     *     )
     * )
     */

    public function getAll(Request $request)
    {
        $request->validate([
            'playerId' => 'required|integer|exists:players,id',
        ]);

        $completedPois = CollectedPoi::where('playerId', $request->playerId)->get();

        return response()->json($completedPois);
    }
    /**
     * @OA\Post(
     *     path="/api/collectedPois/create",
     *     summary="Crea o aggiorna un POI raccolto da un giocatore",
     *     tags={"CompletedPois"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"playerId", "latitude", "longitude"},
     *             @OA\Property(property="playerId", type="integer", example=1),
     *             @OA\Property(property="latitude", type="number", format="float", example=45.4642),
     *             @OA\Property(property="longitude", type="number", format="float", example=9.1900)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="POI raccolto creato o aggiornato",
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if (
            CollectedPoi::where('playerId', $request->playerId)
                ->where('latitude', $request->latitude)
                ->where('longitude', $request->longitude)
                ->exists()
        ) {
            // if exists, update the updated_at timestamp
            $completedPoi = CollectedPoi::where('playerId', $request->playerId)
                ->where('latitude', $request->latitude)
                ->where('longitude', $request->longitude)
                ->first();
            $completedPoi->touch(); // Update the updated_at timestamp
            $completedPoi->save();
        } else {
            $completedPoi = CollectedPoi::create([
                'playerId' => $request->playerId,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);


        }

        return response()->json($completedPoi, 201);
    }
}
