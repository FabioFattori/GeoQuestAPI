<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get helloWorld",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    function index(Request $request)
    {
        return response()->json([
            'message' => 'Hello, World!'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Create a new user",
     *     description="Creates a new user with an email and password",
     *     operationId="createUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password", "playerName"},
     *                 @OA\Property(
     *                     property="playerName",
     *                     type="string",
     *                     description="User's player name",
     *                     example="Player123"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User's email",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User's password",
     *                     example="Password123!"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", type="string"),
     *             @OA\Property(property="token", type="string", example="csrf_token_value")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function create(Request $request) : \Illuminate\Http\JsonResponse
    {
        // Validazione dei dati in entrata
        $request->validate([
            'playerName'=> ['required','string','max:255'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Creazione del nuovo utente
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'playerId' => PlayerController::_create($request)->player->id,
        ]);

        if ($user) {
            // Risposta JSON
            return response()->json([
                'message' => 'User created successfully',
                'user' => json_encode($user),
                'token' => Crypt::encryptString(csrf_token()),
            ]);
        } else {
            return response()->json([
                'message'=> 'User creation failed',
                'errors' => [
                    'ERROR' => ['User creation failed'],
                ],
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/user/all",
     *   summary="Get all users",
     *   description="Returns a list of all users",
     *   operationId="getAllUsers",
     *  tags={"User"},
     *   @OA\Response(
     *       response=200,
     *      description="A list of users",
     *      @OA\JsonContent(
     *           type="array",
     *          @OA\Items(
     *              type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *              )
     *          )
     *       )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function getAll(Request $request) : \Illuminate\Http\JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }
}
