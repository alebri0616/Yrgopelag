<?php
require_once 'app/header.php';
require_once 'app/calendar.php';
require_once 'app/db.php';


$db = getDatabase();
$roomsQuery = $db->query("SELECT * FROM rooms ORDER BY price_per_night ASC");
$rooms = $roomsQuery->fetchAll(PDO::FETCH_ASSOC);


$roomPrices = [];
foreach ($rooms as $room) {
    $roomPrices[$room['room_type']] = $room['price_per_night'];
}
?>

<div class="rooms">
    <?php foreach ($rooms as $room): ?>
    <div class="room">
        <h3> <?php echo htmlspecialchars($room['room_name']); ?></h3>
        <h2><?php echo htmlspecialchars($room['room_type']); ?></h2>
        <div class="price"><?php echo $room['price_per_night']; ?> credits/night</div>
    </div>
    <?php endforeach; ?>
</div>

<div class="calendars-section">
    <h2> January 2026 Availability</h2>
    <div class="calendars-container">
        <?php foreach ($rooms as $room): ?>
            <?php echo renderCalendar($room['id'],$room['room_name']); ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="instructions">
    <h3> How to Book Your Magical Stay</h3>
    <ol>
        <li>Fill in the booking form below</li>
        <li>Calculate your total (price × nights)</li>
        <li>Visit the Central Bank to create a transfer code</li>
        <li>Return here and enter your transfer code</li>
        <li>Complete your booking and prepare for magic!</li>
    </ol>
</div>

<form class="mainBooking" action="app/booking.php" method="POST">
    <div class="bookingForm">
    <h2 class="form-title">Book Your Magical Stay</h2>
    
    <div class="form-group">
        <label for="guest_name">Your Name:</label>
        <input type="text" id="guest_name" name="guest_name" required>
    </div>
    
    <div class="form-group">
        <label for="room_id">Choose Your Room:</label>
        <select id="room_id" name="room_id" required>
            <option value="">-- Select a room --</option>
            <?php foreach ($rooms as $room): ?>
            <option value="<?php echo $room['id']; ?>">
                 <?php echo htmlspecialchars($room['room_name']); ?> 
                (<?php echo $room['price_per_night']; ?> credits/night)
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="arrival_date">Arrival Date (Check-in at 15:00):</label>
        <input type="date" 
               id="arrival_date" 
               name="arrival_date" 
               min="2026-01-01" 
               max="2026-01-31" 
               required>
    </div>
    
    <div class="form-group">
        <label for="departure_date">Departure Date (Check-out at 11:00):</label>
        <input type="date" 
               id="departure_date" 
               name="departure_date" 
               min="2026-01-01" 
               max="2026-01-31" 
               required>
    </div>
    
    <div class="form-group">
        <label for="transfer_code">Transfer Code (from Central Bank):</label>
        <input type="text" 
               id="transfer_code" 
               name="transfer_code" 
               placeholder="Enter your transfer code here"
               required>
    </div>
    
    <button type="submit"> Complete Booking </button>
    </div>
    <div class="hotelImages">
    <img src="/images/badhotel.jpeg" alt="hotel budget">
    <b><h3>Budget</h3></b>
    <img src="/images/standardhotel.jpeg" alt="hotel standard">
    <b><h3>Standard</h3></b>
    <img src="/images/luxuryhotel.webp" alt="hotel luxury">
    <b><h3>Luxury</h3></b>
    </div>
</form>

<?php require_once 'app/footer.php'; ?>