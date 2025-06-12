<?php
require_once 'vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setClientId('448863237026-3m9m1jv6et95mlk1g2jrjdtsmeuqvcn8.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/W_rkn_travels/callback.php');
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token);
        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();
        $_SESSION['name'] = $userInfo->name;
        $_SESSION['email'] = $userInfo->email;
        $_SESSION['picture'] = $userInfo->picture;
        header('Location: dashboard.php');
    } else {
        echo "Error: " . $token['error_description'];
    }
} else {
    header('Location: login.php');
}
?>
