<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$servername = "localhost";
$username = "root";
$password = "";
$database = "code_chalenge";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function processQueue($conn) {
    $sql = "SELECT * FROM queued_emails WHERE status='Waiting'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            sendEmail($row['to_email'], $row['subject'], $row['message']);
            // Update status to 'Sent'
            $updateSql = "UPDATE queued_emails SET status='Sent' WHERE id=?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $row['id']);
            $updateStmt->execute();
            $updateStmt->close();
        }
    }
}


// Mengirikan email dari proses queue
function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp-relay.brevo.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'yozanadiprada@gmail.com';
    $mail->Password = 'xsmtpsib-3adc2dc98c28de146f5d098cc91a89e321b622eb25008da4ed9f3f19b2fc0753-cVJ3nkbpN189xhBF';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('adipradayozan@gmail.com', 'Yozan');
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;

    if ($mail->send()) {
        echo 'Email sent successfully';
    } else {
        echo 'Error: ' . $mail->ErrorInfo;
    }
}

// Memproses email
processQueue($conn);

// Clear proses queue
$sql = "TRUNCATE TABLE email_queue";
$conn->query($sql);

// Close koneksi database
$conn->close();
?>
