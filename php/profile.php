<?php
session_start();
require_once "vendor/autoload.php"; // Include the MongoDB PHP driver
$uid = $_POST["userid"];
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");

// Select the database and collection
$database = $mongoClient->selectDatabase("assessment");
$collection = $database->selectCollection("users");


// Function to get the edit profile form
function getEditForm()
{
    global $collection;

    // Check if user_id is set in the session
    if (isset($_SESSION['user_id'])) {
        // Fetch profile information from MongoDB using the user_id stored in the session
        $user_id = $_SESSION['user_id'];
        $document = $collection->findOne(['user_id' => '']);

        // Check if the document exists
        if ($document) {
            // Return JSON response for AJAX handling in profile.js
            echo json_encode($document);
        } else {
            // Handle the case when no document is found
            echo json_encode(["error" => "User profile not found for user_id: $user_id"]);
        }
    } else {
        // Handle the case when user_id is not set in the session
        echo json_encode(["error" => "User_id not set in the session."]);
    }
}

// Perform actions based on the requested action
if (isset($_POST["action"])) {
    if ($_POST["action"] == "getProfile") {
        global $collection;
        $document = $collection->findOne(['full_name' => $uid]);
        if ($document) {
            echo json_encode($document);
        } else {
            echo json_encode(["error" => "User profile not found for user_id: $uid "]);
        }
    } elseif ($_POST["action"] == "getEditForm") {
        getEditForm();
    } elseif ($_POST["action"] == "editProfile") {
        // Handle the form submission to update the profile in MongoDB
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $updateResult = $collection->updateOne(
                ['user_id' => $user_id],
                ['$set' => [
                    'full_name' => $_POST['full_name'],
                    'email' => $_POST['email'],
                    'age' => $_POST['age'],
                    'contact' => $_POST['contact'],
                    'address' => $_POST['address']
                ]]
            );

            if ($updateResult->getModifiedCount() > 0) {
                // Profile updated successfully
                echo json_encode(["success" => "Profile updated successfully!"]);
            } else {
                // Failed to update profile
                echo json_encode(["error" => "Failed to update profile."]);
            }
        } else {
            // Handle the case when user_id is not set in the session
            echo json_encode(["error" => "User_id not set in the session."]);
        }
    }
}
