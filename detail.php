<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'shop') or die('Error connecting to MySQL server.');

$ItemNo = intval($_GET['ItemNo']); // Ensure ItemNo is an integer to prevent SQL injection
$query = "SELECT * FROM item WHERE ItemID = $ItemNo";
$result = mysqli_query($db, $query);

if (!$result) {
    die('Error executing query: ' . mysqli_error($db));
}

$row = mysqli_fetch_array($result);
if (!$row) {
    die('Item not found.');
}

$leastValue = $row['CurrentPrice'] + 50;
$ExpectedValue = $row['ExpectedPrice'];

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
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100' rel='stylesheet' type='text/css'>
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/owl.theme.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/style.default.css" rel="stylesheet" id="theme-stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <script src="js/respond.min.js"></script>
    <link rel="shortcut icon" href="favicon.png">
</head>

<body onload="countdown(year,month,day,hour,minute)">
    <?php include 'header.php'; ?>

    <div id="all">
        <div id="content">
            <div class="container">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="#">Home</a></li>
                        <li><?php echo htmlspecialchars($row['ItemName']); ?></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <div class="panel panel-default sidebar-menu">
                        <div class="panel-heading">
                            <h3 class="panel-title">Categories</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked category-menu">
                                <?php
                                $query1 = "SELECT * FROM category";
                                $result1 = mysqli_query($db, $query1);
                                if (!$result1) {
                                    die('Error executing query: ' . mysqli_error($db));
                                }
                                while ($categories = mysqli_fetch_assoc($result1)) {
                                ?>
                                    <li>
                                        <a href="category.php?CategoryID=<?php echo $categories['CategoryID']; ?>"><?php echo htmlspecialchars($categories['Category']); ?></a>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="banner">
                        <a href="#">
                            <img src="img/banner.jpg" alt="sales 2014" class="img-responsive">
                        </a>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="row" id="productMain">
                        <div class="col-sm-6">
                            <div id="mainImage">
                                <img src="<?php echo htmlspecialchars($row['PhotosID']); ?>" alt="" class="img-responsive">
                            </div>
                            <div class="ribbon sale">
                                <div class="theribbon">SALE</div>
                                <div class="ribbon-background"></div>
                            </div>
                            <div class="ribbon new">
                                <div class="theribbon">NEW</div>
                                <div class="ribbon-background"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box">
                                <h1 class="text-center"><?php echo htmlspecialchars($row['ItemName']); ?></h1>
                                <p class="goToDescription"><a href="#details" class="scroll-to">Scroll to product details, material & care and sizing</a></p>
                                <p class="price">Current Bid : Rs <?php echo number_format($row['CurrentPrice'], 2); ?></p>
                                <form action="" method="post" align="center">
                                    <input type="text" name="bidValue" /><br><br>
                                    <p align="center">Enter a value greater than Rs <?php echo number_format($leastValue, 2); ?> </p>
                                    <input class="btn btn-primary" type="submit" align="center" value="Bid Now"><br/><br>
                                </form>
                                <div style="color:red" align="center">
                                    <?php
                                    function updater($value, $id, $leastValue, $ExpectedValue, $userID)
                                    {
                                        $db = mysqli_connect('localhost', 'root', '', 'shop') or die('Error connecting to MySQL server.');
                                        $value = floatval($value); // Ensure $value is a float
                                        if ($value >= $leastValue && $value < $ExpectedValue) {
                                            $sql = "UPDATE item SET CurrentPrice='$value' WHERE ItemID='$id'";
                                            if (mysqli_query($db, $sql)) {
                                                echo '<div class="alert alert-success"><strong>Success!</strong> Your Bid Placed.</div>';
                                                echo "<meta http-equiv='refresh' content='0'>";
                                                $sql2 = "INSERT INTO bids (ItemID, BidderID, BidAmount) VALUES ('$id', '$userID', '$value')";
                                                if (!mysqli_query($db, $sql2)) {
                                                    echo "Error: " . $sql2 . "<br>" . mysqli_error($db);
                                                }
                                            } else {
                                                echo '<div class="alert alert-danger"><strong>Bid not Placed!</strong> The amount entered is not valid!</div>' . mysqli_error($db);
                                            }
                                        } else {
                                            echo '<div class="alert alert-danger"><strong>Bid not Placed!</strong> The amount entered is not valid!</div>';
                                        }
                                        mysqli_close($db);
                                    }

                                    if (isset($_POST['bidValue'])) {
                                        if (isset($_SESSION['userid'])) {
                                            updater($_POST['bidValue'], $ItemNo, $leastValue, $ExpectedValue, $_SESSION['userid']);
                                        } else {
                                            echo '<div class="alert alert-info"><strong>Sorry!</strong> You need to <a href="register.php">log in</a> to bid on items.</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <br>
                                <script type="text/javascript">
                                    var current = "Auction Ended!"; // Message to display when countdown ends
                                    var year = <?php echo date('Y', strtotime($row['EndTime'])); ?>;
                                    var month = <?php echo date('m', strtotime($row['EndTime'])); ?>;
                                    var day = <?php echo date('d', strtotime($row['EndTime'])); ?>;
                                    var hour = <?php echo date('H', strtotime($row['EndTime'])); ?>;
                                    var minute = <?php echo date('i', strtotime($row['EndTime'])); ?>;
                                    var tz = -5; // Timezone offset
                                </script>
                                <table id="table" border="0">
                                    <tr>
                                        <td align="center" colspan="6">
                                            <div class="numbers" id="count2" style="padding: 5px 0 0 0;"></div>
                                        </td>
                                    </tr>
                                    <tr id="spacer1">
                                        <td align="center"><div class="numbers"></div></td>
                                        <td align="center"><div class="numbers" id="dday"></div></td>
                                        <td align="center"><div class="numbers" id="dhour"></div></td>
                                        <td align="center"><div class="numbers" id="dmin"></div></td>
                                        <td align="center"><div class="numbers" id="dsec"></div></td>
                                        <td align="center"><div class="numbers"></div></td>
                                    </tr>
                                    <tr id="spacer2">
                                        <td align="center"><div class="title"></div></td>
                                        <td align="center"><div class="title" id="days">Days</div></td>
                                        <td align="center"><div class="title" id="hours">Hours</div></td>
                                        <td align="center"><div class="title" id="minutes">Minutes</div></td>
                                        <td align="center"><div class="title" id="seconds">Seconds</div></td>
                                        <td align="center"><div class="title"></div></td>
                                    </tr>
                                </table>
                                <br>
                                <p class="text-center buttons">
                                    <a href="" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Buy now for Rs <?php echo number_format($row['ExpectedPrice'], 2); ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="box" id="details">
                        <h4>Product details</h4>
                        <p><?php echo htmlspecialchars($row['Description']); ?></p>
                        <hr>
                    </div>

                    <?php
                    $count = 1;
                    $query2 = "SELECT * FROM item WHERE CategoryID = '{$row['CategoryID']}' AND ItemID != $ItemNo";
                    $result2 = mysqli_query($db, $query2);
                    if (!$result2) {
                        die('Error executing query: ' . mysqli_error($db));
                    }
                    ?>
                    <div class="row same-height-row">
                        <div class="col-md-3 col-sm-6">
                            <div class="box same-height">
                                <h3>You may also like these products</h3>
                            </div>
                        </div>
                        <?php
                        while ($count <= 3 && $row2 = mysqli_fetch_assoc($result2)) {
                        ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="product same-height">
                                    <div class="flip-container">
                                        <div class="flipper">
                                            <div class="front">
                                                <a href="detail.php?ItemNo=<?php echo $row2['ItemID']; ?>">
                                                    <img src="<?php echo htmlspecialchars($row2['PhotosID']); ?>" alt="" class="img-responsive">
                                                </a>
                                            </div>
                                            <div class="back">
                                                <a href="detail.php?ItemNo=<?php echo $row2['ItemID']; ?>">
                                                    <img src="<?php echo htmlspecialchars($row2['PhotosID']); ?>" alt="" class="img-responsive">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="detail.php?ItemNo=<?php echo $row2['ItemID']; ?>" class="invisible">
                                        <img src="img/product2.jpg" alt="" class="img-responsive">
                                    </a>
                                    <div class="text">
                                        <h3><?php echo htmlspecialchars($row2['ItemName']); ?></h3>
                                        <p class="price">Rs : <?php echo number_format($row2['CurrentPrice'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $count++;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

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
