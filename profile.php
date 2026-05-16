<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    // Redirect guest to login
    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    $userID = $_SESSION['userID'];
    
    // Get user details
    $user_sql = "SELECT * FROM users WHERE userID = '$userID'";
    $user_res = mysqli_query($connection, $user_sql);
    $user_data = mysqli_fetch_assoc($user_res);

    // Get active listing count
    $active_listings = getUserListings($connection, $userID, 'active');
    $active_count = $active_listings ? mysqli_num_rows($active_listings) : 0;

    // Get cart items count safely (Fixed from original code)
    $cart_sql = "SELECT ci.cartItemID FROM cart_items ci JOIN carts c ON ci.cartID = c.cartID WHERE c.userID = '$userID'";
    $cart_res = mysqli_query($connection, $cart_sql);
    $saved_count = $cart_res ? mysqli_num_rows($cart_res) : 0;
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; padding: 60px 20px; max-width: 1000px; margin: 0 auto;">
    
    <header style="margin-bottom: 30px;">
    <p style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Account</p>
    <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0;">Profile</h1>
    </header>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        
        <section style="background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #c0a0a0, #8b6060); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: white; margin: 0 auto 20px;">
                <?php echo strtoupper(substr($user_data['firstName'], 0, 1) . substr($user_data['lastName'], 0, 1)); ?>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px;"><?php echo htmlspecialchars($user_data['firstName'] . ' ' . $user_data['lastName']); ?></h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;"><?php echo htmlspecialchars($user_data['email']); ?></p>
            <div style="display: inline-block; background: #f0f0f0; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; color: #555; margin-bottom: 25px;">
                Verified <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="edit-profile.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; color: #1a1a1a; text-decoration: none; font-weight: 700; transition: 0.2s;">Edit Profile</a>
                <a href="list-item.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; background: #8B2635; color: #fff; text-decoration: none; font-weight: 700; transition: 0.2s;">New Listing</a>
                <?php if($user_data['role'] == 'admin'): ?>
                    <a href="admin.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; background: #1a1a1a; color: #fff; text-decoration: none; font-weight: 700; transition: 0.2s;">Moderate Listings</a>
                <?php endif; ?>
            </div>
        </section>

        <div style="display: flex; flex-direction: column; gap: 30px;">
            <section style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: #8B2635;"><?php echo $active_count; ?></div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Active Listings</div>
                </div>
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: #8B2635;"><?php echo $saved_count; ?></div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Items in Cart</div>
                </div>
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: #1f7a34;">100%</div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;">Trust Rating</div>
                </div>
            </section>

            <section style="background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <h3 style="font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Activity Dashboard</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <a href="my-listings.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                        <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">My Listings &rarr;</h4>
                        <p style="font-size: 13px; color: #666; margin: 0;">Manage items you've posted.</p>
                    </a>
                    <a href="seller-dashboard.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                        <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">Seller Dashboard &rarr;</h4>
                        <p style="font-size: 13px; color: #666; margin: 0;">Track incoming buyer requests.</p>
                    </a>
                    <a href="transaction-history.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                        <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">Transaction History &rarr;</h4>
                        <p style="font-size: 13px; color: #666; margin: 0;">View past purchases and sales.</p>
                    </a>
                    <a href="fines.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                        <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">My Fines &rarr;</h4>
                        <p style="font-size: 13px; color: #666; margin: 0;">Manage penalties from late returns.</p>
                    </a>
                </div>
            </section>
        </div>
    </div>

</main>
</div>

<?php require_once 'includes/footer.php'; ?>