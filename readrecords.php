<?php
    include 'connect.php';
    
    if (!$connection) {
        die('Could not connect: ' . mysqli_connect_error());
    }
    
    $featured_query = "SELECT l.listingID, l.listingType as type, l.listingStatus as status, 
                            i.name as title, i.itemCondition as `condition`, i.category, 
                            u.firstName, u.lastName,
                            IF(l.listingType = 'sale', (SELECT price FROM sale_listings WHERE listingID = l.listingID),
                                IF(l.listingType = 'rental', (SELECT rentalPricePerDay FROM rental_listings WHERE listingID = l.listingID), 0.00)
                            ) as price,
                            IF(l.listingType = 'rental', (SELECT maxDays FROM rental_listings WHERE listingID = l.listingID),
                                IF(l.listingType = 'borrow', (SELECT maxDays FROM borrow_listings WHERE listingID = l.listingID), 0)
                            ) as maxDays,
                            (SELECT imagePath FROM listing_images WHERE listingID = l.listingID ORDER BY sortOrder ASC LIMIT 1) as imagePath
                    FROM listings l
                    JOIN items i ON l.itemID = i.itemID 
                    JOIN users u ON i.ownerID = u.userID 
                    WHERE l.listingStatus = 'active' 
                    ORDER BY l.datePosted DESC LIMIT 6";
    $resultset_featured = mysqli_query($connection, $featured_query);

    $browse_query = "SELECT l.listingID, l.listingType as type, l.listingStatus as status, 
                            i.name as title, i.itemCondition as `condition`, i.category, 
                            u.firstName, u.lastName,
                            IF(l.listingType = 'sale', (SELECT price FROM sale_listings WHERE listingID = l.listingID),
                            IF(l.listingType = 'rental', (SELECT rentalPricePerDay FROM rental_listings WHERE listingID = l.listingID), 0.00)
                            ) as price,
                            IF(l.listingType = 'rental', (SELECT maxDays FROM rental_listings WHERE listingID = l.listingID),
                            IF(l.listingType = 'borrow', (SELECT maxDays FROM borrow_listings WHERE listingID = l.listingID), 0)
                            ) as maxDays,
                            (SELECT imagePath FROM listing_images WHERE listingID = l.listingID ORDER BY sortOrder ASC LIMIT 1) as imagePath
                    FROM listings l
                    JOIN items i ON l.itemID = i.itemID 
                    JOIN users u ON i.ownerID = u.userID 
                    WHERE l.listingStatus = 'active'";

    if(isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = mysqli_real_escape_string($connection, trim($_GET['search']));
        $browse_query .= " AND (i.name LIKE '%$search%' OR i.description LIKE '%$search%')";
    }
    
    if(isset($_GET['type']) && is_array($_GET['type']) && count($_GET['type']) > 0) {
        $types = array_map(function($t) use ($connection) { return "'" . mysqli_real_escape_string($connection, $t) . "'"; }, $_GET['type']);
        $types_str = implode(",", $types);
        $browse_query .= " AND l.listingType IN ($types_str)";
    }
    
    if(isset($_GET['category']) && is_array($_GET['category']) && count($_GET['category']) > 0) {
        $cats = array_map(function($c) use ($connection) { return "'" . mysqli_real_escape_string($connection, $c) . "'"; }, $_GET['category']);
        $cats_str = implode(",", $cats);
        $browse_query .= " AND i.category IN ($cats_str)";
    }

    $browse_query .= " ORDER BY l.datePosted DESC";
    $resultset_browse = mysqli_query($connection, $browse_query);

    function getUserCart($conn, $userID) {
        $query = "SELECT ci.cartItemID as cartID, ci.quantity, 
                        l.listingID, l.listingType as type, 
                        i.name as title, i.itemCondition as condition_item, 
                        u.firstName,
                        IF(l.listingType = 'sale', 
                            (SELECT price FROM sale_listings WHERE listingID = l.listingID),
                            IF(l.listingType = 'rent', 
                            (SELECT rentalPricePerDay FROM rental_listings WHERE listingID = l.listingID), 
                            0.00)
                        ) as price
                FROM carts c, cart_items ci, listings l, items i, users u 
                WHERE c.cartID = ci.cartID 
                AND ci.listingID = l.listingID 
                AND l.itemID = i.itemID 
                AND i.ownerID = u.userID 
                AND c.userID = " . intval($userID);
        return mysqli_query($conn, $query);
    }

    function getUserListings($conn, $ownerID, $status = 'active') {
        $query = "SELECT l.listingID, l.listingType as type, l.listingStatus as status, 
                        i.name as title, i.itemCondition as condition_item,
                        IF(l.listingType = 'sale', 
                            (SELECT price FROM sale_listings WHERE listingID = l.listingID),
                            IF(l.listingType = 'rent', 
                            (SELECT rentalPricePerDay FROM rental_listings WHERE listingID = l.listingID), 
                            0.00)
                        ) as price
                FROM listings l, items i 
                WHERE l.itemID = i.itemID 
                AND i.ownerID = " . intval($ownerID) . " 
                AND l.listingStatus = '" . mysqli_real_escape_string($conn, $status) . "' 
                ORDER BY l.datePosted DESC";
        return mysqli_query($conn, $query);
    }

    function getSellerRequests($conn, $sellerID) {
        $query = "SELECT t.transactionID, t.status AS txnStatus, t.amount, 
                        i.name as title, l.listingType as type, 
                        u.firstName AS buyerName 
                FROM transactions t
                JOIN users u ON t.senderID = u.userID
                LEFT JOIN listings l ON t.listingID = l.listingID
                LEFT JOIN items i ON l.itemID = i.itemID
                WHERE t.receiverID = " . intval($sellerID) . "
                ORDER BY t.checkoutDate DESC";
        return mysqli_query($conn, $query);
    }

    function getPendingListings($conn) {
        $query = "SELECT s.*, u.firstName, u.lastName 
                FROM listing_submissions s, users u 
                WHERE s.submitterID = u.userID 
                AND s.status = 'pending' 
                ORDER BY s.createdAt ASC";
        return mysqli_query($conn, $query);
    }

    function getUserTransactionHistory($conn, $userID) {
        $query = "SELECT t.transactionID, t.amount, t.status AS txnStatus, t.checkoutDate as created_at, 
                        t.senderID, i.name as title, l.listingType as type, 
                        sender.firstName AS buyerName, receiver.firstName AS sellerName
                FROM transactions t
                JOIN users sender ON t.senderID = sender.userID
                JOIN users receiver ON t.receiverID = receiver.userID
                LEFT JOIN listings l ON t.listingID = l.listingID
                LEFT JOIN items i ON l.itemID = i.itemID
                WHERE (t.senderID = " . intval($userID) . " OR t.receiverID = " . intval($userID) . ")
                ORDER BY t.checkoutDate DESC";
        return mysqli_query($conn, $query);
    }
?>