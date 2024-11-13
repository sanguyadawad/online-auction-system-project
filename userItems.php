<?php
session_start();
include '../db_connection.php'; // Adjust the path based on your directory structure
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

    <title>Auction</title>

    <meta name="keywords" content="">

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

    <script>
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                display.textContent = minutes + ":" + seconds;
                if (--timer < 0) {
                    timer = duration;
                }
            }, 1000);
        }

        window.onload = function () {
            var auctionDuration = 60 * 5, // 5 minutes
                display = document.querySelector('#time');
            startTimer(auctionDuration, display);
        };
    </script>
</head>

<body>
    <?php include 'header.php'; ?>

    <?php
    $ItemNo = intval($_GET['ItemNo']);
    $query = "SELECT * FROM item WHERE ItemID=$ItemNo";
    $result = mysqli_query($db, $query);
    $item = mysqli_fetch_array($result);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $bidAmount = floatval($_POST['bidAmount']);

        if ($bidAmount < $item['MinBidPrice'] || $bidAmount > $item['MaxBidPrice']) {
            $message = "Your bid must be between " . number_format($item['MinBidPrice'], 2) . " and " . number_format($item['MaxBidPrice'], 2);
        } else {
            // Process the bid
            $updateQuery = "UPDATE item SET CurrentPrice=$bidAmount WHERE ItemID=$ItemNo";
            if (mysqli_query($db, $updateQuery)) {
                $message = "Your bid of " . number_format($bidAmount, 2) . " has been placed successfully.";
            } else {
                $message = "Error: " . mysqli_error($db);
            }
        }
    }
    ?>

    <div id="all">
        <div id="content">
            <div class="container">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="#">Home</a></li>
                        <li><a href="category.php?CategoryID=<?php echo $item['CategoryID'] ?>"><?php echo $item['CategoryID'] ?></a></li>
                        <li><?php echo $item['ItemName']; ?></li>
                    </ul>
                </div>

                <div class="col-md-9" id="productMain">
                    <div class="row" id="productView">
                        <div class="col-sm-6">
                            <div id="mainImage">
                                <img src="<?php echo $item['PhotosID']; ?>" alt="" class="img-responsive">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box">
                                <h1 class="text-center"><?php echo $item['ItemName']; ?></h1>
                                <p class="price">Current Price: Rs <?php echo number_format($item['CurrentPrice'], 2); ?></p>
                                <p class="text-center">
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <label for="bidAmount">Enter your bid amount:</label>
                                            <input type="number" step="0.01" name="bidAmount" id="bidAmount" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Place Bid</button>
                                    </form>
                                    <?php if (isset($message)): ?>
                                        <div class="alert alert-info mt-2">
                                            <?php echo $message; ?>
                                        </div>
                                    <?php endif; ?>
                                </p>
                                <div class="auction-timer">
                                    <p>Auction ends in: <span id="time">05:00</span> minutes!</p>
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
