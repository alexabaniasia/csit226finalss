<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    // Block Admins from accessing marketplace user features
    if(strtolower($_SESSION['role']) == 'admin'){
        header("Location: admin.php");
        exit();
    }

    $buyerID = $_SESSION['userID'];
    $msg = "";
    $msg_type = "";

    // Handle Direct Checkout from view-item.php
    if(isset($_POST['direct_checkout']) && isset($_POST['listing_id'])){
        $listingID = intval($_POST['listing_id']);
        
        // Get listing and pricing details
        $query = "SELECT l.*, i.ownerID, s.price as salePrice, r.rentalPricePerDay, r.maxDays as rentMaxDays, r.deposit, r.fee, b.maxDays as borrowMaxDays 
                FROM listings l 
                JOIN items i ON l.itemID = i.itemID 
                LEFT JOIN sale_listings s ON l.listingID = s.listingID 
                LEFT JOIN rental_listings r ON l.listingID = r.listingID 
                LEFT JOIN borrow_listings b ON l.listingID = b.listingID 
                WHERE l.listingID = '$listingID'";
                
        $result = mysqli_query($connection, $query);
        $item = mysqli_fetch_assoc($result);

        if($item){
            if($item['ownerID'] == $buyerID){
                $msg = "You cannot purchase or rent your own item.";
                $msg_type = "error";
            } else {
                $sellerID = $item['ownerID'];
                $amount = 0;
                $txnType = ($item['listingType'] == 'sale') ? 'sale' : 'returnable';

                if($item['listingType'] == 'sale') $amount = $item['salePrice'];
                if($item['listingType'] == 'rental') $amount = $item['rentalPricePerDay'] + $item['deposit'] + $item['fee']; // Assuming 1 day upfront for simplicity

                // Start Transaction
                mysqli_begin_transaction($connection);
                try {
                    // 1. Insert into supertype `transactions` table
                    $stmt1 = $connection->prepare("INSERT INTO transactions (listingID, senderID, receiverID, transactionType, status, amount) VALUES (?, ?, ?, ?, 'pending', ?)");
                    $stmt1->bind_param("iiisd", $listingID, $buyerID, $sellerID, $txnType, $amount);
                    $stmt1->execute();
                    $transactionID = $connection->insert_id;

                    // 2. Insert into subtype
                    if($txnType == 'sale'){
                        $stmt2 = $connection->prepare("INSERT INTO sale_transactions (transactionID, finalPrice) VALUES (?, ?)");
                        $stmt2->bind_param("id", $transactionID, $amount);
                        $stmt2->execute();
                    } else {
                        $maxDays = ($item['listingType'] == 'rental') ? $item['rentMaxDays'] : $item['borrowMaxDays'];
                        $dueDate = date('Y-m-d', strtotime("+$maxDays days"));
                        $stmt2 = $connection->prepare("INSERT INTO returnable_transactions (transactionID, dueDate) VALUES (?, ?)");
                        $stmt2->bind_param("is", $transactionID, $dueDate);
                        $stmt2->execute();
                    }

                    mysqli_commit($connection);
                    $msg = "Request sent successfully! The seller has been notified.";
                    $msg_type = "success";

                } catch (Exception $e) {
                    mysqli_rollback($connection);
                    $msg = "Error processing your request: " . $e->getMessage();
                    $msg_type = "error";
                }
            }
        }
    }
?>

<div class="page">
<div class="bg-image"></div>
<main style="min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; z-index: 10;">
    
    <div style="background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 100%;">
        
        <?php if($msg_type == "success"): ?>
            <div style="width: 60px; height: 60px; background: #e6f4ea; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #137333;">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 style="font-family: 'Nunito', sans-serif; font-size: 24px; color: #1a1a1a; margin-bottom: 10px;">Success!</h2>
            <p style="color: #555; margin-bottom: 30px; line-height: 1.6;"><?php echo $msg; ?></p>
            <a href="transaction-history.php" style="display: inline-block; padding: 12px 24px; background: #8B2635; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 700;">View My Transactions</a>
        
        <?php else: ?>
            <div style="width: 60px; height: 60px; background: #fce8e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #c5221f;">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h2 style="font-family: 'Nunito', sans-serif; font-size: 24px; color: #1a1a1a; margin-bottom: 10px;">Oops</h2>
            <p style="color: #555; margin-bottom: 30px; line-height: 1.6;"><?php echo $msg ?: "No item was selected for checkout."; ?></p>
            <a href="browse.php" style="display: inline-block; padding: 12px 24px; background: #1a1a1a; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 700;">Back to Browse</a>
        <?php endif; ?>

    </div>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>