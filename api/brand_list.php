<?php
require_once './config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {
        // Fetch all brands from 'lg_brands' table
        $query = "SELECT brand_name, brand_image, brand_mobile, brand_email, weburl, status, id FROM lg_brands";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch all records
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($brands)) {
            echo json_encode(["success" => true, "brands" => $brands]);
        } else {
            echo json_encode(["success" => false, "message" => "No brands available in the system"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
