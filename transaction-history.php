<?php
    require_once 'includes/header.php';
    include 'connect.php';
    include 'readrecords.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    $userID = $_SESSION['userID'];
    $history = getUserTransactionHistory($connection, $userID);
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; max-width: 1000px; margin: 0 auto; padding: 60px 20px;">
    
    <header style="margin-bottom: 40px; text-align: center;">
        <p style="color: #8B2635; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Account &middot; History</p>
        <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0;">Transaction History</h1>
        <p style="color: #666; font-size: 16px; margin: 0;">Monitor the items you’ve bought, rented, borrowed, or sold.</p>
    </header>

    <section style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">
        
        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Nunito', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0;">Recent Transactions</h3>
            <a href="profile.php" style="color: #8B2635; font-size: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Profile
            </a>
        </div>

        <div style="display: grid; gap: 16px;">
            <?php if(mysqli_num_rows($history) == 0): ?>
                <div style="text-align: center; padding: 40px 20px; color: #888; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
                    <p style="margin: 0; font-size: 15px; font-weight: 500;">You have no transaction history yet.</p>
                </div>
            <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($history)): 
                    $isBuyer = ($row['senderID'] == $_SESSION['userID']);
                    $roleText = $isBuyer ? "Bought from " . $row['sellerName'] : "Sold to " . $row['buyerName'];
                    
                    $statusColor = "#666";
                    $statusBg = "#f5f5f5";
                    
                    if($row['txnStatus'] == 'approved' || $row['txnStatus'] == 'completed') {
                        $statusColor = "#1f7a34";
                        $statusBg = "#e6f4ea";
                    }
                    if($row['txnStatus'] == 'rejected') {
                        $statusColor = "#b3261e";
                        $statusBg = "#fce8e6";
                    }
                ?>
                    <article style="border: 1px solid #eaeaea; padding: 20px; border-radius: 12px; background: #fff; display: flex; justify-content: space-between; align-items: center; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#eaeaea'">
                        <div>
                            <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 6px; color: #1a1a1a;">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h4>
                            <p style="font-size: 13px; color: #555; margin: 0;">
                                <span style="font-weight: 700; color: #444;"><?php echo $roleText; ?></span> &middot; 
                                Type: <?php echo ucfirst($row['type']); ?> &middot; 
                                Date: <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </p>
                        </div>
                        
                        <div style="text-align: right;">
                            <div style="font-size: 18px; font-weight: 800; color: #1a1a1a;">
                                ₱<?php echo number_format($row['amount'], 2); ?>
                            </div>
                            <div style="font-size: 12px; font-weight: 700; color: <?php echo $statusColor; ?>; margin-top: 6px; background: <?php echo $statusBg; ?>; display: inline-block; padding: 4px 12px; border-radius: 20px; text-transform: capitalize;">
                                <?php echo $row['txnStatus']; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>