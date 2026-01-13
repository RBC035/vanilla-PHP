<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Helpers\Response;

class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        
        // Look in standard headers, then fallback to $_SERVER variables
        $authHeader = $headers['Authorization'] ?? 
                      $headers['authorization'] ?? 
                      $_SERVER['HTTP_AUTHORIZATION'] ?? 
                      $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 
                      null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error("Unauthorized: No token provided", 401);
        }

        $token = $matches[1];

        try {
            $secretKey = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            Response::error("Unauthorized: " . $e->getMessage(), 401);
        }
    }

    public static function checkRole($allowedRoles) {
        $user = self::authenticate();
        $userRole = $user['role'] ?? null;

        if (!$userRole || !in_array($userRole, (array)$allowedRoles)) {
            Response::error("Forbidden: You do not have the required permissions", 403);
        }

        return $user;
    }
}