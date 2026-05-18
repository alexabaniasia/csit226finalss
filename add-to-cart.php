<?php
    session_start();
    include 'connect.php';

    if(!isset($_SESSION['userID']) || strtolower($_SESSION['role']) == 'admin'){
        header("Location: login.php");
        exit();
    }

    if(isset($_POST['add_to_cart']) && isset($_POST['listing_id'])){
        $listingID = intval($_POST['listing_id']);
        $userID = $_SESSION['userID'];

        $check_owner = mysqli_query($connection, "SELECT i.ownerID FROM listings l JOIN items i ON l.itemID = i.itemID WHERE l.listingID = '$listingID'");
        $owner_data = mysqli_fetch_assoc($check_owner);
        
        if($owner_data && $owner_data['ownerID'] == $userID) {
            header("Location: view-item.php?id=$listingID&error=own_item");
            exit();
        }

        $cart_query = mysqli_query($connection, "SELECT cartID FROM carts WHERE userID = '$userID'");
        
        if(mysqli_num_rows($cart_query) > 0){
            $cart = mysqli_fetch_assoc($cart_query);
            $cartID = $cart['cartID'];
        } else {
            mysqli_query($connection, "INSERT INTO carts (userID) VALUES ('$userID')");
            $cartID = mysqli_insert_id($connection);
        }

        $insert_item = "INSERT INTO cart_items (cartID, listingID, quantity) VALUES ('$cartID', '$listingID', 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
        mysqli_query($connection, $insert_item);

        header("Location: cart.php");
        exit();
    } else {
        header("Location: browse.php");
        exit();
    }
?>