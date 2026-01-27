<?php 
declare(strict_types=1);

function getDatabase(): PDO
{
    
    $envFile =  __DIR__ . '/../.env';
    $lines = file($envFile);
    
    
    $dbPath = __DIR__ . '/database/hotel.db'; 
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, 'DB_PATH=') === 0) {
            $dbPath = substr($line, 8); 
        }
    }
    
    $fullPath = __DIR__ . '/../' . $dbPath;
    
    $db = new PDO("sqlite:$fullPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $db;
}


function getEnvVar(string $key): string
{
    
    $envFile =  __DIR__ . '/../.env';
    $lines = file($envFile);
    
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, $key . '=') === 0) {
            
            $value = substr($line, strlen($key) + 1);
            return $value;
        }
    }
    

    return '';
}