<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include Composer's autoloader

// Set header to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['receiver_email'])) {
        $email = filter_var($_POST['receiver_email'], FILTER_VALIDATE_EMAIL);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email address is missing']);
        exit;
    }

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';  // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'support@minionsenterprises.com'; // Your SMTP username
        $mail->Password = 'Minionsgroup@10#'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('support@minionsenterprises.com', 'Invoice ME');
        $mail->addAddress($email);  // Receiver's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Invoice Minions Enterprise';
        $mail->Body    = 'Please find attached your PDF document Invoice and Amount details.';

        // Attach the PDF file (ensure the file exists)
        $filePath = __DIR__ . '/Charges_and_Amounts.pdf';  // Saves the PDF in the current directory
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'PDF file not found']);
            exit;
        }

        // Send email
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
