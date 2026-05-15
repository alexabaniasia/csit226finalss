<?php 
    $custom_css = 'style.css';
    session_start();
    include 'connect.php'; 
    
    if(!isset($_GET['id'])){
        echo "<script>alert('No item selected.'); window.location.href='browse-page.php';</script>";
        exit;
    }
    
    $listing_id = $_GET['id'];

    $query = "SELECT l.*, i.name, i.description, i.itemCondition, i.category, u.firstName, u.lastName 
              FROM listings l, items i, users u 
              WHERE l.itemID = i.itemID 
              AND i.ownerID = u.userID 
              AND l.listingID = $listing_id";
    $result = mysqli_query($connection, $query);
    
    if(mysqli_num_rows($result) == 0){
        echo "<script>alert('Item not found.'); window.location.href='browse-page.php';</script>";
        exit;
    }
    
    $item = mysqli_fetch_assoc($result);
    $type = $item['listingType'];

    $price_display = '';
    $deposit_display = '';
    $max_days_display = '';
    
    if($type == 'sale'){
        $s_query = mysqli_query($connection, "SELECT price FROM sale_listings WHERE listingID = $listing_id");
        $s_row = mysqli_fetch_assoc($s_query);
        $price_display = '₱' . number_format($s_row['price'], 2);
        $badge_color = '#af5353';
        $badge_text = 'For Sale';
        $btn_text = 'Add to Cart';
    } else if($type == 'rent'){
        $r_query = mysqli_query($connection, "SELECT rentalPricePerDay, deposit, maxDays FROM rental_listings WHERE listingID = $listing_id");
        $r_row = mysqli_fetch_assoc($r_query);
        $price_display = '₱' . number_format($r_row['rentalPricePerDay'], 2) . ' <span style="font-size: 16px; color: #888; font-weight: normal;">/ day</span>';
        $deposit_display = 'Refundable deposit: <strong>₱' . number_format($r_row['deposit'], 2) . '</strong>';
        $max_days_display = $r_row['maxDays'] . ' days max';
        $badge_color = '#4a79d6';
        $badge_text = 'For Rent';
        $btn_text = 'Add to Cart (Rental)';
    } else if($type == 'borrow'){
        $b_query = mysqli_query($connection, "SELECT maxDays FROM borrow_listings WHERE listingID = $listing_id");
        $b_row = mysqli_fetch_assoc($b_query);
        $price_display = '<span style="color: #3a8a3a;">Free</span>';
        $max_days_display = $b_row['maxDays'] . ' days max';
        $badge_color = '#9a6b2b';
        $badge_text = 'For Borrow';
        $btn_text = 'Add to Cart (Borrow)';
    }

    if(isset($_POST['btnAddToCart'])){
        if(!isset($_SESSION['userID'])){
            echo "<script>alert('Please log in to add items to your cart.'); window.location.href='login.php';</script>";
        } else {
            $user_id = $_SESSION['userID'];
            
            // Get or create cart
            $cart_query = mysqli_query($connection, "SELECT cartID FROM carts WHERE userID = $user_id");
            if(mysqli_num_rows($cart_query) == 0){
                mysqli_query($connection, "INSERT INTO carts (userID) VALUES ($user_id)");
                $cart_id = mysqli_insert_id($connection);
            } else {
                $cart_row = mysqli_fetch_assoc($cart_query);
                $cart_id = $cart_row['cartID'];
            }
            
            $check_cart = mysqli_query($connection, "SELECT cartItemID FROM cart_items WHERE cartID = $cart_id AND listingID = $listing_id");
            if(mysqli_num_rows($check_cart) > 0){
                echo "<script>alert('This item is already in your cart!');</script>";
            } else {
                mysqli_query($connection, "INSERT INTO cart_items (cartID, listingID, quantity) VALUES ($cart_id, $listing_id, 1)");
                echo "<script>alert('Item added to cart!'); window.location.href='cart.php';</script>";
            }
        }
    }

    require_once 'includes/header.php'; 
?>

<main style="max-width: 900px; margin: 40px auto; padding: 20px;">
  <a href="browse-page.php" style="color: #8B2635; text-decoration: none; font-size: 14px; font-weight: bold;">← Back to Browse</a>
  
  <div style="background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #ccc; margin-top: 15px; display: flex; flex-wrap: wrap; gap: 30px;">
    
    <div style="flex: 1; min-width: 300px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; min-height: 350px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.2" style="width: 80px; height: 80px;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 4-4 4 4 4-4 4 4"/><circle cx="8.5" cy="15.5" r="1.5"/></svg>
    </div>

    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
        
        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <div style="font-weight: bold; color: #555;">Listed by: <?php echo $item['firstName'] . ' ' . $item['lastName']; ?></div>
            <div style="font-size: 13px; color: #888;">Verified Campus Seller</div>
        </div>

        <div style="margin-bottom: 20px;">
            <div style="background: <?php echo $badge_color; ?>; color: white; font-size: 12px; font-weight: bold; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-bottom: 10px;">
                <?php echo $badge_text; ?>
            </div>
            <h1 style="font-size: 26px; margin: 0; color: #333;"><?php echo $item['name']; ?></h1>
        </div>

        <div style="background: #f4f4f4; padding: 20px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 20px;">
            <div style="font-size: 32px; font-weight: 800; color: #8B2635; margin-bottom: 5px;">
                <?php echo $price_display; ?>
            </div>
            <?php if($deposit_display): ?>
                <div style="font-size: 14px; color: #555;"><?php echo $deposit_display; ?></div>
            <?php endif; ?>
        </div>

        <form method="post" style="margin-bottom: 25px;">
            <button type="submit" name="btnAddToCart" style="width: 100%; padding: 14px; background: #8B2635; color: white; border: none; border-radius: 4px; font-weight: bold; font-size: 16px; cursor: pointer;">
                <?php echo $btn_text; ?>
            </button>
        </form>

        <div>
            <h3 style="font-size: 16px; border-bottom: 2px solid #8B2635; padding-bottom: 5px; margin-bottom: 10px; color: #8B2635;">Item Details</h3>
            <ul style="list-style: none; padding: 0; font-size: 14px; color: #333; margin-bottom: 15px;">
                <li style="margin-bottom: 5px;"><strong>Category:</strong> <?php echo ucfirst($item['category']); ?></li>
                <li style="margin-bottom: 5px;"><strong>Condition:</strong> <?php echo ucfirst($item['itemCondition']); ?></li>
                <li style="margin-bottom: 5px;"><strong>Location:</strong> <?php echo $item['location']; ?></li>
                <?php if($max_days_display): ?>
                    <li style="margin-bottom: 5px;"><strong>Duration:</strong> <?php echo $max_days_display; ?></li>
                <?php endif; ?>
            </ul>
            
            <h4 style="font-size: 14px; color: #555; margin-bottom: 5px;">Description</h4>
            <p style="font-size: 14px; color: #333; line-height: 1.6; background: #f9f9f9; padding: 15px; border: 1px solid #eee; border-radius: 4px;">
                <?php echo nl2br($item['description']); ?>
            </p>
        </div>

    </div>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>