<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

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
<main style="position: relative; z-index: 10; max-width: 1000px; margin: 0 auto; padding: 10px 20px 60px;">

    <header style="margin-bottom: 40px; text-align: center;">
        <p style="color: #8B2635; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Account &middot; Listings</p>
        <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0;">My Listings</h1>
        <p style="color: #666; font-size: 16px; margin: 0;">Manage the items you’ve posted to the marketplace.</p>
    </header>

    <section style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">

        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Nunito', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0;">Active Items (<?php echo $count; ?>)</h3>
            <a href="profile.php" style="color: #8B2635; font-size: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Profile
            </a>
        </div>
        
        <?php if($msg != ""): ?>
            <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; font-size: 14px;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; gap: 16px;">
            <?php if($count == 0): ?>
                <div style="text-align: center; padding: 40px 20px; color: #888; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
                    <p style="margin: 0 0 10px 0; font-size: 15px; font-weight: 500;">You don't have any active listings right now.</p>
                    <a href="list-item.php" style="color: #8B2635; font-weight: 700; text-decoration: none;">Create one! &rarr;</a>
                </div>
            <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($activeListings)): ?>
                    <article style="border: 1px solid #eaeaea; padding: 20px; border-radius: 12px; background: #fff; display: flex; justify-content: space-between; align-items: center; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#eaeaea'">
                        <div>
                            <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 6px; color: #1a1a1a;"><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p style="font-size: 13px; color: #555; margin: 0;">
                                Type: <strong style="color: #444;"><?php echo ucfirst($row['type']); ?></strong> &middot; 
                                <?php if($row['type'] == 'borrow'): ?>
                                    <span style="color: #1f7a34; font-weight: 700;">Free</span>
                                <?php else: ?>
                                    Price: ₱<?php echo number_format($row['price'], 2); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <a href="edit-listing.php?id=<?php echo $row['listingID']; ?>" style="color: #555; font-size: 14px; font-weight: 700; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#1a1a1a'" onmouseout="this.style.color='#555'">Edit</a>
                            <a href="my-listings.php?delete_id=<?php echo $row['listingID']; ?>" style="color: #b3261e; font-size: 14px; font-weight: 700; text-decoration: none; padding: 6px 12px; background: #fff5f5; border-radius: 6px; transition: 0.2s;" onmouseover="this.style.background='#fce8e6'" onmouseout="this.style.background='#fff5f5'" onclick="showModal('Are you sure you want to delete this listing?', this.href, event);">Delete</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>