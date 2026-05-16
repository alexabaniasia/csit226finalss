<?php
    require_once 'includes/header.php';
    include 'connect.php';

    $error_msg = "";

    if(isset($_POST['btnLogin'])){
        $email = mysqli_real_escape_string($connection, $_POST['txtemail']);
        $pwd = $_POST['txtpassword'];
        
        $sql = "SELECT * FROM users WHERE email='".$email."'";
        $result = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_array($result);
        
        if($count == 0){
            $error_msg = "Email not registered.";
        } else if(!password_verify($pwd, $row['passwordHash'])){
            $error_msg = "Incorrect password.";
        } else {
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['firstName'] = $row['firstName'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit();
        }
    }
?>

<main style="position: relative; z-index: 9999; min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
<div style="width: 100%; max-width: 460px; background: #fff; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="padding: 40px;">
    <h2 style="font-family: 'Nunito', sans-serif; font-size: 28px; margin-bottom: 20px; color: #1a1a1a;">Welcome Back</h2>
    
    <?php if($error_msg != ""): ?>
        <div style="color: #b3261e; background: #fff5f5; border: 1px solid #f2c0c0; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <label style="font-size: 14px; font-weight: 600; color: #1a1a1a;">Email</label>
        <input type="email" name="txtemail" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
        
        <label style="font-size: 14px; font-weight: 600; margin-top: 10px; color: #1a1a1a;">Password</label>
        <input type="password" name="txtpassword" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;">
        
        <button type="submit" name="btnLogin" style="margin-top: 20px; background: #8B2635; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.2s;">Login</button>
    </form>

    <div style="margin-top: 20px; text-align: center; font-size: 14px; color: #555;">
        Don't have an account? <a href="register.php" style="color: #8B2635; font-weight: 600; text-decoration: none;">Register here</a>
    </div>
    </div>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>