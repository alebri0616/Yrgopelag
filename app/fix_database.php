<?php
declare(strict_types=1);

require_once 'db.php';

try {
    $db = getDatabase();
    
 
    $db->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            room_type VARCHAR,
            room_name VARCHAR,
            price_per_night INTEGER,
            description TEXT
        )
    ");
    
 
    $db->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            room_id INTEGER,
            guest_name VARCHAR,
            arrival_date VARCHAR,
            departure_date VARCHAR,
            total_cost INTEGER,
            transfer_code VARCHAR,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES rooms(id)
        )
    ");
    
    
    $checkRooms = $db->query("SELECT COUNT(*) as count FROM rooms");
    $roomCount = $checkRooms->fetch()['count'];
    
    if ($roomCount == 0) {
        echo "Rooms table is empty. Adding room data...\n";
        
        
        $rooms = [
            [1, 'budget', 'Budget Room', 100, 'Cozy and comfortable'],
            [2, 'standard', 'Standard Room', 200, 'Perfect island retreat'],
            [3, 'luxury', 'Luxury Room', 300, 'Ultimate magical experience']
        ];
        
        $insertRoom = $db->prepare("
            INSERT INTO rooms (id, room_type, room_name, price_per_night, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($rooms as $room) {
            $insertRoom->execute($room);
        }
        
        echo " Added 3 rooms successfully!\n";
    } else {
        echo " Rooms table already has data ($roomCount rooms)\n";
    }
    

    echo "\n Current rooms in database:\n";
    $allRooms = $db->query("SELECT * FROM rooms");
    while ($room = $allRooms->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID: {$room['id']} - {$room['room_name']} - {$room['price_per_night']} credits/night\n";
    }
    
    echo "\n Database is ready!\n";
    
} catch (Exception $e) {
    echo " Error: " . $e->getMessage() . "\n";
    exit(1);
}