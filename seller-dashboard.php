<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    if(strtolower($_SESSION['role']) == 'admin'){
        header("Location: admin.php");
        exit();
    }

    $action_msg = "";
    if(isset($_GET['action']) && isset($_GET['txn_id'])){
        $txn_id = intval($_GET['txn_id']);
        $action = $_GET['action'];
        $new_status = ($action == 'accept') ? 'approved' : 'rejected';
        
        $verify_sql = "SELECT transactionID FROM transactions WHERE transactionID = '$txn_id' AND receiverID = '" . $_SESSION['userID'] . "'";
        if(mysqli_num_rows(mysqli_query($connection, $verify_sql)) > 0){
            $update_sql = "UPDATE transactions SET status = '$new_status' WHERE transactionID = '$txn_id'";
            mysqli_query($connection, $update_sql);
            $action_msg = "Transaction successfully " . strtolower($new_status) . "!";
        }
    }

    $requests = getSellerRequests($connection, $_SESSION['userID']);
    $request_count = mysqli_num_rows($requests);
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; max-width: 1000px; margin: 0 auto; padding: 10px 20px 60px;">
    
    <header style="margin-bottom: 40px; text-align: center;">
        <p style="color: #8B2635; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Dashboard</p>
        <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a; margin: 0 0 12px 0;">Seller Dashboard</h1>
        <p style="color: #666; font-size: 16px; margin: 0;">Track buyer requests and monitor your active deals.</p>
    </header>

    <section style="background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">
        
        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Nunito', sans-serif; font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0;">Recent Buyer Requests</h3>
            <a href="profile.php" style="color: #8B2635; font-size: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Profile
            </a>
        </div>
        
        <?php if($action_msg != ""): ?>
            <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 600; font-size: 14px;">
                <?php echo $action_msg; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; gap: 16px;">
            <?php if($request_count == 0): ?>
                <div style="text-align: center; padding: 40px 20px; color: #888; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
                    <p style="margin: 0; font-size: 15px; font-weight: 500;">No buyer requests yet.</p>
                </div>
            <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($requests)): ?>
                    <article style="border: 1px solid #eaeaea; padding: 20px; border-radius: 12px; background: #fff; display: flex; justify-content: space-between; align-items: center; transition: 0.2s;" onmouseover="this.style.borderColor='#8B2635'" onmouseout="this.style.borderColor='#eaeaea'">
                        <div>
                            <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 6px; color: #1a1a1a; display: flex; align-items: center; gap: 10px;">
                                <?php echo htmlspecialchars($row['title']); ?> 
                                <span style="font-size: 11px; background: #f5f5f5; color: #666; padding: 4px 10px; border-radius: 20px; text-transform: capitalize; font-weight: 700; letter-spacing: 0.5px;">
                                    <?php echo $row['txnStatus']; ?>
                                </span>
                            </h4>
                            <p style="font-size: 13px; color: #555; margin: 0;">
                                Requested by: <strong style="color: #1a1a1a;"><?php echo $row['buyerName']; ?></strong> &middot; 
                                Type: <?php echo ucfirst($row['type']); ?> &middot; 
                                Amount: ₱<?php echo number_format($row['amount'], 2); ?>
                            </p>
                        </div>
                        
                        <?php if($row['txnStatus'] == 'pending'): ?>
                        <div style="display: flex; gap: 10px;">
                            <a href="seller-dashboard.php?action=accept&txn_id=<?php echo $row['transactionID']; ?>" style="background: #e6f4ea; color: #137333; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid transparent; transition: 0.2s;" onmouseover="this.style.borderColor='#137333'" onmouseout="this.style.borderColor='transparent'" onclick="showModal('Are you sure you want to accept this request?', this.href, event);">Accept</a>
                            <a href="seller-dashboard.php?action=reject&txn_id=<?php echo $row['transactionID']; ?>" style="background: #fce8e6; color: #c5221f; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid transparent; transition: 0.2s;" onmouseover="this.style.borderColor='#c5221f'" onmouseout="this.style.borderColor='transparent'" onclick="showModal('Are you sure you want to reject this request?', this.href, event);">Reject</a>
                        </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>