<?php
    require_once 'includes/header.php';
    include 'connect.php';

    $msg = "";
    $msg_color = "";

    if(isset($_POST['btnRegister'])){
        $fname = mysqli_real_escape_string($connection, $_POST['txtfirstname']);
        $lname = mysqli_real_escape_string($connection, $_POST['txtlastname']);
        $email = mysqli_real_escape_string($connection, $_POST['txtemail']);
        $password = $_POST['txtpassword'];
        
        $check_sql = "SELECT * FROM users WHERE email='".$email."'";
        $check_result = mysqli_query($connection, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            $msg = "Email is already registered.";
            $msg_color = "#b3261e"; // Error Red
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql1 = "INSERT INTO users (firstName, lastName, email, passwordHash, role) 
                    VALUES ('".$fname."', '".$lname."', '".$email."', '".$hashedPassword."', 'Student')";
                    
            if(mysqli_query($connection, $sql1)){
                $msg = "Registration successful! You can now login.";
                $msg_color = "#1f7a34"; // Success Green
            } else {
                $msg = "Error: " . mysqli_error($connection);
                $msg_color = "#b3261e";
            }
        }
    }
?>

<main style="position: relative; z-index: 9999; min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
<div style="width: 100%; max-width: 480px; background: #fff; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="padding: 40px;">
    <h2 style="font-family: 'Nunito', sans-serif; font-size: 28px; margin-bottom: 20px; color: #1a1a1a;">Create an Account</h2>
    
    <?php if($msg != ""): ?>
        <div style="color: <?php echo $msg_color; ?>; border: 1px solid <?php echo $msg_color; ?>; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; background: #fafafa;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 600; color: #1a1a1a;">First Name</label>
                <input type="text" name="txtfirstname" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 600; color: #1a1a1a;">Last Name</label>
                <input type="text" name="txtlastname" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
            </div>
        </div>

        <label style="font-size: 14px; font-weight: 600; margin-top: 10px; color: #1a1a1a;">Email (CIT-U)</label>
        <input type="email" name="txtemail" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
        
        <label style="font-size: 14px; font-weight: 600; margin-top: 10px; color: #1a1a1a;">Password</label>
        <input type="password" name="txtpassword" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
        
        <button type="submit" name="btnRegister" style="margin-top: 20px; background: #8B2635; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: bold; cursor: pointer;">Register</button>
    </form>

    <div style="margin-top: 20px; text-align: center; font-size: 14px; color: #555;">
        Already have an account? <a href="login.php" style="color: #8B2635; font-weight: 600; text-decoration: none;">Sign in here</a>
    </div>
    </div>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>