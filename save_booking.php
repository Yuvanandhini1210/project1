<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "rkn_travels");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
if (isset($_POST['name'], $_POST['from_city'], $_POST['to_city'], $_POST['travel_date'], $_POST['tickets'], $_POST['price_per_ticket'], $_POST['distance'], $_POST['duration'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $from_city = $conn->real_escape_string($_POST['from_city']);
    $to_city = $conn->real_escape_string($_POST['to_city']);
    $travel_date = $conn->real_escape_string($_POST['travel_date']);
    $tickets = (int)$_POST['tickets'];
    $price_per_ticket = (float)$_POST['price_per_ticket'];
    $total_price = $tickets * $price_per_ticket;

    $distance = (float)$_POST['distance']; // Distance in km
    $duration = (int)$_POST['duration'];   // Duration in mins

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO bookings (name, from_city, to_city, travel_date, tickets, price_per_ticket, total_price, distance_km, duration_mins) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiddi", $name, $from_city, $to_city, $travel_date, $tickets, $price_per_ticket, $total_price, $distance, $duration);

    if ($stmt->execute()) {
        echo "<div style='text-align:center;margin-top:50px;'><h2>🎟 Booking Confirmed!</h2>";
        echo "<p>Name: $name</p><p>From: $from_city</p><p>To: $to_city</p>";
        echo "<p>Date: $travel_date</p><p>Tickets: $tickets</p><p>Total Price: ₹$total_price</p>";
        echo "<p>🛣 Distance: {$distance} km</p><p>⏱ Duration: {$duration} mins</p></div>";
        echo "<div style='text-align:center;'><a href='index.php'>Back to Home</a></div>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "All fields are required!";
}

$conn->close();
?>
