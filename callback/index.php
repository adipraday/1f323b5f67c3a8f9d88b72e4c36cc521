<?php

session_status() === PHP_SESSION_ACTIVE ?: session_start();
require_once '../vendor/autoload.php';
require_once '../db_conn.php';

use Google\Client as GoogleClient;

$client = new GoogleClient();
$client->setClientId('419192020039-mbgi6qp474lg2prk33nbtk3dh73gqnsm.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/codechalenge/callback/index.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ' . filter_var('http://' . $_SERVER['HTTP_HOST'] . '/index.php', FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token'])) {
    $accessToken = $_SESSION['access_token'];
    $shortAccessToken = substr($accessToken['access_token'], 0, 50); 

    $sql = "INSERT INTO sendemail_request (access_token) VALUES ('$shortAccessToken')";
    $conn->query($sql);
    
    $pesan = "Silahkan copy access token anda <b>token akan expired dalam 5 menit</b> :";
    echo $pesan;
    echo '<br>';
    echo $shortAccessToken;
    
} else {
    echo 'Access Token not found.';
}
?>
