<?php
    require_once 'includes/header.php';
    include 'connect.php';

    // Security Check: Admins only
    if(!isset($_SESSION['userID']) || strtolower($_SESSION['role']) != 'admin'){
        header("Location: index.php");
        exit();
    }

    $msg = "";

    // Handle Report Actions via POST to capture the note
    if(isset($_POST['btnAction'])){
        $reportID = intval($_POST['report_id']);
        $action = $_POST['btnAction'];
        $note = mysqli_real_escape_string($connection, $_POST['moderatorNote']);
        $now = date('Y-m-d H:i:s');

        if($action == 'dismiss'){
            mysqli_query($connection, "UPDATE listing_reports SET status = 'dismissed', moderatorNote = '$note', resolvedAt = '$now' WHERE reportID = '$reportID'");
            $msg = "Report dismissed successfully.";
        }
        else if($action == 'delete_listing'){
            $listingID = intval($_POST['listing_id']);
            
            // Delete the listing (mark as deleted) and resolve report
            mysqli_query($connection, "UPDATE listings SET listingStatus = 'deleted' WHERE listingID = '$listingID'");
            mysqli_query($connection, "UPDATE listing_reports SET status = 'resolved', moderatorNote = '$note', resolvedAt = '$now' WHERE reportID = '$reportID'");
            $msg = "Offending listing has been removed and report resolved.";
        }
    }

    // Fetch Open Reports
    $reports_query = mysqli_query($connection, "
        SELECT r.*, l.listingStatus, i.name as itemName, u.firstName, u.lastName 
        FROM listing_reports r
        JOIN listings l ON r.listingID = l.listingID
        JOIN items i ON l.itemID = i.itemID
        JOIN users u ON r.reporterID = u.userID
        WHERE r.status = 'open'
        ORDER BY r.createdAt DESC
    ");
?>

<div class="page">
<div class="bg-image"></div>
<div style="max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    
    <div style="margin-bottom: 24px;">
    <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Reported Listings</h1>
    <p style="color: #555;">Review items flagged by the community for safety or fraud violations.</p>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.95); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px;">
    
    <?php if(mysqli_num_rows($reports_query) == 0): ?>
        <p style="color: #888;">No active reports to review. The community is safe!</p>
    <?php else: ?>
        <div style="display: grid; gap: 16px;">
            <?php while($report = mysqli_fetch_assoc($reports_query)): ?>
            <div style="border: 1px solid #f2c0c0; border-radius: 12px; padding: 16px; background: #fffaf9;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    
                    <div style="flex: 1;">
                        <h4 style="color: #b3261e; font-size: 16px; font-weight: 800; margin-bottom: 4px;">Reason: <?php echo htmlspecialchars($report['reason']); ?></h4>
                        <p style="font-size: 14px; color: #1a1a1a; margin-bottom: 8px;"><strong>Item:</strong> <?php echo htmlspecialchars($report['itemName']); ?></p>
                        <p style="font-size: 13px; color: #555; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                            "<?php echo nl2br(htmlspecialchars($report['details'])); ?>"
                        </p>
                        <p style="font-size: 12px; color: #888; margin-top: 8px;">Reported by: <?php echo htmlspecialchars($report['firstName'] . ' ' . $report['lastName']); ?> on <?php echo date('M d, Y', strtotime($report['createdAt'])); ?></p>
                    </div>
                    
                    <form method="post" action="admin-reports.php" style="display: flex; flex-direction: column; gap: 8px; min-width: 280px; background: #fff; padding: 12px; border-radius: 8px; border: 1px solid #e0dada;">
                        <input type="hidden" name="report_id" value="<?php echo $report['reportID']; ?>">
                        <input type="hidden" name="listing_id" value="<?php echo $report['listingID']; ?>">
                        
                        <label style="font-size: 12px; font-weight: 700; color: #1a1a1a;">Moderator Note:</label>
                        <textarea name="moderatorNote" placeholder="Explain your action (optional)..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 12px; resize: none; font-family: inherit; height: 60px;"></textarea>
                        
                        <div style="display: flex; gap: 6px; margin-top: 4px;">
                            <a href="view-item.php?id=<?php echo $report['listingID']; ?>" target="_blank" style="flex: 1; text-align: center; background: #1a1a1a; color: #fff; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">View Item</a>
                            <button type="submit" name="btnAction" value="delete_listing" style="flex: 1; background: #fce8e6; color: #c5221f; border: 1px solid #f2c0c0; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;" onclick="return confirm('Delete this listing permanently?');">Delete Listing</button>
                            <button type="submit" name="btnAction" value="dismiss" style="flex: 1; background: #fff; color: #666; border: 1px solid #ccc; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Dismiss</button>
                        </div>
                    </form>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>