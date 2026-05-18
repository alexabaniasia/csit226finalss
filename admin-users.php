<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID']) || strtolower($_SESSION['role']) != 'admin'){
        header("Location: index.php");
        exit();
    }

    $msg = "";

    if(isset($_GET['action']) && isset($_GET['id'])){
        $targetID = intval($_GET['id']);
        $action = $_GET['action'];
        
        if($targetID != $_SESSION['userID']) {
            $new_status = ($action == 'ban') ? 'inactive' : 'active';
            $update_sql = "UPDATE users SET status = '$new_status' WHERE userID = '$targetID'";
            if(mysqli_query($connection, $update_sql)){
                $msg = "User account status updated to: " . ucfirst($new_status);
            }
        } else {
            $msg = "You cannot change your own admin account status.";
        }
    }

    $users_query = mysqli_query($connection, "SELECT * FROM users ORDER BY userID DESC");
?>

<div class="page">
<div class="bg-image"></div>
<div style="position: relative; z-index: 10; max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    
    <div style="margin-bottom: 24px;">
    <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Manage Users</h1>
    <p style="color: #555;">Monitor campus accounts and enforce platform access.</p>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.98); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    
    <?php if(mysqli_num_rows($users_query) == 0): ?>
        <div style="text-align: center; padding: 60px 20px; color: #666; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
            <svg width="48" height="48" fill="none" stroke="#aaa" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            <h3 style="font-size: 18px; color: #1a1a1a; margin-bottom: 8px;">No Users Found</h3>
            <p style="font-size: 14px;">There are currently no users registered in the system.</p>
        </div>
    <?php else: ?>
        <table id="adminTable" class="display" style="width:100%; border-collapse: collapse; text-align: left;">
            <thead>
            <tr style="border-bottom: 2px solid #8B2635;">
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Email</th>
                <th style="padding: 12px;">Role</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 16px 12px; font-weight: 600; color: #1a1a1a;"><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></td>
                <td style="padding: 16px 12px; color: #555;"><?php echo htmlspecialchars($user['email']); ?></td>
                <td style="padding: 16px 12px; font-weight: 600;"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                <td style="padding: 16px 12px;">
                    <?php if($user['status'] == 'active'): ?>
                        <span style="background: #e6f4ea; color: #137333; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">Active</span>
                    <?php else: ?>
                        <span style="background: #fce8e6; color: #c5221f; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td style="padding: 16px 12px;">
                    <?php if($user['userID'] != $_SESSION['userID']): ?>
                        <?php if($user['status'] == 'active'): ?>
                            <a href="#" style="background: #fff5f5; border: 1px solid #f2c0c0; color: #c5221f; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s;" onclick="showModal('Are you sure you want to suspend this user? They will not be able to log in.', 'admin-users.php?action=ban&id=<?php echo $user['userID']; ?>', event);">Suspend</a>
                        <?php else: ?>
                            <a href="admin-users.php?action=activate&id=<?php echo $user['userID']; ?>" style="background: #e6f4ea; border: 1px solid #b6ddc3; color: #137333; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s;">Reactivate</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: #aaa; font-size: 13px; font-weight: 600;">(You)</span>
                    <?php endif; ?>
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