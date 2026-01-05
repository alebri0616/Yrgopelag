<?php 
declare(strict_types=1);

require_once 'vendor/autoload.php';

require_once 'db.php';

$myApiKey = 'b6483622-94ad-4591-bf29-bffdcf149717';
$myUsername = 'Alexandru';
$centralBankUrl = 'https://www.yrgopelag.se';

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


$validation = json_decode($validateResponse, true);

if ($validation['status'] !== 'success') {
    echo "Error: Your transfer code is not valid or doesn't have enough money.";
    echo "<br><a href='index.php'>Go back</a>";
    exit;
}

$depositUrl = $centralBankUrl . '/centralbank/deposit';
$depositData = http_build_query([
    'user' => $myUsername,
    'transferCode' => $transferCode
]);

$curl = curl_init($depositUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $depositData);
$depositResponse = curl_exec($curl);

$deposit = json_decode($depositResponse, true);

if ($deposit['status'] !== 'success') {
    echo "Error: Payment failed. Please try again.";
    echo "<br><a href='index.php'>Go back</a>";
    exit;
}

$saveBooking = $db->prepare("
    INSERT INTO bookings (guest_name, room_type, arrival_date, departure_date, total_cost, transfer_code)
    VALUES (?, ?, ?, ?, ?, ?)
");

$saveBooking->execute([
    $guestName,
    $roomType,
    $arrivalDate,
    $departureDate,
    $totalCost,
    $transferCode
]);

$receiptUrl = $centralBankUrl . '/centralbank/receipt';
$receiptData = json_encode([
    'user' => $myUsername,
    'api_key' => $myApiKey,
    'guest_name' => $guestName,
    'arrival_date' => $arrivalDate,
    'departure_date' => $departureDate,
    'features_used' => [],
    'star_rating' => 1
]);

$curl = curl_init($receiptUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $receiptData);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($curl);