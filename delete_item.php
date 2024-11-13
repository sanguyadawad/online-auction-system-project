<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'shop') or die('Error connecting to MySQL server.');

if (isset($_GET['ItemID']) && isset($_GET['CategoryID'])) {
    $ItemID = intval($_GET['ItemID']); // Ensure ItemID is an integer to prevent SQL injection
    $CategoryID = intval($_GET['CategoryID']); // Ensure CategoryID is an integer to prevent SQL injection

    // Delete the item
    $query = "DELETE FROM item WHERE ItemID = $ItemID";
    $result = mysqli_query($db, $query);

    if ($result) {
        // Redirect back to the category page
        header("Location: category.php?CategoryID=$CategoryID");
        exit;
    } else {
        echo 'Error deleting item.';
    }
} else {
    echo 'ItemID or CategoryID is missing.';
}
?>
