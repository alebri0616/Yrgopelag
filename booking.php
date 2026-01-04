<?php 
declare(strict_types=1);
require_once 'db.php';

$myApiKey = 'b6483622-94ad-4591-bf29-bffdcf149717';
$myUsername = 'Alexandru';
$centralBankUrl = 'https://your-central-bank-url.com';

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