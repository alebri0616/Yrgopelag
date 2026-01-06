<?php
require_once 'header.php';

$budgetPrice = 100;
$standardPrice = 200;
$luxuryPrice = 300;


?>
<div class="rooms">
    <div class="room">
        <h3> Budget Room</h3>
        <p>Cozy and comfortable</p>
        <div class="price"><?php echo $budgetPrice; ?> credits/night</div>
    </div>
    
    <div class="room">
        <h3> Standard Room</h3>
        <p>Perfect island retreat</p>
        <div class="price"><?php echo $standardPrice; ?> credits/night</div>
    </div>
    
    <div class="room">
        <h3> Luxury Room</h3>
        <p>Ultimate magical experience</p>
        <div class="price"><?php echo $luxuryPrice; ?> credits/night</div>
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

<form action="booking.php" method="POST">
    <h2 class="form-title">Book Your Magical Stay</h2>
    
    <div class="form-group">
        <label for="guest_name">Your Name:</label>
        <input type="text" id="guest_name" name="guest_name" required>
    </div>
    
    <div class="form-group">
        <label for="room_type">Choose Your Room:</label>
        <select id="room_type" name="room_type" required>
            <option value="">-- Select a room --</option>
            <option value="budget"> Budget Room (<?php echo $budgetPrice; ?> credits/night)</option>
            <option value="standard"> Standard Room (<?php echo $standardPrice; ?> credits/night)</option>
            <option value="luxury"> Luxury Room (<?php echo $luxuryPrice; ?> credits/night)</option>
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
</form>

<?php

require_once 'footer.php';

?>