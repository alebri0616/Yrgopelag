<?php 
declare(strict_types=1);

require_once 'vendor/autoload.php';

require_once 'db.php';

use GuzzleHttp\Client;

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

$client = new Client([
    'verify' => false

]);



try {
    $validateResponse = $client->post($centralBankUrl . '/centralbank/transferCode', [
        'json' => [
            'transferCode' => $transferCode,
            'totalCost' => $totalCost
        ]
    ]);

 $validation = json_decode($validateResponse->getBody()->getContents(), true);
    
    if ($validation['status'] !== 'success') {
        echo "Error: Your transfer code is not valid or doesn't have enough money.";
        echo "<br><a href='index.php'>Go back</a>";
        exit;
    }
} catch (Exception $e) {
    echo "Error validating transfer code: " . $e->getMessage();
    echo "<br><a href='index.php'>Go back</a>";
    exit;
}

try {
    $depositResponse = $client->post($centralBankUrl . '/centralbank/deposit', [
        'form_params' => [
            'user' => $myUsername,
            'transferCode' => $transferCode
        ]
    ]);
    
    $deposit = json_decode($depositResponse->getBody()->getContents(), true);
    
    if ($deposit['status'] !== 'success') {
        echo "Error: Payment failed. Please try again.";
        echo "<br><a href='index.php'>Go back</a>";
        exit;
    }
} catch (Exception $e) {
    echo "Error processing payment: " . $e->getMessage();
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

try {
    $client->post($centralBankUrl . '/centralbank/receipt', [
        'json' => [
            'user' => $myUsername,
            'api_key' => $myApiKey,
            'guest_name' => $guestName,
            'arrival_date' => $arrivalDate,
            'departure_date' => $departureDate,
            'features_used' => [],
            'star_rating' => 1
        ]
    ]);
} catch (Exception $e) {

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="styles/bookingstyle.css">
</head>
<body>
    <div class="container">
        <h1>Booking Confirmed!</h1>
        
        <p>Thank you for your booking!</p>
        
        <div class="details">
            <p><strong>Guest Name:</strong> <?php echo htmlspecialchars(trim($guestName)); ?></p>
            <p><strong>Room Type:</strong> <?php echo htmlspecialchars(trim(ucfirst($roomType))); ?></p>
            <p><strong>Check-in:</strong> <?php echo htmlspecialchars(trim($arrivalDate)); ?> at 15:00</p>
            <p><strong>Check-out:</strong> <?php echo htmlspecialchars(trim($departureDate)); ?> at 11:00</p>
            <p><strong>Number of Nights:</strong> <?php echo $nights; ?></p>
            <p><strong>Total Cost:</strong> <?php echo $totalCost; ?> credits</p>
        </div>
        
        <p>We look forward to seeing you!</p>
        <p>Have a Magical stay!</p>
        
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>