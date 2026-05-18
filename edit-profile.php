<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    $userID = $_SESSION['userID'];
    $msg = "";
    $msg_type = "";

    $user_query = mysqli_query($connection, "SELECT firstName, lastName, contactNumber FROM users WHERE userID = '$userID'");
    $user = mysqli_fetch_assoc($user_query);

    if(isset($_POST['btnUpdateProfile'])){
        $fname = mysqli_real_escape_string($connection, $_POST['fname']);
        $lname = mysqli_real_escape_string($connection, $_POST['lname']);
        $contact = mysqli_real_escape_string($connection, $_POST['contact']);

        $update_sql = "UPDATE users SET firstName = '$fname', lastName = '$lname', contactNumber = '$contact' WHERE userID = '$userID'";
        
        if(mysqli_query($connection, $update_sql)){
            $_SESSION['firstName'] = $fname; 
            $msg = "Profile updated successfully!";
            $msg_type = "success";
            
            $user['firstName'] = $fname;
            $user['lastName'] = $lname;
            $user['contactNumber'] = $contact;
        } else {
            $msg = "Error updating profile.";
            $msg_type = "error";
        }
    }
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; padding: 60px 20px; display: flex; justify-content: center;">
    <div style="width: 100%; max-width: 500px; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family: 'Nunito', sans-serif; font-size: 24px; color: #1a1a1a; margin: 0;">Edit Profile</h2>
        <a href="profile.php" style="color: #8B2635; font-size: 14px; font-weight: 600; text-decoration: none;">&larr; Back</a>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: <?php echo ($msg_type == 'success') ? '#f2fbf5' : '#fff5f5'; ?>; color: <?php echo ($msg_type == 'success') ? '#1f7a34' : '#b3261e'; ?>; border: 1px solid <?php echo ($msg_type == 'success') ? '#b6ddc3' : '#f2c0c0'; ?>; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 15px;">
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">First Name</label>
                <input type="text" name="fname" value="<?php echo htmlspecialchars($user['firstName']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Last Name</label>
                <input type="text" name="lname" value="<?php echo htmlspecialchars($user['lastName']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
        </div>

        <div>
            <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Contact Number</label>
            <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contactNumber']); ?>" placeholder="09XX XXX XXXX" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            <p style="font-size: 12px; color: #888; margin-top: 4px;">Buyers need this to coordinate campus meetups with you.</p>
        </div>

        <button type="submit" name="btnUpdateProfile" style="margin-top: 10px; padding: 14px; border-radius: 8px; border: none; background: #8B2635; color: white; font-weight: bold; cursor: pointer;">Save Changes</button>
    </form>

    </div>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>