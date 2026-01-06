<?php

declare(strict_types=1);

require_once 'db.php';

function getBookedDates(string $roomType): array
{
    $db = getDatabase();
    
    $stmt = $db->prepare("
        SELECT arrival_date, departure_date 
        FROM bookings 
        WHERE room_type = ?
    ");
    
    $stmt->execute([$roomType]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $bookedDates = [];
    
    foreach ($bookings as $booking) {
        $start = new DateTime($booking['arrival_date']);
        $end = new DateTime($booking['departure_date']);
        
        while ($start < $end) {
            $bookedDates[] = $start->format('Y-m-d');
            $start->modify('+1 day');
        }
    }
    
    return $bookedDates;
}