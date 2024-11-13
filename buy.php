<?php
session_start();
include 'db_connection.php'; // Ensure this file establishes a connection to your database

// Initialize message variable
$message = '';
$winner = null;
$bidAmount = 0; // Variable to store the bid amount

// Get item number from GET parameters
$ItemNo = intval($_GET['ItemNo'] ?? 0);
$query = "SELECT * FROM item WHERE ItemID=$ItemNo";
$result = mysqli_query($db, $query);

// Check if the query was successful and if any rows were returned
if ($result && mysqli_num_rows($result) > 0) {
    $item = mysqli_fetch_array($result, MYSQLI_ASSOC);

    // Default values
    $item['MinBidPrice'] = $item['MinBidPrice'] ?? 0;
    $item['MaxBidPrice'] = $item['MaxBidPrice'] ?? PHP_INT_MAX;
    $item['AuctionStartTime'] = $item['AuctionStartTime'] ?? date("Y-m-d H:i:s");

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bidAmount'])) {
        // Check if the auction has ended
        $auctionEndTime = strtotime('+1 minute', strtotime($item['AuctionStartTime']));
        if (time() > $auctionEndTime) {
            $message = "The auction has ended. You cannot place a bid.";
        } else {
            $bidAmount = floatval($_POST['bidAmount']);

            if ($bidAmount <= $item['CurrentPrice']) {
                $message = "Your bid must be higher than the current price of " . number_format($item['CurrentPrice'], 2);
            } elseif ($bidAmount < $item['MinBidPrice'] || $bidAmount > $item['MaxBidPrice']) {
                $message = "Your bid must be between " . number_format($item['MinBidPrice'], 2) . " and " . number_format($item['MaxBidPrice'], 2);
            } else {
                if (isset($_SESSION['UserID'])) {
                    $username = $_SESSION['Username'];
                    // Process the bid
                    $updateQuery = "UPDATE item SET CurrentPrice=$bidAmount, WinnerUserID='{$_SESSION['UserID']}', WinnerUsername='$username', WinnerBidAmount=$bidAmount WHERE ItemID=$ItemNo";
                    if (mysqli_query($db, $updateQuery)) {
                        $_SESSION['WinningBidAmount'] = $bidAmount; // Store the bid amount in session
                        $_SESSION['WinningItem'] = $item; // Optionally store item details if needed
                        $message = "Congratulations, your bid of " . number_format($bidAmount, 2) . " has been placed successfully.";
                    } else {
                        $message = "Error: " . mysqli_error($db);
                    }
                } else {
                    $message = "Your bid has been placed successfully. Wait a minute.";
                }
            }
        }
    }
} else {
    $message = "Item not found.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <meta name="robots" content="all,follow">
    <meta name="googlebot" content="index,follow,snippet,archive">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Obaju e-commerce template">
    <meta name="author" content="Ondrej Svestka | ondrejsvestka.cz">
    <meta name="keywords" content="">

    <title>Online System</title>

    <link href='http://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100' rel='stylesheet' type='text/css'>
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/owl.theme.css" rel="stylesheet">
    <link href="css/style.default.css" rel="stylesheet" id="theme-stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <script src="js/respond.min.js"></script>
    <link rel="shortcut icon" href="favicon.png">
    
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <script>
        function startTimer(duration, display, endCallback) {
            var timer = duration, minutes, seconds;
            var interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    endCallback();
                }
            }, 1000);
        }

        window.onload = function () {
            var auctionDuration = 60, // 1 minute in seconds
                display = document.querySelector('#time');

            startTimer(auctionDuration, display, function() {
                document.getElementById('bidForm').style.display = 'none';
                document.getElementById('endMessage').style.display = 'block';
                document.getElementById('winnerButton').style.display = 'block'; // Show the button
                document.getElementById('winnerInfo').style.display = 'block'; // Show the winner info
            });
        };
    </script>
    <style>
        #winnerButton {
            display: none;
            background-color: #28a745; /* Green */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        #winnerButton:hover {
            background-color: #218838; /* Darker green */
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div id="all">
        <div id="content">
            <div class="container">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="#">Home</a></li>
                        <li><a href="category.php?CategoryID=<?php echo $item['CategoryID'] ?? '#'; ?>"><?php echo $item['CategoryID'] ?? 'Unknown'; ?></a></li>
                        <li><?php echo $item['ItemName'] ?? 'Unknown'; ?></li>
                    </ul>
                </div>

                <div class="col-md-9" id="productMain">
                    <div class="row" id="productView">
                        <div class="col-sm-6">
                            <div id="mainImage">
                                <img src="<?php echo $item['PhotosID'] ?? 'placeholder.jpg'; ?>" alt="" class="img-responsive">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box">
                                <h1 class="text-center"><?php echo $item['ItemName'] ?? 'Unknown Item'; ?></h1>
                                <p class="price">Current Price: <span id="currentPrice">Rs <?php echo number_format($item['CurrentPrice'] ?? 0, 2); ?></span></p>
                                <p class="text-center">
                                    <form id="bidForm" action="" method="post">
                                        <div class="form-group">
                                            <label for="bidAmount">Enter your bid amount:</label>
                                            <input type="number" step="0.01" name="bidAmount" id="bidAmount" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Place Bid</button>
                                    </form>
                                    <?php if ($message): ?>
                                        <div class="alert alert-info mt-2">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>
                                </p>
                                <div id="endMessage" class="alert alert-success mt-2" style="display: none;">
                                    Congratulations, <?php echo $_SESSION['Username'] ?? 'User'; ?>! You have won the bid for the item <?php echo $item['ItemName'] ?? 'Tshirt'; ?>.
                                    <br><br>
                                </div>
                                <a id="winnerButton" href="winer.php">Winner Is...</a>

                                <div class="auction-timer">
                                    <p>Auction ends in: <span id="time">01:00</span> minutes!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </div>
        <!-- /#content -->

        <?php include 'footer.php'; ?>
    </div>
    <!-- /#all -->

    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/modernizr.js"></script>
    <script src="js/bootstrap-hover-dropdown.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/front.js"></script>
</body>

</html>
