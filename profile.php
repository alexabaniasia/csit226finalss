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

    $isAdmin = (strtolower($user_data['role']) == 'admin');

    // Initialize stats variables
    $stat1_count = 0; $stat1_label = "";
    $stat2_count = 0; $stat2_label = "";
    $stat3_count = 0; $stat3_label = "";

    if ($isAdmin) {
        // --- ADMIN STATS ---
        $pending_listings = getPendingListings($connection);
        $stat1_count = $pending_listings ? mysqli_num_rows($pending_listings) : 0;
        $stat1_label = "Pending Listings";

        $reports_sql = mysqli_query($connection, "SELECT reportID FROM listing_reports WHERE status = 'open'");
        $stat2_count = $reports_sql ? mysqli_num_rows($reports_sql) : 0;
        $stat2_label = "Active Reports";

        $users_sql = mysqli_query($connection, "SELECT userID FROM users");
        $stat3_count = $users_sql ? mysqli_num_rows($users_sql) : 0;
        $stat3_label = "Total Users";
    } else {
        // --- STUDENT / USER STATS ---
        $active_listings = getUserListings($connection, $userID, 'active');
        $stat1_count = $active_listings ? mysqli_num_rows($active_listings) : 0;
        $stat1_label = "Active Listings";

        $cart_sql = "SELECT ci.cartItemID FROM cart_items ci JOIN carts c ON ci.cartID = c.cartID WHERE c.userID = '$userID'";
        $cart_res = mysqli_query($connection, $cart_sql);
        $stat2_count = $cart_res ? mysqli_num_rows($cart_res) : 0;
        $stat2_label = "Items in Cart";

        $stat3_count = "100%"; // Placeholder for Trust Rating
        $stat3_label = "Trust Rating";
    }
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; padding: 60px 20px; max-width: 1000px; margin: 0 auto;">
    
    <header style="margin-bottom: 30px;">
    <p style="color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Account</p>
    <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0;">
        <?php echo $isAdmin ? 'Admin Profile' : 'Profile'; ?>
    </h1>
    </header>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        
        <section style="background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center; height: fit-content;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #c0a0a0, #8b6060); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: white; margin: 0 auto 20px;">
                <?php echo strtoupper(substr($user_data['firstName'], 0, 1) . substr($user_data['lastName'], 0, 1)); ?>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: #1a1a1a; margin-bottom: 4px;"><?php echo htmlspecialchars($user_data['firstName'] . ' ' . $user_data['lastName']); ?></h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;"><?php echo htmlspecialchars($user_data['email']); ?></p>
            <div style="display: inline-block; background: <?php echo $isAdmin ? '#fce8e6' : '#f0f0f0'; ?>; color: <?php echo $isAdmin ? '#c5221f' : '#555'; ?>; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; margin-bottom: 25px;">
                Verified <?php echo ucfirst(htmlspecialchars($user_data['role'])); ?>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="edit-profile.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; color: #1a1a1a; text-decoration: none; font-weight: 700; transition: 0.2s;">Edit Profile</a>
                
                <?php if(!$isAdmin): ?>
                    <a href="list-item.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; background: #8B2635; color: #fff; text-decoration: none; font-weight: 700; transition: 0.2s;">New Listing</a>
                <?php else: ?>
                    <a href="admin.php" style="display: block; width: 100%; padding: 12px; border-radius: 8px; background: #1a1a1a; color: #fff; text-decoration: none; font-weight: 700; transition: 0.2s;">Go to Moderation</a>
                <?php endif; ?>
            </div>
        </section>

        <div style="display: flex; flex-direction: column; gap: 30px;">
            
            <section style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: #8B2635;"><?php echo $stat1_count; ?></div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;"><?php echo $stat1_label; ?></div>
                </div>
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: <?php echo ($isAdmin && $stat2_count > 0) ? '#b3261e' : '#8B2635'; ?>;"><?php echo $stat2_count; ?></div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;"><?php echo $stat2_label; ?></div>
                </div>
                <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center;">
                    <div style="font-size: 32px; font-weight: 800; color: #1f7a34;"><?php echo $stat3_count; ?></div>
                    <div style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase;"><?php echo $stat3_label; ?></div>
                </div>
            </section>

            <section style="background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <h3 style="font-size: 18px; font-weight: 800; color: #1a1a1a; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <?php echo $isAdmin ? 'Admin Tools' : 'Activity Dashboard'; ?>
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <?php if($isAdmin): ?>
                        <a href="admin.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                            <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">Review Submissions &rarr;</h4>
                            <p style="font-size: 13px; color: #666; margin: 0;">Approve or reject newly listed campus items.</p>
                        </a>
                        <a href="admin-reports.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                            <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">Moderation Queue &rarr;</h4>
                            <p style="font-size: 13px; color: #666; margin: 0;">Review listings flagged by the community.</p>
                        </a>
                        <a href="admin-users.php" style="display: block; padding: 20px; border: 1px solid #e8d3d3; border-radius: 12px; text-decoration: none; color: inherit; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#e8d3d3'">
                            <h4 style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 5px;">Manage Users &rarr;</h4>
                            <p style="font-size: 13px; color: #666; margin: 0;">Suspend accounts or monitor user behavior.</p>
                        </a>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

</main>
</div>

<?php require_once 'includes/footer.php'; ?>