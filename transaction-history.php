<?php
    require_once 'includes/header.php';
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
<main class="account-main account-main--profile">
    <header class="profile-page-header">
    <p class="profile-page-eyebrow">Account · History</p>
    <h1 class="profile-page-title">Transaction History</h1>
    <p class="profile-page-lead">Transactions where you were a buyer or seller.</p>
    </header>

    <section class="profile-card profile-card--section">
    <div class="profile-card-head" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h3 class="profile-section-heading">Recent transactions</h3>
        <a href="profile.php" style="color: #8B2635; font-size: 14px; font-weight: 600;">← Back to profile</a>
    </div>

    <div style="display: grid; gap: 15px;">
        <?php if(mysqli_num_rows($history) == 0): ?>
            <p style="color: #888;">You have no transaction history yet.</p>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($history)): 
                // Determine if the current user was the buyer or the seller
                $isBuyer = ($row['buyerName'] == $_SESSION['firstName']);
                $roleText = $isBuyer ? "Bought from " . $row['sellerName'] : "Sold to " . $row['buyerName'];
                
                // Color coding for status
                $statusColor = "#666";
                if($row['txnStatus'] == 'Accepted' || $row['txnStatus'] == 'Completed') $statusColor = "#1f7a34";
                if($row['txnStatus'] == 'Rejected') $statusColor = "#b3261e";
            ?>
                <article style="border: 1px solid #e8d3d3; padding: 16px; border-radius: 12px; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px; color: #1a1a1a;">
                        <?php echo htmlspecialchars($row['title']); ?>
                    </h4>
                    <p style="font-size: 13px; color: #555;">
                        <span style="font-weight: 600;"><?php echo $roleText; ?></span> · 
                        Type: <?php echo ucfirst($row['type']); ?> · 
                        Date: <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                    </p>
                </div>
                
                <div style="text-align: right;">
                    <div style="font-size: 16px; font-weight: 800; color: #1a1a1a;">
                        ₱<?php echo number_format($row['amount'], 2); ?>
                    </div>
                    <div style="font-size: 12px; font-weight: 700; color: <?php echo $statusColor; ?>; margin-top: 4px; background: #f5f5f5; display: inline-block; padding: 2px 8px; border-radius: 10px;">
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