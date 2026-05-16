<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    $userID = $_SESSION['userID'];
    $msg = "";

    // Simulate paying a fine
    if(isset($_GET['pay_fine'])){
        $fineID = intval($_GET['pay_fine']);
        // Only update if the user actually owns the transaction this fine belongs to
        $update_sql = "UPDATE fines f JOIN returnable_transactions rt ON f.transactionID = rt.transactionID JOIN transactions t ON rt.transactionID = t.transactionID SET f.isPaid = 1 WHERE f.fineID = '$fineID' AND t.senderID = '$userID'";
        
        if(mysqli_query($connection, $update_sql)){
            $msg = "Fine paid successfully.";
        }
    }

    // Fetch fines for the current user (assuming senderID = borrower/renter who gets fined)
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
<main class="account-main account-main--profile">
    <header class="profile-page-header">
    <p class="profile-page-eyebrow">Account · Penalties</p>
    <h1 class="profile-page-title">My Fines</h1>
    <p class="profile-page-lead">Manage penalties from late returns or damaged campus rentals.</p>
    </header>

    <section class="profile-card profile-card--section">
    <div class="profile-card-head" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 class="profile-section-heading">Fine History</h3>
    </div>
    
    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; gap: 15px;">
        <?php if(mysqli_num_rows($fines_result) == 0): ?>
            <p style="color: #888;">You have no fines on your account. Great job!</p>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($fines_result)): ?>
                <article style="border: 1px solid #e8d3d3; padding: 16px; border-radius: 12px; background: <?php echo $row['isPaid'] ? '#fafafa' : '#fff'; ?>; display: flex; justify-content: space-between; align-items: center;">
                <div style="opacity: <?php echo $row['isPaid'] ? '0.6' : '1'; ?>;">
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px; color: #1a1a1a;">
                        Item: <?php echo htmlspecialchars($row['itemName']); ?>
                    </h4>
                    <p style="font-size: 13px; color: #555;">
                        <strong>Reason:</strong> <?php echo htmlspecialchars($row['reason']); ?><br>
                        <span style="color: #888; font-size: 12px;">Issued on: <?php echo date('M d, Y', strtotime($row['checkoutDate'])); ?></span>
                    </p>
                </div>
                
                <div style="text-align: right;">
                    <div style="font-size: 18px; font-weight: 800; color: #b3261e; margin-bottom: 8px;">
                        ₱<?php echo number_format($row['amount'], 2); ?>
                    </div>
                    <?php if($row['isPaid']): ?>
                        <span style="font-size: 12px; font-weight: 700; color: #1f7a34; background: #e6f4ea; padding: 4px 10px; border-radius: 12px;">Paid</span>
                    <?php else: ?>
                        <a href="fines.php?pay_fine=<?php echo $row['fineID']; ?>" style="background: #1a1a1a; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;" onclick="return confirm('Confirm payment for this fine?');">Pay Now</a>
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