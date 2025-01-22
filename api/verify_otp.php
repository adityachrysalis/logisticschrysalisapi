<?php
require_once './config/db.php';

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $otp = $data['otp'] ?? '';

    try {
        // Read the saved OTP data
        $savedOtpJson = file_get_contents("otps/$email.txt");

        if ($savedOtpJson === false) {
            echo json_encode(["success" => false, "message" => "OTP not found."]);
            exit;
        }

        // Decode the JSON data
        $savedOtpData = json_decode($savedOtpJson, true);
        if (!$savedOtpData) {
            echo json_encode(["success" => false, "message" => "Failed to read OTP data."]);
            exit;
        }

        $requestedOtp = $savedOtpData['otp'] ?? ''; // OTP
        $name = $savedOtpData['name'] ?? '';       // Name
        $id = $savedOtpData['id'] ?? '';          // ID

        // Verify the OTP
        if ($otp == $requestedOtp) {
            $token = bin2hex(random_bytes(16)); // Generate a token
            echo json_encode(["success" => true, "token" => $token, "name" => $name, "id" => $id]);
        } else {
            echo json_encode(["success" => false, "message" => $requestedOtp ]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}





