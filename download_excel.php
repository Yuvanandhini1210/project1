<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="bookings.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Output header row
fputcsv($output, ['ID', 'Name', 'From', 'To', 'Date', 'Tickets', 'Price/Seat', 'Total Price', 'Booking Time']);

// Connect to the database
$conn = new mysqli("localhost", "root", "", "rkn_travels");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id, name, from_city, to_city, travel_date, tickets, price_per_ticket, total_price, booking_time FROM bookings");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ['No bookings available']);
}

fclose($output);
$conn->close();
?>
