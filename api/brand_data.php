<?php
require_once './config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);
    $brand_id = $data['id'] ?? null;

    try {
        if (!empty($brand_id)) {
            // Use a prepared statement to prevent SQL injection
            $query = "SELECT brand_name, brand_image, brand_mobile, weburl, api_url, brand_email, status, id FROM lg_brands WHERE id = :brand_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':brand_id', $brand_id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the record
            $brand = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($brand) {
                echo json_encode(["success" => true, "brand" => $brand]);
            } else {
                echo json_encode(["success" => false, "message" => "No brand found with the provided ID."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Brand ID is required."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
