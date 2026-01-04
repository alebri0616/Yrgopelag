<?php 
declare(strict_types=1);

function getDatabas(): PDO
{
    $db = new PDO('sqlite:database.sql');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;

}