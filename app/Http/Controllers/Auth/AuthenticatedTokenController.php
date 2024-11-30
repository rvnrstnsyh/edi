<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedTokenController extends Controller
{
    public function me()
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

    public function refresh()
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

    public function logout()
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
