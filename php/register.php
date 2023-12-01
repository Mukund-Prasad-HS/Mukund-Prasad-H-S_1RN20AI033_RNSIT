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


$errors = array();

if (empty($fullName) or empty($email) or empty($password) or empty($passwordRepeat)) {
    array_push($errors, "All fields are required");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    array_push($errors, "Email is not valid");
}
if (strlen($password) < 8) {
    array_push($errors, "Password must be at least 8 characters long");
}
if ($password !== $passwordRepeat) {
    array_push($errors, "Password does not match");
}

if (count($errors) > 0) {
    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
} else {
    $user_id = mt_rand(100000, 999999); // Generate a random numeric user_id

    $hostName = "localhost";
    $dbUser = "root";
    $dbPassword = "";
    $dbName = "guvi_task";
    $conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);

    if ($rowCount > 0) {
        array_push($errors, "Email already exists!");
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        $sql = "INSERT INTO users (user_id, full_name, email, password, age, dob, contact, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $user_id, $fullName, $email, $password, $age, $dob, $contact, $address);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Registration successful

                $user_id = mysqli_insert_id($conn);
                // Insert additional user details into MongoDB
                require_once "vendor/autoload.php"; // Include the MongoDB PHP driver
                $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
                $mongoDB = $mongoClient->selectDatabase("guvi_task");
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
                echo json_encode(['user_id' => $fullName, 'success' => true]);
            } else {
                // Registration failed
                echo json_encode(['user_id' => 'error', 'success' => false]);
            }

            mysqli_stmt_close($stmt);
        } else {
            die("Something went wrong with the query");
        }
    }

    mysqli_close($conn);
}
