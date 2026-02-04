<?php
declare(strict_types=1);

require_once 'db.php';

function getBookedDates(int $roomId): array
{
    $db = getDatabase();
    
    $stmt = $db->prepare("
        SELECT arrival_date, departure_date 
        FROM bookings 
        WHERE room_id = ?
    ");
    
    $stmt->execute([$roomId]);
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

function renderCalendar(int $roomId, string $roomName): string
{
    $bookedDates = getBookedDates($roomId);
    
    $html = '<div class="calendar">';
    $html .= '<h3>' . htmlspecialchars($roomName) . '</h3>';
    $html .= '<div class="calendar-grid">';
    
    
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    foreach ($days as $day) {
        $html .= '<div class="calendar-day-header">' . $day . '</div>';
    }
    
    
    for ($i = 0; $i < 3; $i++) {
        $html .= '<div class="calendar-day empty"></div>';
    }
    
    
    for ($day = 1; $day <= 31; $day++) {
        $date = sprintf('2026-01-%02d', $day);
        $isBooked = in_array($date, $bookedDates);
        $class = $isBooked ? 'calendar-day booked' : 'calendar-day available';
        
        $html .= '<div class="' . $class . '">' . $day . '</div>';
    }
    
    $html .= '</div>';
    $html .= '<div class="calendar-legend">';
    $html .= '<span class="legend-available">⬜ Available</span>';
    $html .= '<span class="legend-booked">🟥 Booked</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}