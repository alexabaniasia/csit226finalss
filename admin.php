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
                mysqli_begin_transaction($connection);
                try {
                    $stmt_item = $connection->prepare("INSERT INTO items (ownerID, name, description, category, itemCondition, source) VALUES (?, ?, ?, ?, ?, 'student')");
                    $stmt_item->bind_param("issss", $sub['submitterID'], $sub['title'], $sub['description'], $sub['category'], $sub['itemCondition']);
                    $stmt_item->execute();
                    $new_item_id = $connection->insert_id;
                    
                    $stmt_list = $connection->prepare("INSERT INTO listings (itemID, listingType, listingStatus, location, availabilityFrom, availabilityTo) VALUES (?, ?, 'active', ?, ?, ?)");
                    $stmt_list->bind_param("issss", $new_item_id, $sub['listingType'], $sub['location'], $sub['availabilityFrom'], $sub['availabilityTo']);
                    $stmt_list->execute();
                    $new_listing_id = $connection->insert_id;
                    
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

<div style="position: relative; z-index: 10; max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    
    <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom: 20px;">
        <div>
            <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Moderate Submissions</h1>
            <p style="opacity:.85; margin:8px 0 0; color: #555;">Review submissions to generate active campus listings.</p>
        </div>
        <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 14px; border-radius:999px; background:#fff; border:1px solid rgba(0,0,0,.1); font-size: 14px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <span>Pending</span>
            <strong style="color: #8B2635;"><?php echo $pendingCount; ?></strong>
        </div>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.98); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    
    <?php if($pendingCount == 0): ?>
        <div style="text-align: center; padding: 60px 20px; color: #666; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
            <svg width="48" height="48" fill="none" stroke="#aaa" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" /></svg>
            <h3 style="font-size: 18px; color: #1a1a1a; margin-bottom: 8px;">No Pending Submissions</h3>
            <p style="font-size: 14px;">All student listings have been reviewed and processed.</p>
        </div>
    <?php else: ?>
        <table id="adminTable" class="display" style="width:100%; margin-top: 10px; text-align: left;">
            <thead>
            <tr style="border-bottom: 2px solid #8B2635;">
                <th style="padding: 12px;">Item</th>
                <th style="padding: 12px;">Submitter</th>
                <th style="padding: 12px;">Details</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($pendingListings)): ?>
                <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 16px 12px;">
                    <strong style="color: #1a1a1a; font-size: 15px;"><?php echo htmlspecialchars($row['title']); ?></strong><br>
                    <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;"><?php echo htmlspecialchars($row['listingType']); ?></span>
                </td>
                <td style="padding: 16px 12px; color: #444;"><?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?></td>
                <td style="padding: 16px 12px;">
                    <span style="font-size: 13px; color: #555;">
                        <strong style="color: #222;">Cond:</strong> <?php echo ucfirst(htmlspecialchars($row['itemCondition'])); ?><br>
                        <?php if($row['listingType'] == 'sale'): ?>
                            <strong style="color: #222;">Price:</strong> ₱<?php echo number_format($row['price'], 2); ?>
                        <?php elseif($row['listingType'] == 'rental'): ?>
                            <strong style="color: #222;">Rate:</strong> ₱<?php echo number_format($row['rentalPricePerDay'], 2); ?>/day
                        <?php else: ?>
                            <strong style="color: #222;">Max Days:</strong> <?php echo $row['maxDays']; ?>
                        <?php endif; ?>
                    </span>
                </td>
                <td style="padding: 16px 12px;">
                    <div style="display:flex; gap:8px;">
                    <a href="admin.php?action=approve&id=<?php echo $row['submissionID']; ?>" style="background:#e6f4ea; color:#137333; padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 13px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#ceead6'" onmouseout="this.style.background='#e6f4ea'">Approve</a>
                    <a href="admin.php?action=reject&id=<?php echo $row['submissionID']; ?>" style="background:#fce8e6; color:#c5221f; padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 13px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#fad2cf'" onmouseout="this.style.background='#fce8e6'" onclick="return confirm('Reject this listing?');">Reject</a>
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