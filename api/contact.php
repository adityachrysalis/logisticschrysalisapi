<?php
require_once './config/db.php';

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $required_fields = ['first_name', 'last_name', 'email', 'message'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "$field is required."]);
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO contacts (first_name, last_name, company, phone, email, message) 
            VALUES (:first_name, :last_name, :company, :phone, :email, :message)
        ");
        $stmt->execute([
            ':first_name' => htmlspecialchars($input['first_name']),
            ':last_name' => htmlspecialchars($input['last_name']),
            ':company' => htmlspecialchars($input['company'] ?? ''),
            ':phone' => htmlspecialchars($input['phone'] ?? ''),
            ':email' => htmlspecialchars($input['email']),
            ':message' => htmlspecialchars($input['message'])
        ]);
        http_response_code(200);
        echo json_encode(["message" => "Thank you, we will reach out to you soon."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to send message: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method."]);
}
?>
