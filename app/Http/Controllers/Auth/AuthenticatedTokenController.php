<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="API endpoints for user authentication, authorization and token management"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class AuthenticatedTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Handle an incoming authentication request",
     *     description="Validate user email and password, return JWT token upon successful authentication",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User's registered email address",
     *                 example="chernobyl@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="User's account password",
     *                 example="secret000"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 description="JWT authentication token"
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 example="bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 description="Token expiration time in seconds"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid credentials"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Login failed"
     *             ),
     *             @OA\Property(
     *                 property="details",
     *                 type="string",
     *                 description="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */
    public function store(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/me",
     *     operationId="getCurrentUser",
     *     tags={"Authentication"},
     *     summary="Get authenticated user profile",
     *     description="Retrieve the currently authenticated user's profile information. Requires a valid Bearer Token in the Authorization header.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Header(
     *         header="Authorization",
     *         description="Bearer Token",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Chernobyl"),
     *             @OA\Property(property="email", type="string", example="chernobyl@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access - Invalid or missing Bearer Token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized: Invalid or missing token"
     *             )
     *         )
     *     )
     * )
     */
    public function show()
    {
        try {
            if (!$user = auth('api')->user()) return response()->json(['error' => 'User not found'], 404);
            return response()->json($user);
        } catch (TokenExpiredException $error) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $error) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $error) {
            return response()->json(['error' => 'Authorization token error'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     description="Generate a new JWT token using the current token. Requires a valid Bearer Token in the Authorization header.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Header(
     *         header="Authorization",
     *         description="Bearer Token",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token successfully refreshed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 description="New JWT authentication token"
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 example="bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 description="New token expiration time in seconds"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token refresh failed - Invalid or missing Bearer Token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized: Token is invalid or expired"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during token refresh",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Could not refresh token"
     *             ),
     *             @OA\Property(
     *                 property="details",
     *                 type="string",
     *                 description="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */
    public function update()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return $this->respondWithToken($token);
        } catch (TokenExpiredException $error) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $error) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $error) {
            Log::error('Token refresh error: ' . $error->getMessage());
            return response()->json([
                'error' => 'Could not refresh token',
                'details' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     summary="Log out the current user",
     *     description="Invalidate the current JWT token. Requires a valid Bearer Token in the Authorization header.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Header(
     *         header="Authorization",
     *         description="Bearer Token",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Successfully logged out"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Logout failed - Invalid or missing Bearer Token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized: Invalid or missing token"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during logout",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Logout failed"
     *             ),
     *             @OA\Property(
     *                 property="details",
     *                 type="string",
     *                 description="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */
    public function destroy()
    {
        try {
            auth('api')->logout();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $error) {
            Log::error('Logout error: ' . $error->getMessage());
            return response()->json([
                'error' => 'Logout failed',
                'details' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        try {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl', 60) * 60 // Fallback to config or default
            ]);
        } catch (Exception $error) {
            Log::error('Token response error: ' . $error->getMessage());
            return response()->json([
                'error' => 'Token generation failed',
                'details' => $error->getMessage()
            ], 500);
        }
    }
}
