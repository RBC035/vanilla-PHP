<?php
namespace App\Helpers;

class Response {
    public static function json($data = [], $message = "Success", $code = 200) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($code);
        
        echo json_encode([
            "status"  => "success",
            "message" => $message,
            "data"    => $data
        ]);
        exit;
    }

    public static function error($message = "An error occurred", $code = 400, $details = null) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($code);
        
        $response = [
            "status"  => "error",
            "message" => $message
        ];

        if ($details) {
            $response["details"] = $details;
        }

        echo json_encode($response);
        exit;
    }
}