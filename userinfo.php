<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid']) || !isset($_SESSION['username'])) {
    header("Location: register.php");
    exit();
}

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'shop')
    or die('Error connecting to MySQL server.');

// Retrieve user information from the database
$userID = $_SESSION['userid'];
$query = "SELECT * FROM user WHERE UserID = $userID";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_array($result);
} else {
    header("Location: register.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Information</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.default.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header-logo {
            width: 50px;
            height: auto;
        }
        .user-info {
            margin-top: 20px;
        }
        .user-info p {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .user-info strong {
            color: #333;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div id="all">
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <img src="img/cantact.jpg" alt="Contact Logo" class="header-logo">
                        <h1 class="mt-4">Welcome, <?php echo htmlspecialchars($user['FName']) . ' ' . htmlspecialchars($user['LName']); ?></h1>
                    </div>
                </div>
                <div class="user-info">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['Username']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address']); ?></p>
                    <p><strong>Password:</strong> <?php echo '********'; // Display masked password ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
