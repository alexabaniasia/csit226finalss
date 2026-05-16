<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    // Handle Deletion logic natively in PHP without alerts
    $msg = "";
    if(isset($_GET['delete_id'])){
        $del_id = intval($_GET['delete_id']);
        $sql_del = "DELETE FROM listings WHERE listingID = '$del_id' AND sellerID = '" . $_SESSION['userID'] . "'";
        if(mysqli_query($connection, $sql_del)){
            $msg = "Listing successfully deleted.";
        }
    }

    $activeListings = getUserListings($connection, $_SESSION['userID'], 'Active');
    $count = mysqli_num_rows($activeListings);
?>

<div class="page">
<div class="bg-image"></div>
<main class="account-main account-main--profile account-main--listings">
    <header class="profile-page-header">
    <p class="profile-page-eyebrow">Account · Listings</p>
    <h1 class="profile-page-title">My listings</h1>
    <p class="profile-page-lead">Manage the items you’ve posted to the marketplace.</p>
    </header>

    <section class="profile-card profile-card--section">
    <div class="profile-card-head" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 class="profile-section-heading">Active Items (<?php echo $count; ?>)</h3>
    </div>
    
    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="my-listings-grid" style="display: grid; gap: 15px;">
        
        <?php if($count == 0): ?>
            <p style="color: #888;">You don't have any active listings right now. <a href="list-item.php" style="color: #8B2635;">Create one!</a></p>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($activeListings)): ?>
                <article class="my-listing-card" style="border: 1px solid #e8d3d3; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p style="font-size: 13px; color: #666;">
                        Type: <?php echo ucfirst($row['type']); ?> · 
                        <?php if($row['type'] == 'borrow'): ?>
                            Free
                        <?php else: ?>
                            ₱<?php echo number_format($row['price'], 2); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="#" style="color: #666; font-size: 13px; font-weight: 600; margin-right: 15px;">Edit</a>
                    <a href="my-listings.php?delete_id=<?php echo $row['listingID']; ?>" style="color: #b3261e; font-size: 13px; font-weight: 600;" onclick="return confirm('Are you sure you want to delete this listing?');">Delete</a>
                </div>
                </article>
            <?php endwhile; ?>
        <?php endif; ?>

    </div>
    </section>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>