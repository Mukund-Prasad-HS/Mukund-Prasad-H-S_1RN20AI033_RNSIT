<?php
require 'vendor/autoload.php'; // Include the MongoDB PHP driver

$mongoClient = new MongoDB\Client("mongodb://localhost:27017");

try {
    $mongoClient->listDatabases();
    echo "Connected successfully";
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
