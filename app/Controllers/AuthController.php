<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Response;
use Firebase\JWT\JWT;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['username']) || empty($data['password'])) {
            Response::error("Username and password are required", 400);
        }

        $user = $this->userModel->findByUsername($data['username']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::error("Invalid username or password", 401);
        }

        $secretKey = getenv('JWT_SECRET');
        $issuedAt = time();
        $expire = $issuedAt + (int)getenv('JWT_EXPIRATION');

        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'sub'  => $user['user_id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        $jwt = JWT::encode($payload, $secretKey, alg: 'HS256');

        Response::json([
            "token" => $jwt,
            "type" => "Bearer",
            "expires_in" => $expire,
            "user" => [
                "userId" => $user['user_id'],
                "username" => $user['username'],
                "role" => $user['role']
            ]
        ], "Login successful");
    }

    public function getByUsername()
    {
        if (empty($_GET['username'])) {
            Response::error("Username parameter is required", 400);
        }

        $user = $this->userModel->findByUsername($_GET['username']);

        if (!$user) {
            Response::error("User not found", 404);
        }

        // Remove sensitive fields
        // unset($user['password']);

        Response::json($user, "User retrieved successfully");
    }

    public function changePassword($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['currentPassword']) || empty($data['newPassword'])) {
            Response::error("Current password and new password are required", 400);
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            Response::error("User not found", 404);
        }

        // Verify old password
        // if (!password_verify($data['currentPassword'], $user['password'])) {
        //     Response::error("Current password is incorrect", 401);
        // }

        // Prevent using the same password
        // if (password_verify($data['newPassword'], $user['password'])) {
        //     Response::error("New password cannot be the same as old password", 400);
        // }

        // Hash new password
        $hashedPassword = password_hash($data['newPassword'], PASSWORD_BCRYPT);

        $updated = $this->userModel->updatePassword($id, $hashedPassword);

        if (!$updated) {
            Response::error("Failed to change password", 500);
        }

        Response::json(null, "Password changed successfully");
    }
}
