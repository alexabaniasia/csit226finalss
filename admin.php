<?php
    require_once 'includes/header.php';
    include 'readrecords.php';

    // Security Check: Only allow Admins
    if(!isset($_SESSION['userID']) || $_SESSION['role'] != 'admin'){
        header("Location: index.php");
        exit();
    }

    $msg = "";
    
    // Process Approve/Reject actions
    if(isset($_GET['action']) && isset($_GET['id'])){
        $submission_id = intval($_GET['id']);
        $action = $_GET['action'];
        $admin_id = $_SESSION['userID'];
        $now = date('Y-m-d H:i:s');
        
        if($action == 'reject'){
            $update_sql = "UPDATE listing_submissions SET status = 'rejected', reviewerID = '$admin_id', reviewedAt = '$now' WHERE submissionID = '$submission_id'";
            if(mysqli_query($connection, $update_sql)){
                $msg = "Submission has been rejected.";
            }
        } 
        else if($action == 'approve'){
            // 1. Fetch the submission details
            $sub_query = mysqli_query($connection, "SELECT * FROM listing_submissions WHERE submissionID = '$submission_id'");
            $sub = mysqli_fetch_assoc($sub_query);
            
            if($sub && $sub['status'] == 'pending'){
                // Start a transaction to ensure all queries succeed or fail together
                mysqli_begin_transaction($connection);
                
                try {
                    // 2. Insert into `items` table
                    $stmt_item = $connection->prepare("INSERT INTO items (ownerID, name, description, category, itemCondition, source) VALUES (?, ?, ?, ?, ?, 'student')");
                    $stmt_item->bind_param("issss", $sub['submitterID'], $sub['title'], $sub['description'], $sub['category'], $sub['itemCondition']);
                    $stmt_item->execute();
                    $new_item_id = $connection->insert_id;
                    
                    // 3. Insert into `listings` table (Supertype)
                    $stmt_list = $connection->prepare("INSERT INTO listings (itemID, listingType, listingStatus, location, availabilityFrom, availabilityTo) VALUES (?, ?, 'active', ?, ?, ?)");
                    $stmt_list->bind_param("issss", $new_item_id, $sub['listingType'], $sub['location'], $sub['availabilityFrom'], $sub['availabilityTo']);
                    $stmt_list->execute();
                    $new_listing_id = $connection->insert_id;
                    
                    // 4. Insert into the correct subtype table
                    if($sub['listingType'] == 'sale'){
                        $stmt_sub = $connection->prepare("INSERT INTO sale_listings (listingID, price) VALUES (?, ?)");
                        $stmt_sub->bind_param("id", $new_listing_id, $sub['price']);
                        $stmt_sub->execute();
                    } 
                    else if($sub['listingType'] == 'rental'){
                        $fee = $sub['rentalFee'] ?? 0;
                        $dep = $sub['depositAmount'] ?? 0;
                        $stmt_sub = $connection->prepare("INSERT INTO rental_listings (listingID, fee, deposit, rentalPricePerDay, maxDays) VALUES (?, ?, ?, ?, ?)");
                        $stmt_sub->bind_param("idddi", $new_listing_id, $fee, $dep, $sub['rentalPricePerDay'], $sub['maxDays']);
                        $stmt_sub->execute();
                    } 
                    else if($sub['listingType'] == 'borrow'){
                        $stmt_sub = $connection->prepare("INSERT INTO borrow_listings (listingID, maxDays) VALUES (?, ?)");
                        $stmt_sub->bind_param("ii", $new_listing_id, $sub['maxDays']);
                        $stmt_sub->execute();
                    }
                    
                    // 5. Update the submission record
                    $stmt_update = $connection->prepare("UPDATE listing_submissions SET status = 'approved', reviewerID = ?, reviewedAt = ?, approvedListingID = ? WHERE submissionID = ?");
                    $stmt_update->bind_param("isii", $admin_id, $now, $new_listing_id, $submission_id);
                    $stmt_update->execute();
                    
                    mysqli_commit($connection);
                    $msg = "Listing successfully approved and published!";
                    
                } catch (Exception $e) {
                    mysqli_rollback($connection);
                    $msg = "Error approving listing: " . $e->getMessage();
                }
            }
        }
    }

    $pendingListings = getPendingListings($connection);
    $pendingCount = mysqli_num_rows($pendingListings);
?>

<div class="page">
<div class="bg-image"></div>

<div style="max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom: 20px;">
    <div>
        <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Moderate Submissions</h1>
        <p style="opacity:.85; margin:8px 0 0; color: #555;">Review submissions to generate active campus listings.</p>
    </div>
    <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 14px; border-radius:999px; background:#f3f4f6; border:1px solid rgba(0,0,0,.06); font-size: 14px;">
        <span>Pending</span>
        <strong><?php echo $pendingCount; ?></strong>
    </div>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.92); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px;">
    
    <?php if($pendingCount == 0): ?>
        <div style="padding: 14px 10px; color: #888;">No pending submissions right now.</div>
    <?php else: ?>
        <table id="adminTable" class="table table-striped table-bordered" style="width:100%; margin-top: 10px;">
            <thead style="background: #8B2635; color: white;">
            <tr>
                <th>Item</th>
                <th>Submitter</th>
                <th>Details</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($pendingListings)): ?>
                <tr>
                <td>
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                    <span style="font-size: 12px; color: #666;"><?php echo ucfirst($row['listingType']); ?></span>
                </td>
                <td><?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?></td>
                <td>
                    <span style="font-size: 13px;">
                        Cond: <?php echo ucfirst($row['itemCondition']); ?><br>
                        <?php if($row['listingType'] == 'sale'): ?>
                            Price: ₱<?php echo number_format($row['price'], 2); ?>
                        <?php elseif($row['listingType'] == 'rental'): ?>
                            Rate: ₱<?php echo number_format($row['rentalPricePerDay'], 2); ?>/day
                        <?php else: ?>
                            Max Days: <?php echo $row['maxDays']; ?>
                        <?php endif; ?>
                    </span>
                </td>
                <td>
                    <div style="display:flex; gap:8px;">
                    <a href="admin.php?action=approve&id=<?php echo $row['submissionID']; ?>" style="background:#f2fbf5; border:1px solid #b6ddc3; color:#1f7a34; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none;">Approve</a>
                    <a href="admin.php?action=reject&id=<?php echo $row['submissionID']; ?>" style="background:#fff5f5; border:1px solid #f2c0c0; color:#9b1c1c; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none;" onclick="return confirm('Reject this listing?');">Reject</a>
                    </div>
                </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    </div>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>