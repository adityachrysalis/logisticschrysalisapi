<?php
// config/db.php
$host = '139.59.36.8';
$db_name = 'chrysa59_testlogisticssystem';
$username = 'chrysa59_testslogisticsusr';
$password = 'TestLogi12*';



try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}
?>
