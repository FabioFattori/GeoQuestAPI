<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\Content(
 *             @OA\Schema(
 *                 type="object",
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
 *                     example="password123"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User created successfully"),
 *             @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
public function create(Request $request)
{
    // Validazione dei dati in entrata
    $request->validate([
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    // Creazione del nuovo utente
    $user = User::create([
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    // Risposta JSON
    return response()->json([
        'message' => 'User created successfully',
        'user' => $user
    ]);
}



}
