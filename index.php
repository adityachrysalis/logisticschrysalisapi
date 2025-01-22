<?php
$request = $_GET['for'] ?? '';

// Set CORS headers for both preflight (OPTIONS) and actual requests (e.g., POST)
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); 
    exit;
}

// Map requests to different files
switch ($request) {
    case 'contact':
        require_once __DIR__ . '/api/contact.php';
        break;
    case 'send_otp':
        require_once __DIR__ . '/api/send_otp.php';
        break;
    case 'verify_otp':
        require_once __DIR__ . '/api/verify_otp.php';
        break;
    case 'brand_list':
        require_once __DIR__ . '/api/brand_list.php';
        break;
    case 'brand_data':
        require_once __DIR__ . '/api/brand_data.php';
        break;
    case 'register_brand':
        require_once __DIR__ . '/api/register_brand.php';
        break;
    case 'brand_apiurl':
        require_once __DIR__ . '/api/brand_apiurl.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        break;
}
