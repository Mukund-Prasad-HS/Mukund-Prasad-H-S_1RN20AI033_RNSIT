<?php
session_start();

if (isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $conn = mysqli_connect("localhost", "root", "", "assessment");
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM users WHERE LOWER(email) = LOWER(?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Compare the entered password with the stored hashed password
            if (password_verify($password, $user["hashed_password"])) {
                echo json_encode(['success' => true, 'user_id' => $user['full_name']]);
            } else {
                // Password is incorrect
                echo json_encode(['error' => 'Password is incorrect']);
            }
        } else {
            // User with the provided email not found
            echo json_encode(['error' => 'User with the provided email not found']);
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Database query failed: " . mysqli_error($conn));
    }

    mysqli_close($conn);
}
?>
