<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    // Block Admins from accessing marketplace user features
    if(strtolower($_SESSION['role']) == 'admin'){
        header("Location: admin.php");
        exit();
    }

    // Process Accept/Reject actions securely via PHP GET parameters
    $action_msg = "";
    if(isset($_GET['action']) && isset($_GET['txn_id'])){
        $txn_id = intval($_GET['txn_id']);
        $action = $_GET['action'];
        $new_status = ($action == 'accept') ? 'Accepted' : 'Rejected';
        
        // Ensure this transaction belongs to the logged-in seller
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
<main class="account-main account-main--profile">
    <header class="profile-page-header">
    <p class="profile-page-eyebrow">Dashboard</p>
    <h1 class="profile-page-title">Seller Dashboard</h1>
    <p class="profile-page-lead">Track buyer requests and monitor your active deals.</p>
    </header>

    <section class="profile-card profile-card--section">
    <div class="profile-card-head" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 class="profile-section-heading">Recent Buyer Requests</h3>
    </div>
    
    <?php if($action_msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $action_msg; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; gap: 15px;">
        <?php if($request_count == 0): ?>
            <p style="color: #888;">No buyer requests yet.</p>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($requests)): ?>
                <article style="border: 1px solid #e8d3d3; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">
                        <?php echo htmlspecialchars($row['title']); ?> 
                        <span style="font-size: 12px; background: #f0f0f0; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">
                            <?php echo $row['txnStatus']; ?>
                        </span>
                    </h4>
                    <p style="font-size: 13px; color: #666;">
                        Requested by: <strong style="color: #1a1a1a;"><?php echo $row['buyerName']; ?></strong> · 
                        Type: <?php echo ucfirst($row['type']); ?> · 
                        Amount: ₱<?php echo number_format($row['amount'], 2); ?>
                    </p>
                </div>
                
                <?php if($row['txnStatus'] == 'Pending'): ?>
                <div style="display: flex; gap: 10px;">
                    <a href="seller-dashboard.php?action=accept&txn_id=<?php echo $row['transactionID']; ?>" style="background: #e6f4ea; color: #137333; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none;">Accept</a>
                    <a href="seller-dashboard.php?action=reject&txn_id=<?php echo $row['transactionID']; ?>" style="background: #fce8e6; color: #c5221f; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none;">Reject</a>
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