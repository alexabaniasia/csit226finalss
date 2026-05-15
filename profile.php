<?php 
    $custom_css = 'style.css';
    session_start();
    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit;
    }
    
    include 'connect.php'; 
    require_once 'includes/header.php'; 
    
    $user_id = $_SESSION['userID'];
    
    $listing_query = mysqli_query($connection, "SELECT count(*) as total FROM items WHERE ownerID = $user_id");
    $listing_stat = mysqli_fetch_assoc($listing_query);
    
    $user_query = mysqli_query($connection, "SELECT email, role FROM users WHERE userID = $user_id");
    $user_data = mysqli_fetch_assoc($user_query);
?>

<main style="max-width: 1000px; margin: 40px auto; padding: 20px;">
  
  <div style="margin-bottom: 30px;">
    <p style="text-transform: uppercase; font-size: 13px; font-weight: bold; color: #888; margin: 0 0 5px 0;">Account</p>
    <h1 style="color: #8B2635; margin: 0 0 10px 0; font-size: 32px;">Profile Dashboard</h1>
    <p style="color: #555; font-size: 16px; margin: 0;">Manage your identity and activity across the marketplace.</p>
  </div>

  <div style="display: flex; flex-direction: column; gap: 30px;">
    
    <section style="background: #fff; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 8px 24px rgba(0,0,0,0.03); display: flex; flex-wrap: wrap; overflow: hidden;">
      <div style="padding: 30px; flex: 2; min-width: 300px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
          <h2 style="font-size: 28px; margin: 0; color: #222;"><?php echo $_SESSION['firstname']; ?></h2>
          <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; border: 1px solid #c8e6c9;">Verified CIT-U</span>
        </div>
        <p style="font-size: 16px; color: #555; margin: 0 0 8px 0;"><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
        <p style="font-size: 16px; color: #555; margin: 0; text-transform: capitalize;"><strong>Role:</strong> <?php echo $user_data['role']; ?></p>
      </div>
      
      <div style="padding: 30px; flex: 1; min-width: 200px; background: #fafafa; border-left: 1px solid #eaeaea; display: flex; flex-direction: column; justify-content: center; gap: 20px;">
        <div>
          <div style="font-size: 28px; font-weight: 800; color: #8B2635; line-height: 1;"><?php echo $listing_stat['total']; ?></div>
          <div style="font-size: 14px; color: #666; font-weight: 600; margin-top: 5px; text-transform: uppercase;">My Items</div>
        </div>
        <div>
          <div style="font-size: 20px; font-weight: 800; color: #2e7d32; line-height: 1;">Active</div>
          <div style="font-size: 14px; color: #666; font-weight: 600; margin-top: 5px; text-transform: uppercase;">Account Status</div>
        </div>
      </div>
    </section>

    <section>
      <h3 style="color: #8B2635; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">Activity Overview</h3>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        
        <article class="feature-card" style="padding: 24px; display: flex; flex-direction: column; cursor: pointer;" onclick="window.location.href='my-listings.php';">
          <h4 style="font-size: 18px; margin: 0 0 10px 0; color: #222;">My Listings</h4>
          <p style="font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.5;">Manage the items you've posted to the marketplace.</p>
          <a href="my-listings.php" style="color: #8B2635; font-weight: bold; text-decoration: none; margin-top: auto; display: inline-flex; align-items: center; gap: 5px;">Open my listings <span style="font-size: 18px;">→</span></a>
        </article>

        <article class="feature-card" style="padding: 24px; display: flex; flex-direction: column; cursor: pointer;" onclick="window.location.href='seller-dashboard.php';">
          <h4 style="font-size: 18px; margin: 0 0 10px 0; color: #222;">Seller Dashboard</h4>
          <p style="font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.5;">Review requests from buyers or renters.</p>
          <a href="seller-dashboard.php" style="color: #8B2635; font-weight: bold; text-decoration: none; margin-top: auto; display: inline-flex; align-items: center; gap: 5px;">Open seller dashboard <span style="font-size: 18px;">→</span></a>
        </article>

        <article class="feature-card" style="padding: 24px; display: flex; flex-direction: column; cursor: pointer;" onclick="window.location.href='transaction-history.php';">
          <h4 style="font-size: 18px; margin: 0 0 10px 0; color: #222;">Transaction History</h4>
          <p style="font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.5;">Track your past purchases, rentals, and borrowed items.</p>
          <a href="transaction-history.php" style="color: #8B2635; font-weight: bold; text-decoration: none; margin-top: auto; display: inline-flex; align-items: center; gap: 5px;">View history <span style="font-size: 18px;">→</span></a>
        </article>
        
        <?php if($user_data['role'] == 'admin'): ?>
        <article class="feature-card" style="padding: 24px; display: flex; flex-direction: column; background: #fff5f5; border-color: #f5d0d0; cursor: pointer;" onclick="window.location.href='admin.php';">
          <h4 style="font-size: 18px; margin: 0 0 10px 0; color: #b3261e;">Admin Moderation</h4>
          <p style="font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.5;">Approve or reject pending marketplace submissions.</p>
          <a href="admin.php" style="color: #b3261e; font-weight: bold; text-decoration: none; margin-top: auto; display: inline-flex; align-items: center; gap: 5px;">Open admin panel <span style="font-size: 18px;">→</span></a>
        </article>
        <?php endif; ?>
        
      </div>
    </section>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>