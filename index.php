<?php
$request = $_GET['for'] ?? '';

// Set CORS headers for both preflight (OPTIONS) and actual requests (e.g., POST)
header("Access-Control-Allow-Origin: *"); // Allows any origin (you can specify a specific origin here)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allows POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allows Content-Type and Authorization headers


// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond with OK for preflight
    exit; // End the request here for OPTIONS
}

switch ($request) {
    case 'contact':
        require_once 'api/contact.php';
        break;
    case 'send_otp':
        require_once 'api/send_otp.php';
        break;
    case 'verify_otp':
        require_once 'api/verify_otp.php';
        break;
    case 'brand_list':
        require_once 'api/brand_list.php';
        break;
    case 'brand_data':
        require_once 'api/brand_data.php';
        break;
    case 'register_brand':
        require_once 'api/register_brand.php';
        break;
    case 'brand_apiurl':
        require_once 'api/brand_apiurl.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        break;
}
?>
