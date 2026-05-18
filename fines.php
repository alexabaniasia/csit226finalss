<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    $userID = $_SESSION['userID'];
    $msg = "";

    if(isset($_GET['pay_fine'])){
        $fineID = intval($_GET['pay_fine']);
        $update_sql = "UPDATE fines f JOIN returnable_transactions rt ON f.transactionID = rt.transactionID JOIN transactions t ON rt.transactionID = t.transactionID SET f.isPaid = 1 WHERE f.fineID = '$fineID' AND t.senderID = '$userID'";
        
        if(mysqli_query($connection, $update_sql)){
            $msg = "Fine paid successfully.";
        }
    }

    $fines_query = "SELECT f.fineID, f.amount, f.reason, f.isPaid, i.name as itemName, t.checkoutDate
                    FROM fines f
                    JOIN returnable_transactions rt ON f.transactionID = rt.transactionID
                    JOIN transactions t ON rt.transactionID = t.transactionID
                    JOIN listings l ON t.listingID = l.listingID
                    JOIN items i ON l.itemID = i.itemID
                    WHERE t.senderID = '$userID'
                    ORDER BY f.isPaid ASC, t.checkoutDate DESC";
                    
    $fines_result = mysqli_query($connection, $fines_query);
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; max-width: 1000px; margin: 0 auto; padding: 10px 20px 60px;">
    <!-- Cleaned up Page Header -->
    <header style="margin-bottom: 40px; text-align: center;">
        <p style="color: #8B2635; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Account &middot; Penalties</p>
        <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0;">My Fines</h1>
        <p style="color: #666; font-size: 16px; margin: 0;">Manage penalties from late returns or damaged campus rentals.</p>
    </header>

    <section style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">

        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Nunito', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0;">Fine History</h3>
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
            <?php if(mysqli_num_rows($fines_result) == 0): ?>
                <div style="text-align: center; padding: 40px 20px; color: #888; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
                    <p style="margin: 0; font-size: 15px; font-weight: 500;">You have no fines on your account. Great job!</p>
                </div>
            <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($fines_result)): ?>
                    <article style="border: 1px solid #eaeaea; padding: 20px; border-radius: 12px; background: <?php echo $row['isPaid'] ? '#fafafa' : '#fff'; ?>; display: flex; justify-content: space-between; align-items: center; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#eaeaea'">
                        <div style="opacity: <?php echo $row['isPaid'] ? '0.6' : '1'; ?>;">
                            <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 6px; color: #1a1a1a;">
                                Item: <?php echo htmlspecialchars($row['itemName']); ?>
                            </h4>
                            <p style="font-size: 13px; color: #555; margin: 0;">
                                <strong style="color: #444;">Reason:</strong> <?php echo htmlspecialchars($row['reason']); ?> &middot; 
                                Issued: <?php echo date('M d, Y', strtotime($row['checkoutDate'])); ?>
                            </p>
                        </div>
                        
                        <div style="text-align: right;">
                            <div style="font-size: 18px; font-weight: 800; color: #b3261e; margin-bottom: 8px;">
                                ₱<?php echo number_format($row['amount'], 2); ?>
                            </div>
                            <?php if($row['isPaid']): ?>
                                <span style="font-size: 12px; font-weight: 700; color: #1f7a34; background: #e6f4ea; padding: 4px 12px; border-radius: 20px;">Paid</span>
                            <?php else: ?>
                                <a href="fines.php?pay_fine=<?php echo $row['fineID']; ?>" style="background: #1a1a1a; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#8B2635'" onmouseout="this.style.background='#1a1a1a'" onclick="showModal('Confirm payment for this fine?', this.href, event);">Pay Now</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>