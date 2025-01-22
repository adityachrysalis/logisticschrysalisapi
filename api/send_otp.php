<?php
require_once './config/db.php';
require './vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email address."]);
        exit;
    }

    try {
        // Check if the email exists in the 'team' table
        $query = "SELECT email, name, id FROM lg_users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

              // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $user['name'];
            $id = $user['id'];

            // Generate OTP
            $otp = rand(100000, 999999);

            // Save OTP temporarily
            if (!file_exists('otps')) {
                mkdir('otps', 0777, true);
            }
            // file_put_contents("otps/$email.txt", $otp);
            $data = json_encode(['otp' => $otp, 'name' => $name, 'id' => $id]);
            file_put_contents("otps/$email.txt", $data);



            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'adityaingale1814@gmail.com'; // Your Gmail address
                $mail->Password = 'xbtvpzrbiveehwyu';   // Your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
        
                $mail->setFrom('mail@chrysalislogistics.com', 'Chrysalis Logistics');
                $mail->addAddress($email);
        
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Login';
                $mail->Body = "
                                <!DOCTYPE html>
                                <html lang='en'>
                                <head>
                                    <meta charset='UTF-8'>
                                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                    <title>OTP for Login</title>
                                    <style>
                                        .email-container {
                                            width: 100%;
                                            background: #ffffff;
                                            border-radius: 10px;
                                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                                        }
                                        .header {
                                            background: #9999FF;
                                            padding: 20px 0;
                                            text-align: center;
                                            border-radius: 10px 10px 0 0;
                                        }
                                       
                                        .content {
                                            padding: 20px;
                                            text-align: center;
                                        }
                                        .otp-box {
                                            font-size: 24px;
                                            font-weight: bold;
                                            color: #9999FF;
                                            padding: 10px 20px;
                                            border-radius: 5px;
                                            background-color: #f3f4f6;
                                            margin: 20px 0;
                                        }
                                        .footer {
                                            text-align: center;
                                            font-size: 12px;
                                            color: #6c6c6c;
                                            padding: 10px;
                                        }
                                        a {
                                            color: #9999FF;
                                            text-decoration: none;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class='email-container'>
                                        <div class='header'>
                                            <h1 style='color: #fff;'>Chrysalis Logistics</h1>
                                        </div>
                                        <div class='content'>
                                            <h2>Hi, $name</h2>
                                            <h3>Your OTP for Login</h3>
                                            <p>We received a request to log into your account. Please use the following OTP to complete your login process:</p>
                                            <div class='otp-box'>$otp</div>
                                            <p>If you didn't request this, please ignore this email.</p>
                                        </div>
                                        <div class='footer'>
                                            <p>Thank you for using Chrysalis Logistics!</p>
                                            <p>&copy; 2025 Chrysalis Logistics. All rights reserved.</p>
                                        </div>
                                    </div>
                                </body>
                                </html>";

        
                if ($mail->send()) {
                    echo json_encode(["success" => true, "message" => "OTP sent successfully."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to send OTP."]);
                }
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Mail error: " . $e->getMessage()]);
            }

        } else {
            echo json_encode(["success" => false, "message" => "User not found in the system."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
