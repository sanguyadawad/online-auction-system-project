<?php
session_start();
include 'db_connection.php'; // Ensure this file establishes a connection to your database

if (isset($_POST['register'])) {
    // Initialize an array for errors
    $errors = array();

    // Retrieve and sanitize form data
    $fname = mysqli_real_escape_string($db, trim($_POST['fname']));
    $lname = mysqli_real_escape_string($db, trim($_POST['lname']));
    $addr = mysqli_real_escape_string($db, trim($_POST['addr']));
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $password = $_POST['password']; // Will be hashed later
    $contactNumber = mysqli_real_escape_string($db, trim($_POST['cno']));

    // Validation rules
    if (empty($fname) || strlen($fname) < 2) {
        $errors[] = "First name must be at least 2 characters.";
    }

    if (empty($lname) || strlen($lname) < 2) {
        $errors[] = "Last name must be at least 2 characters.";
    }

    if (empty($username) || strlen($username) < 5) {
        $errors[] = "Username must be at least 5 characters.";
    }

    if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        $errors[] = "Username can only contain letters and numbers.";
    }

    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($contactNumber) || !preg_match("/^\d{10}$/", $contactNumber)) {
        $errors[] = "Contact number must be exactly 10 digits.";
    }

    if (empty($addr)) {
        $errors[] = "Address cannot be empty.";
    }

    // If no validation errors, proceed to database check
    if (empty($errors)) {
        // Check if the username already exists
        $query = "SELECT UserID FROM user WHERE Username = '$username' LIMIT 1";
        $result = mysqli_query($db, $query);

        if (mysqli_num_rows($result) == 0) {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the database
            $newuser = "INSERT INTO user (Username, Password, Address, FName, LName, ContactNumber)
                        VALUES ('$username', '$passwordHash', '$addr', '$fname', '$lname', '$contactNumber')";

            if (mysqli_query($db, $newuser)) {
                // Redirect to login page after successful registration
                header("Location: register.php?success=1");
                exit();
            } else {
                echo "Error: " . $newuser . "<br>" . mysqli_error($db);
            }
        } else {
            // Username already exists
            header("Location: register.php?err=2");
            exit();
        }
    } else {
        // Redirect back to registration form with validation errors
        $_SESSION['errors'] = $errors;
        header("Location: register.php");
        exit();
    }
}
?>

