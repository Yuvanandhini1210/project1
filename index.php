<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}
$price_per_ticket = 300;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🚌 R.K.N Travels</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-top: 10px;
        }
        #route-info-box {
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- User Info Bar -->
<div class="user-info">
    <img src="<?= htmlspecialchars($_SESSION['picture']) ?>" alt="Profile">
    <span><?= htmlspecialchars($_SESSION['name']) ?> (<?= htmlspecialchars($_SESSION['email']) ?>)</span>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <h1 class="gradient-text">🚌 R.K.N Travels</h1>
    
    <form method="post" action="save_booking.php" id="bookingForm">
        <input type="hidden" name="price_per_ticket" value="<?= $price_per_ticket ?>">
        <input type="hidden" id="distance" name="distance">
        <input type="hidden" id="duration" name="duration">

        <table>
            <tr>
                <td><input type="text" id="from_city" name="from_city" placeholder="Type or select pickup location" list="city-list"></td>
                <td><button type="button" class="map-btn" onclick="setSelecting('from')">Choose Pickup from Map</button></td>
                <td><button type="button" class="clear-btn" onclick="clearPickup()">Clear Pickup</button></td>
            </tr>
            <tr>
                <td><input type="text" id="to_city" name="to_city" placeholder="Type or select destination location" list="city-list"></td>
                <td><button type="button" class="map-btn" onclick="setSelecting('to')">Choose Destination from Map</button></td>
                <td><button type="button" class="clear-btn" onclick="clearDestination()">Clear Destination</button></td>
            </tr>
            <tr>
                <td colspan="3"><input type="date" name="travel_date" required min="<?= date('Y-m-d') ?>"></td>
            </tr>
        </table>
        <button type="button" onclick="handleSearch()">🔍 Search</button>
    </form>

    <div id="map-section" style="display:none;">
        <div id="map"></div>
        <div id="route-info-box"></div>
    </div>

    <div class="bus-grid">
        <?php for($i=1;$i<=9;$i++): ?>
            <div class="bus-card">
                <img src="images/bus<?= $i % 3 + 1 ?>.jpg" alt="Bus Image">
                <p>Bus Service <?= $i ?></p>
            </div>
        <?php endfor; ?>
    </div>
</div>

<datalist id="city-list">
    <option value="Chennai"><option value="Madurai"><option value="Coimbatore"><option value="Trichy">
    <option value="Salem"><option value="Erode"><option value="Bangalore"><option value="Hyderabad">
    <option value="Mysore"><option value="Pune"><option value="Mumbai"><option value="Delhi">
</datalist>

<script>
let map, fromMarker = null, toMarker = null, routeLine = null, selecting = null;

function setSelecting(mode) {
    if (!map) initMap();
    selecting = mode;
}

function initMap() {
    document.getElementById('map-section').style.display = 'block';
    setTimeout(() => {
        map = L.map('map').setView([13.0827, 80.2707], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        map.on('click', onMapClick);
    }, 100); // delay ensures map container is visible
}

async function onMapClick(e) {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`);
    const data = await res.json();
    const name = data.display_name || `${e.latlng.lat}, ${e.latlng.lng}`;
    if (selecting === 'from') {
        if (fromMarker) map.removeLayer(fromMarker);
        fromMarker = L.marker(e.latlng).addTo(map);
        document.getElementById('from_city').value = name;
        fromMarker.latlng = e.latlng;
        selecting = null;
    } else if (selecting === 'to') {
        if (toMarker) map.removeLayer(toMarker);
        toMarker = L.marker(e.latlng).addTo(map);
        document.getElementById('to_city').value = name;
        toMarker.latlng = e.latlng;
        selecting = null;
    }
}

async function handleSearch() {
    const from = document.getElementById('from_city').value;
    const to = document.getElementById('to_city').value;
    if (!from || !to) {
        alert("Please fill Pickup and Destination");
        return;
    }
    const fromRes = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(from)}&format=json&limit=1`);
    const fromData = await fromRes.json();
    if (!fromData.length) return;
    const toRes = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(to)}&format=json&limit=1`);
    const toData = await toRes.json();
    if (!toData.length) return;
    if (!map) initMap();
    const fromLatLng = { lat: +fromData[0].lat, lng: +fromData[0].lon };
    const toLatLng = { lat: +toData[0].lat, lng: +toData[0].lon };
    if (fromMarker) map.removeLayer(fromMarker);
    if (toMarker) map.removeLayer(toMarker);
    fromMarker = L.marker(fromLatLng).addTo(map);
    toMarker = L.marker(toLatLng).addTo(map);
    map.setView(fromLatLng, 10);
    drawRoute(fromLatLng, toLatLng);
}

async function drawRoute(from, to) {
    const url = 'https://api.openrouteservice.org/v2/directions/driving-car/geojson';
    const apiKey = '5b3ce3597851110001cf6248b6462e6a034e4f6fb69c6a76a0c914a0';
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            Authorization: apiKey,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ coordinates: [[from.lng, from.lat], [to.lng, to.lat]] })
    });
    const data = await res.json();
    if (!data.features.length) return;
    if (routeLine) map.removeLayer(routeLine);
    routeLine = L.geoJSON(data.features[0].geometry, { style: { color: 'blue' } }).addTo(map);
    const dist = (data.features[0].properties.summary.distance / 1000).toFixed(2);
    const dur = Math.round(data.features[0].properties.summary.duration / 60);
    document.getElementById('route-info-box').style.display = 'block';
    document.getElementById('route-info-box').innerHTML = `🛣 ${dist} km | ⏱ ${dur} mins`;
    document.getElementById('distance').value = dist;
    document.getElementById('duration').value = dur;
}

function clearPickup() {
    if (fromMarker) map.removeLayer(fromMarker);
    document.getElementById('from_city').value = '';
    fromMarker = null;
}

function clearDestination() {
    if (toMarker) map.removeLayer(toMarker);
    document.getElementById('to_city').value = '';
    toMarker = null;
}
</script>

</body>
</html>