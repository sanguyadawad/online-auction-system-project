<?php session_start(); ?>
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
    <!-- styles -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/owl.theme.css" rel="stylesheet">
    <!-- theme stylesheet -->
    <link href="css/style.default.css" rel="stylesheet" id="theme-stylesheet">
    <!-- your stylesheet with modifications -->
    <link href="css/custom.css" rel="stylesheet">

    <script src="js/respond.min.js"></script>
    <link rel="shortcut icon" href="favicon.png">
</head>

<body>

    <?php
    $db = mysqli_connect('localhost', 'root', '', 'shop') or die('Error connecting to MySQL server.');
    $query1 = "SELECT * FROM category";
    $result1 = mysqli_query($db, $query1);
    ?>

    <?php include 'header.php'; ?>

    <div class="navbar-collapse collapse" id="navigation">
        <ul class="nav navbar-nav navbar-left">
            <?php while ($categories = mysqli_fetch_assoc($result1)) { ?>
                <li class="inactive">
                    <a href="category.php?CategoryID=<?php echo $categories['CategoryID']; ?>">
                        <?php echo $categories['Category']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <!--/.nav-collapse -->

    <div id="all">
        <div id="content">
            <div class="container">
                <div class="col-md-12">
                    <div id="main-slider">
                        <div class="item">
                            <img src="img/main-slider1.jpg" alt="" class="img-responsive">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/main-slider2.jpg" alt="">
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="img/main-slider3.jpg" alt="">
                        </div>
                    </div>
                    <!-- /#main-slider -->
                </div>
            </div>

            <!-- *** ADVANTAGES HOMEPAGE ***
            _________________________________________________________ -->
            <div id="advantages">
                <!-- /.container -->
            </div>
            <!-- /#advantages -->

            <!-- *** ADVANTAGES END *** -->

            <!-- *** HOT PRODUCT SLIDESHOW ***
            _________________________________________________________ -->
            <?php
            $query = "SELECT * FROM item ORDER BY ItemID DESC";
            $result = mysqli_query($db, $query);
            ?>

            <div id="hot">
                <div class="container">
                    <div class="product-slider">
                        <?php
                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result) && $count <= 10) {
                        ?>
                                <!-- /.product -->
                            </div>
                            <!-- /.item -->
                        <?php
                            $count++;
                        }
                        ?>
                    </div>
                    <!-- /.product-slider -->
                </div>
                <!-- /.container -->
            </div>
            <!-- /#hot -->
        </div>
        <!-- /#content -->

        <?php include 'footer.php'; ?>
    </div>
    <!-- /#all -->

    <!-- *** SCRIPTS TO INCLUDE ***
    _________________________________________________________ -->
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
