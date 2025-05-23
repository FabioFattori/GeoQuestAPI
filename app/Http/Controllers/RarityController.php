<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RarityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rarities",
     *     summary="Get all rarities",
     *     description="Returns a list of all rarities",
     *     operationId="getAllRarities",
     *     tags={"Rarities"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of rarities",
     *         @OA\JsonContent(type="string", example="rarities list stringify"),
     *     ),
     * )
     */
    public function getAll()
    {
        return response()->json( \App\Models\Rarity::all());
    }
}
