<?php 
    include 'connect.php'; 
    require_once 'includes/header.php'; 

    if(isset($_POST['btnLogin'])){
        $email = $_POST['txtEmail'];
        $pwd = $_POST['txtPassword'];
        
        $sql = "SELECT * FROM users WHERE email='".$email."'";
        $result = mysqli_query($connection, $sql);  
        
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        
        if($count == 0){
            echo "<script>alert('Email not registered. Please register first.');</script>";
        } else if($row['status'] == 'inactive'){
            echo "<script>alert('Your account has been deactivated by an administrator.');</script>";
        } else if(!password_verify($pwd, $row['passwordHash'])){
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        } else {
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['firstname'] = $row['firstName'];
            $_SESSION['role'] = $row['role'];
            
            if($row['role'] == 'admin'){
                echo "<script>alert('Admin login successful!'); window.location.href='admin.php';</script>";
            } else {
                echo "<script>alert('Login successful!'); window.location.href='home.php';</script>";
            }
        }
    }
?>

<main style="display: flex; justify-content: center; align-items: center; min-height: 65vh; padding: 40px 20px;">
  <div style="background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 16px; border: 1px solid #eaeaea; width: 100%; max-width: 420px; box-shadow: 0 8px 24px rgba(0,0,0,0.04); backdrop-filter: blur(5px);">
    <h2 style="text-align: center; color: #8B2635; margin-bottom: 25px; font-size: 28px;">Log In</h2>
    
    <form method="post" style="display: flex; flex-direction: column; gap: 16px;">
      <div>
        <label>Email Address</label>
        <input type="email" name="txtEmail" required placeholder="Enter your email">
      </div>

      <div>
        <label>Password</label>
        <input type="password" name="txtPassword" required placeholder="Enter your password">
      </div>

      <button type="submit" name="btnLogin" class="maroon-btn" style="width: 100%; padding: 14px; font-size: 16px; margin-top: 10px;">Log In</button>
      
      <div style="text-align: center; margin-top: 15px; font-size: 14px; color: #666;">
        Don't have an account? <a href="register.php" style="color: #8B2635; font-weight: bold;">Register here</a>
      </div>
    </form>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>