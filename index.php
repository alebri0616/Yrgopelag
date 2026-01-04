<?php 
declare(strict_types=1);


?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles/style.css">
    <title>Hotel</title>
</head>
<body>
    <h1>Magical Island Hotel</h1>
    <p> Welcome to the island of a life time!</p>

    <form action="booking.php" method="POST">
    <h2 class="form-title">Book Your Stay</h2>
    
    <div class="form-group">
        <label for="guest_name">Your Name:</label>
        <input type="text" id="guest_name" name="guest_name" required>
    </div>
    
    <div class="form-group">
        <label for="room_type">Choose Room Type:</label>
        <select id="room_type" name="room_type" required>
            <option value="">-- Select a room --</option>
            <option value="budget">Budget Room (100 credits/night)</option>
            <option value="standard">Standard Room (200 credits/night)</option>
            <option value="luxury">Luxury Room (300 credits/night)</option>
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
        <label for="transfer_code">Transfer Code (get this from Central Bank):</label>
        <input type="text" 
               id="transfer_code" 
               name="transfer_code" 
               placeholder="Enter your transfer code here"
               required>
    </div>
    
    <button type="submit">Complete Booking</button>
</form>
</body>
</html>