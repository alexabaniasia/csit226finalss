<?php    
    $custom_css = 'style.css';
    include 'connect.php'; 
    require_once 'includes/header.php'; 

    if(isset($_POST['btnRegister'])){
        $fname = $_POST['txtFirstname'];
        $lname = $_POST['txtLastname'];
        $email = $_POST['txtEmail'];
        $pwd = $_POST['txtPassword'];
        $contact = $_POST['txtContact'];
        
        $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT); 
        
        $check_sql = "SELECT * FROM users WHERE email='".$email."'";
        $result = mysqli_query($connection, $check_sql);   
        $count = mysqli_num_rows($result);
        
        if($count > 0){
            echo "<script>alert('Email is already registered. Please log in.');</script>";
        } else {
            $insert_sql = "INSERT INTO users (firstName, lastName, email, passwordHash, contactNumber, role, status) 
                           VALUES ('".$fname."', '".$lname."', '".$email."', '".$hashed_pwd."', '".$contact."', 'student', 'active')";
            
            if(mysqli_query($connection, $insert_sql)){
                echo "<script>alert('Registration successful! You can now log in.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Error saving record.');</script>";
            }
        }
    }
?>

<main style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 40px 20px;">
  <div style="background: #fff; padding: 40px; border-radius: 16px; border: 1px solid #eaeaea; width: 100%; max-width: 500px; box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
    <h2 style="text-align: center; color: #8B2635; margin-bottom: 25px; font-size: 28px;">Create Account</h2>
    
    <form method="post" style="display: flex; flex-direction: column; gap: 16px;">
      
      <div style="display: flex; gap: 12px;">
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">First Name</label>
          <input type="text" name="txtFirstname" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
        </div>
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Last Name</label>
          <input type="text" name="txtLastname" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
        </div>
      </div>

      <div>
        <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Email Address</label>
        <input type="email" name="txtEmail" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
      </div>

      <div>
        <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Contact Number</label>
        <input type="text" name="txtContact" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
      </div>

      <div>
        <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Password</label>
        <input type="password" name="txtPassword" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
      </div>

      <button type="submit" name="btnRegister" class="maroon-btn" style="width: 100%; padding: 14px; font-size: 16px; margin-top: 10px;">Register</button>
      
      <div style="text-align: center; margin-top: 15px; font-size: 14px; color: #666;">
        Already have an account? <a href="login.php" style="color: #8B2635; font-weight: bold;">Log in</a>
      </div>
    </form>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>