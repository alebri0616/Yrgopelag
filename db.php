<?php 
declare(strict_types=1);

function getDatabase(): PDO
{
    $db = new PDO('sqlite:database/hotel.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;

}