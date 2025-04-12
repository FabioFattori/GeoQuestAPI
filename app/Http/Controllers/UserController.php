<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

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
     *             @OA\Property(property="player", type="string"),
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
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validazione dei dati in entrata
        $request->validate([
            'playerName' => ['required', 'string', 'max:255'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $playerCreationResponse = PlayerController::_create($request);

        $player = $playerCreationResponse->getData(true)["player"];
        $id = $player["id"];

        // Creazione del nuovo utente
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'playerId' => $id,
        ]);

        $userToReturn = [
            'id' => $user->id,
            'email' => $user->email,
            'player' => $user->playerId,
        ];

        if ($user) {
            // Risposta JSON
            return response()->json([
                'message' => 'User created successfully',
                'user' => $userToReturn,
                'token' =>  $user->createToken("userToken",["Auth"])->plainTextToken,
                'player' => Player::getPlayerToReturnById($id),
            ]);
        } else {
            return response()->json([
                'message' => 'User creation failed',
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
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Get user by ID",
     *     description="Returns a user by ID",
     *     operationId="getUserById",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(
     *            type="string",example="User string",)
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */
    public function getById(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::find($request->id);
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Delete a user",
     *     description="Deletes a user by ID",
     *     operationId="deleteUser",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/user/logout",
     *     summary="Logout user",
     *     description="Logs out the user and invalidates the session",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     ),
     * )
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/login",
     *     summary="Login User",
     *     description="Autentica un utente esistente utilizzando email e password. Restituisce un token di autenticazione se la login ha successo.",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email dell'utente",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password dell'utente",
     *                     example="securePassword123"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login effettuato con successo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="token", type="string", description="Token di autenticazione")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenziali non valide",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validazione dei dati in entrata
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Autenticazione dell'utente
        if (auth()->attempt($request->only('email', 'password'))) {
            $user = auth()->user();
            
            return response()->json([
                'message' => 'User logged in successfully',
                'token' => $user->createToken("userToken",["Auth"])->plainTextToken,
                'player' => Player::getPlayerToReturnById($user->playerId), JSON_UNESCAPED_UNICODE,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'playerId' => $user->playerId,
                ],
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    }

    public function checkToken (Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => ["email","required","string"],
            'token'=> ['required','string'],
            ]);

            $user = User::where('email', $request->email)->first();
            if (empty($user)) {
                return response()->json([
                    'message'=> 'not found',
                    ],404);
                }
                if ($user->token != $request->token) {
                    return response()->json([
                        'message'=> 'unauthenticated',
                    ],403);
                }

                return response()->json([
                    "message" => "logged in"
                ],200);
    }
}
