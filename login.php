<?php
require_once 'vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('448863237026-3m9m1jv6et95mlk1g2jrjdtsmeuqvcn8.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/W_rkn_travels/callback.php');
$client->addScope("email");
$client->addScope("profile");
$login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html>
<head><title>Login - R.K.N Travels</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container"><div class="form-container">
<h1>🚌 R.K.N Travels</h1><h3>Login with Google</h3>
<a href="<?= htmlspecialchars($login_url) ?>"><button>🔑 Sign in with Google</button></a>
</div></div>
</body></html>
