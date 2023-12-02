<?php
session_start();

$fullName = $_POST["fullname"];
$email = $_POST["email"];
$password = $_POST["password"];
$passwordRepeat = $_POST["repeat_password"];
$age = $_POST["age"];
$dob = $_POST["dob"];
$contact = $_POST["contact"];
$address = $_POST["address"];

$errors = [];

// Check for required fields
if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
    $errors[] = "All fields are required";
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email is not valid";
}

// Check password length
if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long";
}

// Check password match
if ($password !== $passwordRepeat) {
    $errors[] = "Password does not match";
}

if (!empty($errors)) {
    echo json_encode(['error' => $errors, 'success' => false]);
} else {
    $conn = mysqli_connect("localhost", "root", "", "assessment");

    if (!$conn) {
        die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error(), 'success' => false]));
    }

    // Use prepared statement for checking if email already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rowCount = mysqli_num_rows($result);

        if ($rowCount > 0) {
            $errors[] = "Email already exists!";
            echo json_encode(['error' => $errors, 'success' => false]);
        } else {
            // Continue with the rest of your script

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (user_id, full_name, email, password, age, dob, contact, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($stmt, $sql)) {
                $user_id = mt_rand(100000, 999999); // Generate a random numeric user_id

                mysqli_stmt_bind_param($stmt, "ssssssss", $user_id, $fullName, $email, $hashedPassword, $age, $dob, $contact, $address);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    // Registration successful

                    $user_id = mysqli_insert_id($conn);
                    // Insert additional user details into MongoDB
                    require_once "vendor/autoload.php"; // Include the MongoDB PHP driver
                    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
                    $mongoDB = $mongoClient->selectDatabase("assessment"); // Update the database name
                    $usersCollection = $mongoDB->selectCollection("users");

                    $userDocument = [
                        'user_id' => $user_id,
                        'full_name' => $fullName,
                        'email' => $email,
                        'age' => $age,
                        'dob' => $dob,
                        'contact' => $contact,
                        'address' =>  $address
                    ];

                    $usersCollection->insertOne($userDocument);
                    echo json_encode(['user_id' => $user_id, 'success' => true]);
                } else {
                    // Registration failed
                    echo json_encode(['error' => 'Registration failed', 'success' => false]);
                }

                mysqli_stmt_close($stmt);
            } else {
                die(json_encode(['error' => 'Something went wrong with the query', 'success' => false]));
            }
        }

        mysqli_close($conn);
    } else {
        die(json_encode(['error' => 'Something went wrong with the query', 'success' => false]));
    }
}
