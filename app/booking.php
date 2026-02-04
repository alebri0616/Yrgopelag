<?php 
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

require_once 'db.php';

ob_start();

use GuzzleHttp\Client;


$myApiKey = getEnvVar('API_KEY');
$myUsername = getEnvVar('USER');
$centralBankUrl = getEnvVar('CENTRAL_BANK_URL');

$db = getDatabase();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}


if (!isset($_POST['guest_name'], $_POST['room_id'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['transfer_code'])) {
    echo "Error: Missing required fields.";
    echo "<br><a href='../index.php'>Go back</a>";
    exit;
}

$guestName = $_POST['guest_name'];
$roomId = (int)$_POST['room_id'];
$arrivalDate = $_POST['arrival_date'];
$departureDate = $_POST['departure_date'];
$transferCode = $_POST['transfer_code'];


$roomQuery = $db->prepare("SELECT id, room_name, price_per_night FROM rooms WHERE id = ?");
$roomQuery->execute([$roomId]);
$room = $roomQuery->fetch();

if (!$room) {
    echo "Error: Invalid room type selected.";
    echo "<br><a href='../index.php'>Go back</a>";
    exit;
}

$pricePerNight = $room['price_per_night'];

$arrival = new DateTime($arrivalDate);
$departure = new DateTime($departureDate);
$nights = $arrival->diff($departure)->days;

$totalCost = $pricePerNight * $nights;


$checkAvailability = $db->prepare("
    SELECT COUNT(*) as count FROM bookings 
    WHERE room_id = ? AND arrival_date < ? AND departure_date > ?
");

$checkAvailability->execute([$roomId, $departureDate, $arrivalDate]);
$result = $checkAvailability->fetch();

if ($result['count'] > 0) {
    echo "Sorry! This room is already booked for those dates.";
    echo "<br><a href='../index.php'>Go back</a>";
    exit;
}

$client = new Client(['verify' => false]);


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
        echo "<br><a href='../index.php'>Go back</a>";
        exit;
    }
} catch (Exception $e) {
    echo "Error validating transfer code: " . $e->getMessage();
    echo "<br><a href='../index.php'>Go back</a>";
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
        echo "<br><a href='../index.php'>Go back</a>";
        exit;
    }
} catch (Exception $e) {
    echo "Error processing payment: " . $e->getMessage();
    echo "<br><a href='../index.php'>Go back</a>";
    exit;
}


$saveBooking = $db->prepare("
    INSERT INTO bookings (guest_name, room_id, arrival_date, departure_date, total_cost, transfer_code)
    VALUES (?, ?, ?, ?, ?, ?)
");

$saveBooking->execute([
    $guestName,
    $roomId,
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
<?php require_once 'header.php'; ?>

<div class="confirmation">
    <h1> Booking Confirmed!</h1>
    
    <p>Thank you for choosing Magical Island Hotel!</p>
    
    <div class="details">
        <p><strong>Guest Name:</strong> <?php echo htmlspecialchars($guestName); ?></p>
        <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['room_name']); ?></p>
        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($arrivalDate); ?> at 15:00</p>
        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($departureDate); ?> at 11:00</p>
        <p><strong>Number of Nights:</strong> <?php echo $nights; ?></p>
        <p><strong>Total Cost:</strong> <?php echo $totalCost; ?> credits</p>
    </div>
    
    <p> Your magical adventure awaits! </p>
    
    <a href="../index.php">Back to Home</a>
</div>

<?php require_once 'footer.php'; ?>