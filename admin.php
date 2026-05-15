<?php 
    session_start();

    if(!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin'){
        echo "<script>alert('Access Denied. Admins only.'); window.location.href='login.php';</script>";
        exit;
    }

    include 'connect.php'; 
    require_once 'includes/header.php'; 

    $view = isset($_GET['view']) ? $_GET['view'] : 'listings';
    $admin_id = $_SESSION['userID'];

    if(isset($_POST['action']) && isset($_POST['submission_id'])){
        $sub_id = $_POST['submission_id'];
        
        if($_POST['action'] == 'approve'){
            $sub_query = "SELECT * FROM listing_submissions WHERE submissionID = " . $sub_id;
            $sub_result = mysqli_query($connection, $sub_query);
            $sub = mysqli_fetch_assoc($sub_result);
            
            $item_sql = "INSERT INTO items (ownerID, name, description, category, itemCondition) 
                         VALUES (".$sub['submitterID'].", '".$sub['title']."', '".$sub['description']."', '".$sub['category']."', '".$sub['itemCondition']."')";
            mysqli_query($connection, $item_sql);
            $new_item_id = mysqli_insert_id($connection);
            
            $avail_from = $sub['availabilityFrom'] ? "'".$sub['availabilityFrom']."'" : 'NULL';
            $avail_to = $sub['availabilityTo'] ? "'".$sub['availabilityTo']."'" : 'NULL';
            
            $listing_sql = "INSERT INTO listings (itemID, listingType, location, availabilityFrom, availabilityTo) 
                            VALUES (".$new_item_id.", '".$sub['listingType']."', '".$sub['location']."', ".$avail_from.", ".$avail_to.")";
            mysqli_query($connection, $listing_sql);
            $new_listing_id = mysqli_insert_id($connection);
            
            if($sub['listingType'] == 'sale'){
                mysqli_query($connection, "INSERT INTO sale_listings (listingID, price) VALUES (".$new_listing_id.", ".$sub['price'].")");
            } else if($sub['listingType'] == 'rent'){
                mysqli_query($connection, "INSERT INTO rental_listings (listingID, fee, deposit, rentalPricePerDay, maxDays) 
                                           VALUES (".$new_listing_id.", ".$sub['rentalFee'].", ".$sub['depositAmount'].", ".$sub['rentalPricePerDay'].", ".$sub['maxDays'].")");
            } else if($sub['listingType'] == 'borrow'){
                mysqli_query($connection, "INSERT INTO borrow_listings (listingID, maxDays) VALUES (".$new_listing_id.", ".$sub['maxDays'].")");
            }
            
            mysqli_query($connection, "UPDATE listing_submissions SET status='approved', approvedListingID=".$new_listing_id.", reviewerID=".$admin_id.", reviewedAt=NOW() WHERE submissionID=".$sub_id);
            echo "<script>alert('Listing approved and published!'); window.location.href='admin.php?view=listings';</script>";
            
        } else if($_POST['action'] == 'reject'){
            mysqli_query($connection, "UPDATE listing_submissions SET status='rejected', reviewerID=".$admin_id.", reviewedAt=NOW() WHERE submissionID=".$sub_id);
            echo "<script>alert('Listing rejected.'); window.location.href='admin.php?view=listings';</script>";
        }
    }

    if(isset($_POST['action']) && isset($_POST['target_user_id'])){
        if($_POST['action'] == 'delete_user'){
            $target_id = (int)$_POST['target_user_id'];
            mysqli_query($connection, "UPDATE users SET status = 'inactive' WHERE userID = $target_id");
            mysqli_query($connection, "UPDATE listings l, items i 
                                       SET l.listingStatus = 'closed' 
                                       WHERE l.itemID = i.itemID 
                                       AND i.ownerID = $target_id");
                                       
            echo "<script>alert('User account deactivated and items hidden.'); window.location.href='admin.php?view=users';</script>";
        }
    }
?>

<main style="max-width: 1200px; margin: 40px auto; padding: 20px;">
  <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
    <div>
      <h1 style="color: #8B2635; margin: 0 0 5px 0; font-size: 32px;">System Administration</h1>
      <p style="color: #666; margin: 0; font-size: 16px;">Manage campus marketplace data and user accounts.</p>
    </div>
    
    <div style="display: flex; gap: 10px; background: #fff; padding: 6px; border-radius: 8px; border: 1px solid #ddd;">
        <a href="admin.php?view=listings" style="padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 14px; color: <?php echo $view == 'listings' ? 'white' : '#555'; ?>; background: <?php echo $view == 'listings' ? '#8B2635' : 'transparent'; ?>;">Manage Listings</a>
        <a href="admin.php?view=users" style="padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 14px; color: <?php echo $view == 'users' ? 'white' : '#555'; ?>; background: <?php echo $view == 'users' ? '#8B2635' : 'transparent'; ?>;">Manage Users</a>
    </div>
  </div>

  <div class="feature-card" style="padding: 30px;">
    
    <?php if($view == 'listings'): ?>
        <h2 style="font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Pending Item Submissions</h2>
        <?php 
            $pending_query = "SELECT ls.*, u.firstName, u.lastName FROM listing_submissions ls, users u WHERE ls.submitterID = u.userID AND ls.status = 'pending' ORDER BY ls.createdAt ASC";
            $pending_result = mysqli_query($connection, $pending_query);
            if(mysqli_num_rows($pending_result) == 0): 
        ?>
            <div style="padding: 40px; text-align: center; color: #666; font-size: 16px;">No pending submissions to review.</div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #eaeaea;">
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Submitter</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Item Details</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Pricing & Type</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($pending_result)): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 20px 10px; vertical-align: top;">
                            <strong style="color: #222; font-size: 16px;"><?php echo $row['firstName'] . " " . $row['lastName']; ?></strong><br>
                            <span style="color: #888; font-size: 13px;">ID: <?php echo $row['submitterID']; ?></span>
                        </td>
                        <td style="padding: 20px 10px; vertical-align: top; max-width: 300px;">
                            <strong style="color: #8B2635; font-size: 16px;"><?php echo $row['title']; ?></strong><br>
                            <span style="color: #555; font-size: 14px;"><strong><?php echo ucfirst($row['category']); ?></strong> • <?php echo ucfirst($row['itemCondition']); ?></span><br>
                            <span style="color: #666; font-size: 13px; display: block; margin-top: 8px;"><?php echo $row['description']; ?></span>
                        </td>
                        <td style="padding: 20px 10px; vertical-align: top;">
                            <span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; display: inline-block;"><?php echo $row['listingType']; ?></span><br>
                            <?php if($row['listingType'] == 'sale'): ?>
                                <strong style="font-size: 18px;">₱<?php echo $row['price']; ?></strong>
                            <?php elseif($row['listingType'] == 'rent'): ?>
                                <strong style="font-size: 18px;">₱<?php echo $row['rentalPricePerDay']; ?>/day</strong>
                            <?php else: ?>
                                <strong style="font-size: 18px; color: #2e7d32;">Free</strong>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 20px 10px; vertical-align: top; display: flex; gap: 10px;">
                            <form method="post">
                                <input type="hidden" name="submission_id" value="<?php echo $row['submissionID']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" style="padding: 8px 16px; border-radius: 6px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c3e6cb; font-weight: bold; cursor: pointer;">Approve</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="submission_id" value="<?php echo $row['submissionID']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" style="padding: 8px 16px; border-radius: 6px; background: #ffebee; color: #d32f2f; border: 1px solid #ffcdd2; font-weight: bold; cursor: pointer;">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif($view == 'users'): ?>
        <h2 style="font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Registered Users</h2>
        <?php 
            // We hide other admins to prevent accidental locking out of the system
            $users_query = "SELECT * FROM users WHERE role != 'admin' ORDER BY userID DESC";
            $users_result = mysqli_query($connection, $users_query);
            if(mysqli_num_rows($users_result) == 0): 
        ?>
            <div style="padding: 40px; text-align: center; color: #666; font-size: 16px;">No registered student accounts found.</div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #eaeaea;">
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">ID</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Name</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Email & Contact</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Role / Status</th>
                        <th style="text-align: left; padding: 15px 10px; color: #333; font-weight: 800;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 20px 10px; color: #888; font-weight: bold;"><?php echo $user['userID']; ?></td>
                        <td style="padding: 20px 10px;">
                            <strong style="color: #222; font-size: 15px;"><?php echo $user['firstName'] . " " . $user['lastName']; ?></strong>
                        </td>
                        <td style="padding: 20px 10px;">
                            <span style="color: #444; font-size: 14px; display: block;"><?php echo $user['email']; ?></span>
                            <span style="color: #888; font-size: 13px;"><?php echo $user['contactNumber']; ?></span>
                        </td>
                        <td style="padding: 20px 10px;">
                            <span style="background: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;"><?php echo $user['role']; ?></span>
                        </td>
                        <td style="padding: 20px 10px;">
                            <form method="post" onsubmit="return confirm('WARNING: This will permanently delete this user and all their listings. Continue?');">
                                <input type="hidden" name="target_user_id" value="<?php echo $user['userID']; ?>">
                                <input type="hidden" name="action" value="delete_user">
                                <button type="submit" style="padding: 8px 12px; border-radius: 6px; background: #fff5f5; color: #b3261e; border: 1px solid #f5d0d0; font-weight: bold; cursor: pointer; font-size: 13px; transition: background 0.2s;">Delete User</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

  </div>
</main>

<?php require_once 'includes/footer.php'; ?>