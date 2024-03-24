<?php

session_status() === PHP_SESSION_ACTIVE ?: session_start();

require_once 'vendor/autoload.php';

use Google\Client as GoogleClient;
use Google\Service\Gmail;

// Configurasi Google OAuth2 
$client = new GoogleClient();
$client->setClientId('419192020039-mbgi6qp474lg2prk33nbtk3dh73gqnsm.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/codechalenge/callback/index.php');
$client->addScope(Gmail::MAIL_GOOGLE_COM);

// Cek Parameter
if (isset($_GET['action']) && $_GET['action'] === 'request_token') {
    // Redirect the user to the Google OAuth2 authorization URL
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit;
}
