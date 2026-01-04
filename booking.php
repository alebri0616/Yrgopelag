<?php 
declare(strict_types=1);
require_once 'db.php';

$myApiKey = 'b6483622-94ad-4591-bf29-bffdcf149717';
$myUsername = 'Alexandru';
$centralBankUrl = 'https://www.yrgopelag.se/centralbank/    ';

$db = getDatabase();

$guestName = $_POST['guest_name'];
$roomType = $_POST['room_type'];
$arrivalDate = $_POST['arrival_date'];
$departureDate = $_POST['departure_date'];
$transferCode = $_POST['transfer_code'];

$roomPrices = [
    'budget' => 100,
    'standard' => 200,
    'luxury' => 300
];

$pricePerNight = $roomPrices[$roomType];

$arrival = new DateTime($arrivalDate);
$departure = new DateTime($departureDate);
$nights = $arrival->diff($departure)->days;

$totalCost = $pricePerNight * $nights;

$checkAvailability = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_type = ? AND arrival_date < ? AND departure_date > ?
");

$checkAvailability->execute([$roomType, $departureDate, $arrivalDate]);
$result = $checkAvailability->fetch();

if ($result['count'] > 0) {
    echo "Sorry! This room is already booked for those dates.";
    echo "<br><a href='index.php'>Go back</a>";
    exit;
}

$validateUrl = $centralBankUrl . '/centralbank/transferCode';
$validateData = json_encode([
    'transferCode' => $transferCode,
    'totalCost' => $totalCost
]);

$curl = curl_init($validateUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $validateData);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$validateResponse = curl_exec($curl);
curl_close($curl);

$validation = json_decode($validateResponse, true);

if ($validation['status'] !== 'success') {
    echo "Error: Your transfer code is not valid or doesn't have enough money.";
    echo "<br><a href='index.php'>Go back</a>";
    exit;
}