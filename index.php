<?php

session_status() === PHP_SESSION_ACTIVE ?: session_start();
require 'vendor/autoload.php';
require_once 'db_conn.php';
require_once 'auth.php';

use PHPMailer\PHPMailer\PHPMailer;
use Google\Client as GoogleClient;
use Google\Service\Gmail;

$BASE_URI = "/codechalenge/";
$endpoints = array();
$requestData = array();

// Configurasi Google OAuth2 
$client = new GoogleClient();
$client->setClientId('419192020039-mbgi6qp474lg2prk33nbtk3dh73gqnsm.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/codechalenge/callback/index.php');
$client->addScope(Gmail::MAIL_GOOGLE_COM);

// mengumpulkan parameter yang datang
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $requestData = $_POST;
        break;
    case 'GET':
        $requestData = $_GET;
        break;
    case 'DELETE':
        $requestData = $_DELETE;
        break;
    case 'PUT':
    case 'PATCH':
        parse_str(file_get_contents('php://input'), $requestData);
        if (!is_array($requestData)) {
            $requestData = array();
        }
        break;
    default:
        
        break;
}

// Endpoint root
$endpoints["/"] = function (array $requestData) {
    echo json_encode("Yozan Adiprada - PHP Rest API - Code Challenge");
};

//Endpoint request token
$endpoints["gettoken"] = function (array $requestData): void {
    // Check parameter
    if (isset($_GET['action']) && $_GET['action'] === 'request_token') {
        // Redirect the user to the Google OAuth2 authorization URL
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    } else {
        echo "Invalid action";
    }
};

// Fungsi cek token
function token_cek($session) {
    
    $pdo = dbconn();
    $stmt = $pdo->prepare("SELECT access_token, time FROM sendemail_request WHERE access_token = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // JIka token tidak ada pada database
    if (!$row) {
        http_response_code(401);
        echo json_encode(array("message" => "Token not found, please check again"));
        exit;
    }

    // cek perbedaan waktu token dengan waktu sekarang
    $tokenTime = $row['time'];
    $timeZone = new DateTimeZone('Asia/Jakarta');
    $currentDateTime = new DateTime('now', $timeZone);
    $currentDateTimeString = $currentDateTime->format('Y-m-d H:i:s');
    
    $start_datetime = new DateTime($tokenTime); 
    $diff = $start_datetime->diff(new DateTime($currentDateTimeString));

    // token expired setelah 5 menit
    if ($diff->i > 5) {
        http_response_code(401);
        echo json_encode(array("message" => "Token has expired {$diff->i} minutes ago"));
        exit;
    }
}

// Endpoint untuk cek status token
$endpoints["checktoken"] = function (array $requestData) {
    // cek header
    if (!isset($_SERVER['HTTP_X_API_KEY'])) {
        http_response_code(401);
        echo json_encode(array("message" => "Authorization header is missing"));
        exit;
    }

    // Extract token dari header
    $session = $_SERVER['HTTP_X_API_KEY'];

    // Definisi fungsi token
    token_cek($session);
};

//Endpoint untuk kirim email
$endpoints["sendemail"] = function (array $requestData) use ($conn) {
    //verifikasi method yang digunakan
    if ($_SERVER['REQUEST_METHOD'] !== "POST") {
        http_response_code(405); 
        echo json_encode("Method not allowed");
        return;
    }

    // cek header untuk authorize akses
    if (!isset($_SERVER['HTTP_X_API_KEY'])) {
        http_response_code(401);
        echo json_encode(array("message" => "Authorization header is missing"));
        exit;
    }

    // Extract token dari header
    $session = $_SERVER['HTTP_X_API_KEY'];

    // Memanggil fungsi cek token
    token_cek($session);

    // Parameter atau data yg datang dari endpoint
    $to = $requestData['to'] ?? '';
    $subject = $requestData['subject'] ?? '';
    $message = $requestData['message'] ?? '';

    if (empty($to) || empty($subject) || empty($message)) {
        http_response_code(400); 
        echo json_encode("Missing required parameters");
        return;
    }

    $status = "Waiting";

    //mengirimkan email pada fungsi queue
    if (addToQueue($conn, $to, $subject, $message, $status)) {
        http_response_code(200); 
        echo json_encode('Email was sent');
    } else {
        http_response_code(500);
        echo json_encode('Failed to add email to queue');
    }
};

//response jika endpoint tidak ditemukan
$endpoints["404"] = function ($requestData) {
    http_response_code(404); 
    echo json_encode("Endpoint not found");
};

// RProses routing endpoint
$parsedURI = parse_url($_SERVER["REQUEST_URI"]);
$endpointName = str_replace($BASE_URI, "", $parsedURI["path"]) ?: "/";
if (isset($endpoints[$endpointName])) {
    $endpoints[$endpointName]($requestData);
} else {
    $endpoints["404"]($requestData);
}

// Close koneksi database
$conn->close();

// fungsi untuk menambahkan email pada queue
function addToQueue($conn, $to, $subject, $message, $status) {
    $sql = "INSERT INTO queued_emails (to_email, subject, message, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $to, $subject, $message, $status);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
