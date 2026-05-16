<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    // Redirect to login if guest tries to access this page
    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    // Block Admins from accessing marketplace user features
    if(strtolower($_SESSION['role']) == 'admin'){
        header("Location: admin.php");
        exit();
    }

    // Handle removing item from cart
    if(isset($_GET['remove_cart_id'])){
        $cartItemID = intval($_GET['remove_cart_id']);
        $userID = $_SESSION['userID'];
        
        // Correctly deletes the specific item from the cart_items table
        $delete_sql = "DELETE ci FROM cart_items ci 
                    JOIN carts c ON ci.cartID = c.cartID 
                    WHERE ci.cartItemID = '$cartItemID' AND c.userID = '$userID'";
        mysqli_query($connection, $delete_sql);
        header("Location: cart.php"); // Refresh page
        exit();
    }

    $cartItems = getUserCart($connection, $_SESSION['userID']);
    $totalItems = mysqli_num_rows($cartItems);
    $subtotal = 0.00;
?>

<div class="page">
<div class="bg-image"></div>
<main class="main" style="max-width: 1200px; margin: 0 auto; padding-top: 40px;">
    
    <h1 style="font-family: 'Nunito', sans-serif; font-size: 32px; font-weight: 800; color: #1a1a1a;">Your cart</h1>
    <p style="color: #666; margin-bottom: 28px;">Review items before you contact sellers or complete checkout.</p>

    <div style="display: flex; gap: 40px; align-items: flex-start;">
    
    <div style="flex: 2; background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e8e0e0;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="font-size: 20px; font-weight: 700;">Items</h2>
        <span style="background: #f0f0f0; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;"><?php echo $totalItems; ?> items</span>
        </div>

        <?php if($totalItems == 0): ?>
            <div style="text-align: center; padding: 40px 0; color: #888;">
            <p style="margin-bottom: 15px;">Your cart is empty. Browse listings to add rentals, purchases, or borrows.</p>
            <a href="browse.php" style="color: #8B2635; font-weight: 700;">Browse listings &rarr;</a>
            </div>
        <?php else: ?>
            
            <?php while($row = mysqli_fetch_assoc($cartItems)): 
                // Calculate subtotal
                $lineTotal = $row['price'] * $row['quantity'];
                $subtotal += $lineTotal;
            ?>
                <div style="display: flex; gap: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
                    <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px;"></div>
                    <div style="flex: 1;">
                        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p style="font-size: 13px; color: #888;">For <?php echo ucfirst($row['type']); ?> · Condition: <?php echo $row['condition_item']; ?></p>
                        <p style="font-size: 12px; color: #666; margin-top: 4px;">Seller: <?php echo $row['firstName']; ?></p>
                    </div>
                    
                    <div style="font-size: 16px; font-weight: 700;">
                        <?php if($row['type'] == 'borrow'): ?>
                            <span style="color: #3a8a3a;">Free</span>
                        <?php else: ?>
                            ₱<?php echo number_format($row['price'], 2); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <a href="cart.php?remove_cart_id=<?php echo $row['cartID']; ?>" style="color: #b3261e; font-size: 14px; font-weight: 600; text-decoration: none;">Remove</a>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php endif; ?>
    </div>

    <aside style="flex: 1; background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e8e0e0;">
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">Order summary</h2>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #555;">
        <span>Subtotal</span>
        <span>₱<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; color: #555;">
        <span>Estimated campus handoff</span>
        <span style="color: #3a8a3a; font-weight: 600;">Free</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 18px; font-weight: 800; border-top: 1px solid #eee; padding-top: 15px;">
        <span>Total</span>
        <span>₱<?php echo number_format($subtotal, 2); ?></span>
        </div>

        <button type="button" class="primary-btn" <?php if($totalItems == 0) echo 'disabled style="background: #ccc; cursor: not-allowed;"'; ?>>Proceed to checkout</button>
        <p style="font-size: 12px; color: #888; text-align: center; margin-top: 15px;">Maroon Market connects you with sellers. Final payment and meetups are arranged between you and the listing owner.</p>
    </aside>

    </div>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>