<?php 
    $custom_css = 'style.css';
    session_start();
    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit;
    }
    
    include 'connect.php'; 
    require_once 'includes/header.php'; 
    
    $user_id = $_SESSION['userID'];

   $cart_query = mysqli_query($connection, "SELECT cartID FROM carts WHERE userID = $user_id");
    if(mysqli_num_rows($cart_query) == 0){
        mysqli_query($connection, "INSERT INTO carts (userID) VALUES ($user_id)");
        $cart_id = mysqli_insert_id($connection);
    } else {
        $cart_row = mysqli_fetch_assoc($cart_query);
        $cart_id = $cart_row['cartID'];
    }

    if(isset($_POST['action']) && $_POST['action'] == 'remove'){
        $remove_id = $_POST['cartItemID'];
        mysqli_query($connection, "DELETE FROM cart_items WHERE cartItemID = $remove_id AND cartID = $cart_id");
        echo "<script>alert('Item removed from cart.');</script>";
    }

    $items_query = "SELECT ci.cartItemID, ci.quantity, l.listingID, l.listingType, i.name, i.itemCondition 
                    FROM cart_items ci, listings l, items i 
                    WHERE ci.listingID = l.listingID 
                    AND l.itemID = i.itemID 
                    AND ci.cartID = $cart_id";
    $items_result = mysqli_query($connection, $items_query);
    $item_count = mysqli_num_rows($items_result);

    $subtotal = 0;
?>

<main style="max-width: 1100px; margin: 40px auto; padding: 20px;">
  <div style="margin-bottom: 30px;">
    <h1 style="color: #8B2635; margin: 0 0 5px 0; font-size: 32px;">Your Cart</h1>
    <p style="color: #666; margin: 0; font-size: 16px;">Review items before you contact sellers or checkout.</p>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
    
    <div>
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eaeaea; padding-bottom: 15px; margin-bottom: 25px;">
        <h2 style="margin: 0; font-size: 20px; color: #222;">Items</h2>
        <span style="background: #f0f0f0; padding: 6px 14px; border-radius: 20px; font-size: 14px; font-weight: bold; color: #555;"><?php echo $item_count; ?> items</span>
      </div>

      <?php if($item_count == 0): ?>
        <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px dashed #ccc;">
          <p style="color: #666; margin-bottom: 20px; font-size: 16px;">Your cart is empty.</p>
          <a href="browse-page.php" class="maroon-btn" style="padding: 12px 24px; text-decoration: none;">Browse listings</a>
        </div>
      <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php
                while($row = mysqli_fetch_assoc($items_result)): 
                    $item_price = 0; $price_display = '';
                    if($row['listingType'] == 'sale'){
                        $s_query = mysqli_query($connection, "SELECT price FROM sale_listings WHERE listingID = ".$row['listingID']);
                        $s_row = mysqli_fetch_assoc($s_query);
                        $item_price = $s_row['price'];
                        $price_display = '₱' . number_format($item_price, 2);
                    } else if($row['listingType'] == 'rent'){
                        $r_query = mysqli_query($connection, "SELECT rentalPricePerDay FROM rental_listings WHERE listingID = ".$row['listingID']);
                        $r_row = mysqli_fetch_assoc($r_query);
                        $item_price = $r_row['rentalPricePerDay'];
                        $price_display = '₱' . number_format($item_price, 2) . ' <span style="font-size:14px; color:#888;">/day</span>';
                    } else if($row['listingType'] == 'borrow'){
                        $price_display = '<span style="color:#2e7d32;">Free</span>';
                    }
                    $subtotal += ($item_price * $row['quantity']);
            ?>
            
            <div class="feature-card" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
                <div>
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #222;"><?php echo $row['name']; ?></h3>
                    <p style="margin: 0; color: #666; font-size: 14px;">For <strong style="text-transform: capitalize;"><?php echo $row['listingType']; ?></strong> &nbsp;|&nbsp; Qty: <?php echo $row['quantity']; ?></p>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 800; font-size: 20px; color: #1a1a1a; margin-bottom: 10px;"><?php echo $price_display; ?></div>
                    <form method="post">
                        <input type="hidden" name="cartItemID" value="<?php echo $row['cartItemID']; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" style="background: none; border: none; color: #b3261e; text-decoration: underline; font-weight: bold; font-size: 13px; cursor: pointer;">Remove</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

    <aside style="background: #fff; padding: 30px; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 8px 24px rgba(0,0,0,0.04); position: sticky; top: 100px;">
      <h2 style="margin: 0 0 20px 0; border-bottom: 2px solid #eaeaea; padding-bottom: 15px; font-size: 22px;">Order Summary</h2>
      
      <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; color: #555;">
        <span>Subtotal</span>
        <span style="font-weight: 600; color: #222;">₱<?php echo number_format($subtotal, 2); ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 16px; color: #555;">
        <span>Campus handoff</span>
        <span style="font-weight: 600; color: #2e7d32;">Free</span>
      </div>
      
      <div style="display: flex; justify-content: space-between; border-top: 2px solid #eaeaea; padding-top: 20px; font-weight: 800; font-size: 24px; color: #1a1a1a; margin-bottom: 25px;">
        <span>Total</span>
        <span>₱<?php echo number_format($subtotal, 2); ?></span>
      </div>
      
      <button class="maroon-btn" style="width: 100%; padding: 16px; font-size: 16px;" onclick="alert('Checkout integration coming soon!');">Proceed to Checkout</button>
      
      <p style="font-size: 13px; color: #888; text-align: center; margin-top: 20px; line-height: 1.5;">Maroon Market connects you with sellers. Final payment and meetups are arranged directly.</p>
    </aside>

  </div>
</main>

<?php require_once 'includes/footer.php'; ?>