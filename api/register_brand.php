<?php
require_once './config/db.php';

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $required_fields = ['brand_name', 'brand_email'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            if('brand_email'== $field)
            {
                echo json_encode(["success" => false, "message" => "Email is required."]);
            }
            if('brand_name'== $field)
            {
                echo json_encode(["success" => false, "message" => "Brand name is required."]);
            }
            exit;
        }
    }

    $image_path = null;
    if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
        // Define the upload directory
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }

        // Validate the file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['brand_image']['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid file type. Only JPEG, PNG, and GIF are allowed."]);
            exit;
        }

        // Generate a unique filename
        $file_name = time() . "_" . basename($_FILES['brand_image']['name']);
        $target_file = $upload_dir . $file_name;

        // Move the file to the upload directory
        if (!move_uploaded_file($_FILES['brand_image']['tmp_name'], $target_file)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to upload the image."]);
            exit;
        }

        $image_path = $target_file;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO lg_brands (brand_name, brand_email, brand_mobile, weburl, api_url, brand_image) 
            VALUES (:brand_name, :brand_email, :brand_mobile, :weburl, :api_url, :brand_image)
        ");
        $stmt->execute([
            ':brand_name' => htmlspecialchars($_POST['brand_name']),
            ':brand_email' => htmlspecialchars($_POST['brand_email']),
            ':brand_mobile' => htmlspecialchars($_POST['brand_mobile'] ?? ''),
            ':weburl' => htmlspecialchars($_POST['brand_url'] ?? ''),
            ':api_url' => htmlspecialchars($_POST['api_url'] ?? ''),
            ':brand_image' => $image_path ?? ''
        ]);

        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Brand registered successfully."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to register brand: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
