<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    if(!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $listingID = intval($_GET['id']);
    $msg = "";
    
    $query = mysqli_query($connection, "SELECT i.name FROM listings l JOIN items i ON l.itemID = i.itemID WHERE l.listingID = '$listingID'");
    $item = mysqli_fetch_assoc($query);

    if(isset($_POST['btnReport'])){
        $reporterID = $_SESSION['userID'];
        $reason = mysqli_real_escape_string($connection, $_POST['reason']);
        $details = mysqli_real_escape_string($connection, $_POST['details']);
        
        $sql = "INSERT INTO listing_reports (listingID, reporterID, reason, details, status) VALUES ('$listingID', '$reporterID', '$reason', '$details', 'open')";
        
        if(mysqli_query($connection, $sql)){
            $msg = "Report submitted successfully. Our moderators will review this shortly.";
        } else {
            $msg = "Error submitting report.";
        }
    }
?>

<main style="position: relative; z-index: 9999; padding: 60px 20px; display: flex; justify-content: center;">
<div style="width: 100%; max-width: 500px; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
    
    <h2 style="font-family: 'Nunito', sans-serif; font-size: 24px; margin-bottom: 8px; color: #1a1a1a;">Report Listing</h2>
    <p style="color: #666; margin-bottom: 24px; font-size: 14px;">You are reporting: <strong><?php echo htmlspecialchars($item['name'] ?? 'Unknown Item'); ?></strong></p>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Reason for Report</label>
            <select name="reason" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
                <option value="" disabled selected>Select a reason...</option>
                <option value="Fraudulent/Scam">Fraudulent or Scam</option>
                <option value="Inappropriate Content">Inappropriate Content</option>
                <option value="Damaged/Not as Described">Item heavily damaged / Not as described</option>
                <option value="Prohibited Item">Prohibited Campus Item</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div>
            <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Additional Details</label>
            <textarea name="details" required rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit; resize: none;" placeholder="Please provide specific details about why you are reporting this listing..."></textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <a href="view-item.php?id=<?php echo $listingID; ?>" style="flex: 1; padding: 14px; text-align: center; border-radius: 8px; border: 1px solid #ccc; color: #555; text-decoration: none; font-weight: 600;">Cancel</a>
            <button type="submit" name="btnReport" style="flex: 1; padding: 14px; border-radius: 8px; border: none; background: #8B2635; color: white; font-weight: bold; cursor: pointer;">Submit Report</button>
        </div>
    </form>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>