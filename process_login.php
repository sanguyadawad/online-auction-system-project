<?php
session_start();
include 'db_connection.php'; // Ensure this file establishes a connection to your database

// Retrieve form data
$username = mysqli_real_escape_string($db, $_POST['username']);
$password = mysqli_real_escape_string($db, $_POST['password']);

// Check if the username exists
$query = "SELECT * FROM user WHERE Username='$username'";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_array($result);
    // Verify the password
    if (password_verify($password, $user['Password'])) {
        $_SESSION['userid'] = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['contactnumber'] = $user['ContactNumber'];
        $_SESSION['fullname'] = $user['FName'] . ' ' . $user['LName']; // Store full name in session
        header('Location: index.php');
    } else {
        header('Location: register.php?err=1');
    }
} else {
    header('Location: register.php?err=1');
}
?>
