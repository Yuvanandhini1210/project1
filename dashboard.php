<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container"><div class="form-container">
<h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
<img src="<?= htmlspecialchars($_SESSION['picture']) ?>" style="width:100px;border-radius:50%;">
<p><?= htmlspecialchars($_SESSION['email']) ?></p>
<a href="index.php"><button>🚍 Go to Booking</button></a>
<a href="logout.php"><button style="background:#ff4b5c;">Logout</button></a>
</div></div>
</body></html>
