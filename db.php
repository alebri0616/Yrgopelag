<?php 

$db = new PDO("sqlite:database.sql");
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);