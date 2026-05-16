<?php
    require_once 'includes/header.php';
    include 'connect.php';

    // Security Check: Admins only
    if(!isset($_SESSION['userID']) || strtolower($_SESSION['role']) != 'admin'){
        header("Location: index.php");
        exit();
    }

    $msg = "";

    // Handle user activation/deactivation
    if(isset($_GET['action']) && isset($_GET['id'])){
        $targetID = intval($_GET['id']);
        $action = $_GET['action'];
        
        // Prevent admin from banning themselves
        if($targetID != $_SESSION['userID']) {
            $new_status = ($action == 'ban') ? 'inactive' : 'active';
            $update_sql = "UPDATE users SET status = '$new_status' WHERE userID = '$targetID'";
            if(mysqli_query($connection, $update_sql)){
                $msg = "User account status updated to: " . $new_status;
            }
        } else {
            $msg = "You cannot change your own admin account status.";
        }
    }

    $users_query = mysqli_query($connection, "SELECT * FROM users ORDER BY userID DESC");
?>

<div class="page">
<div class="bg-image"></div>
<div style="max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    
    <div style="margin-bottom: 24px;">
    <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Manage Users</h1>
    <p style="color: #555;">Monitor campus accounts and enforce platform access.</p>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.95); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px;">
    <table style="width:100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
        <tr>
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
            <td style="padding: 12px; font-weight: 600;"><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></td>
            <td style="padding: 12px; color: #555;"><?php echo htmlspecialchars($user['email']); ?></td>
            <td style="padding: 12px;"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
            <td style="padding: 12px;">
                <?php if($user['status'] == 'active'): ?>
                    <span style="background: #e6f4ea; color: #137333; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">Active</span>
                <?php else: ?>
                    <span style="background: #fce8e6; color: #c5221f; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">Inactive (Banned)</span>
                <?php endif; ?>
            </td>
            <td style="padding: 12px;">
                <?php if($user['userID'] != $_SESSION['userID']): ?>
                    <?php if($user['status'] == 'active'): ?>
                        <a href="admin-users.php?action=ban&id=<?php echo $user['userID']; ?>" style="color: #c5221f; font-size: 13px; font-weight: 700; text-decoration: none;" onclick="return confirm('Suspend this user?');">Suspend</a>
                    <?php else: ?>
                        <a href="admin-users.php?action=activate&id=<?php echo $user['userID']; ?>" style="color: #137333; font-size: 13px; font-weight: 700; text-decoration: none;">Reactivate</a>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

</div>
</div>
<?php require_once 'includes/footer.php'; ?>