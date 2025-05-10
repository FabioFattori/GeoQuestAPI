<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\League;
use App\Models\EquippableItem;
use App\Models\EquippableItemBlueprint;
use App\Models\Rarity;
use Illuminate\Http\JsonResponse;

class LeagueController extends Controller
{
    public static function calculatePositions(){
        return Player::select('players.*')
        ->selectRaw('IF(nBattles >= 10, nWonBattles / nBattles, -1) as win_rate')
        ->orderByDesc('win_rate')
        ->orderByDesc('nBattles')
        ->get();
    }

    public static function getPlayerPosition($playerId)
    {
        // Retrieve the player's position in the league
        $leaguePosition = self::calculatePositions()
            ->where('playerId', $playerId)
            ->first();
        if ($leaguePosition) {
            return $leaguePosition->position;
        }
        return null;
    }

    private function isInThisWeek($date)
    {
        $now = now();
        $updatedAt = $league->updated_at;

        $currentWeek = $now->weekOfYear;
        $currentYear = $now->year;

        $updatedWeek = $updatedAt->weekOfYear;
        $updatedYear = $updatedAt->year;

        $isThisWeek = ($updatedWeek === $currentWeek && $updatedYear === $currentYear);
        $isLastWeek = ($updatedWeek === ($currentWeek - 1) && $updatedYear === $currentYear);

        // Per gestione del passaggio d'anno (es. 2025 W1 vs 2024 W52)
        if (!$isThisWeek && !$isLastWeek && $currentWeek === 1 && $updatedWeek >= 52 && $updatedYear === ($currentYear - 1)) {
            $isLastWeek = true;
        }
        return $isThisWeek;
    }

        /**
     * @OA\Get(
     *     path="/api/league/canGetReward",
     *     summary="Verifica se il giocatore può riscattare il premio settimanale",
     *     description="Controlla se il giocatore ha già ricevuto un premio questa settimana o se è idoneo per riceverlo.",
     *     operationId="canGetReward",
     *     tags={"League"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="query",
     *         required=true,
     *         description="ID del giocatore",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Il giocatore può ricevere il premio",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Player can have the reward"),
     *             @OA\Property(property="position", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=413,
     *         description="Il giocatore ha già ricevuto il premio questa settimana",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reward already claimed.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=414,
     *         description="Il giocatore non può ricevere il premio (prima richiesta registrata)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Player cannot have the reward"),
     *             @OA\Property(property="position", type="integer", example=20)
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
    public function canGetReward(Request $request)
    {
        // Validate the request
        $request->validate([
            'playerId' => 'required|integer|exists:players,id',
        ]);

        $league = League::where('playerId', $request->playerId)->first();
        $position = self::getPlayerPosition($request->playerId);
        if(
            !$league
        ){
            League::create([
                'playerId' => $request->playerId,
                'position' => $position,
            ]);
            return response()->json(
                [
                    'message' => 'Player cannot have the reward',
                    'position' => $position,
                ],414
            );
        }

        if($this->isInThisWeek($league->updated_at)){
            return response()->json(['message' => 'Reward already claimed.'], 413);
        }
        
        return response()->json(
            [
                'message' => 'Player can have the reward',
                'position' => $position,
            ], 200
        );
    }

    private function createReward($position,$playerId): EquippableItem{
        $player = Player::find($playerId);
        $randomBlueprint = EquippableItemBlueprint::getPossibleBlueprintsGivenLevel($request->level)->random();
        $chosenRarity = null;
        switch ($position) {
            case 1:
                $chosenRarity = Rarity::where('rarity', 'Legendary')->first();
                break;
            case 2 || 3:
                $chosenRarity = Rarity::where('rarity', 'Epic')->first();
                break;
            default:
                if ($position <= 20) {
                    $chosenRarity = Rarity::where('rarity', 'Rare')->first();
                } else {
                    $chosenRarity = Rarity::where('rarity', 'Common')->first();
                }
                break;
        }

        return EquippableItemBlueprint::createEquippableItem($chosenRarity->id, $randomBlueprint->id, $playerId);
    }

        /**
     * @OA\Post(
     *     path="/api/league/getReward",
     *     summary="Riscatta il premio settimanale del giocatore",
     *     description="Ritorna un oggetto equipaggiabile generato in base alla posizione settimanale del giocatore.",
     *     operationId="getReward",
     *     tags={"League"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"playerId"},
     *             @OA\Property(property="playerId", type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio riscattato con successo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reward claimed successfully."),
     *             @OA\Property(
     *                 property="reward",
     *                 type="object",
     *                 description="Oggetto equipaggiabile generato",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="rarity_id", type="integer", example=2),
     *                 @OA\Property(property="blueprint_id", type="integer", example=5),
     *                 @OA\Property(property="player_id", type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Il giocatore non è stato trovato nella lega",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Player not found in league."),
     *             @OA\Property(property="reward", type="string", example=null)
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
    public function getReward(Request $request) : JsonResponse
    {
        // Validate the request
        $request->validate([
            'playerId' => 'required|integer|exists:players,id',
        ]);

        $league = League::where('playerId', $request->playerId)->first();
        if($league){
            $league->updated_at = now();
            $league->save();
            $position = self::getPlayerPosition($request->playerId);
            return response()->json([
                'message' => 'Reward claimed successfully.',
                'reward' => $this->createReward($position,$request->playerId),
            ], 200);
        }
        return response()->json([
            'message' => 'Player not found in league.',
            'reward' => null,
        ], 404);
    }
}
