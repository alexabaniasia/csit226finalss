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

    $query = "SELECT t.*, l.listingType, i.name, 
                     u1.firstName as sender_name, u2.firstName as receiver_name 
              FROM transactions t, listings l, items i, users u1, users u2 
              WHERE t.listingID = l.listingID 
              AND l.itemID = i.itemID 
              AND t.senderID = u1.userID 
              AND t.receiverID = u2.userID 
              AND (t.senderID = $user_id OR t.receiverID = $user_id)
              ORDER BY t.checkoutDate DESC";
    $result = mysqli_query($connection, $query);
?>

<main style="max-width: 1000px; margin: 40px auto; padding: 20px;">
  <div style="margin-bottom: 30px;">
    <a href="profile.php" style="color: #8B2635; text-decoration: none; font-weight: bold; font-size: 14px;">← Back to Profile</a>
    <h1 style="color: #8B2635; margin: 15px 0 5px 0; font-size: 32px;">Transaction History</h1>
    <p style="color: #666; margin: 0; font-size: 16px;">View your past purchases, rentals, and borrowed items.</p>
  </div>

  <div style="display: flex; flex-direction: column; gap: 16px;">
      <?php if(mysqli_num_rows($result) == 0): ?>
          <div style="background: #fff; padding: 40px; text-align: center; border-radius: 12px; border: 1px dashed #ccc; color: #666;">No transaction history found.</div>
      <?php else: ?>
          <?php while($row = mysqli_fetch_assoc($result)): 
              $is_buyer = ($row['senderID'] == $user_id);
              $role_label = $is_buyer ? "Bought from: " . $row['receiver_name'] : "Sold/Rented to: " . $row['sender_name'];
              
              $status_color = '#666'; $status_bg = '#f0f0f0';
              if($row['status'] == 'completed' || $row['status'] == 'returned') { $status_color = '#2e7d32'; $status_bg = '#e8f5e9'; }
              if($row['status'] == 'pending') { $status_color = '#ed6c02'; $status_bg = '#fff3e0'; }
              if($row['status'] == 'cancelled' || $row['status'] == 'rejected') { $status_color = '#d32f2f'; $status_bg = '#ffebee'; }
          ?>
          
          <article class="feature-card" style="padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
              <div>
                  <div style="font-size: 12px; color: #888; margin-bottom: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                      <?php echo $row['transaction_type']; ?>
                  </div>
                  <h4 style="margin: 0 0 8px 0; font-size: 20px; color: #222;"><?php echo $row['name']; ?></h4>
                  <p style="margin: 0; font-size: 14px; color: #666;">
                      <strong style="color:#333;"><?php echo $role_label; ?></strong> &nbsp;|&nbsp; Date: <?php echo date("M j, Y", strtotime($row['checkout_date'])); ?>
                  </p>
              </div>
              
              <div style="text-align: right;">
                  <div style="font-weight: 800; font-size: 22px; color: #1a1a1a; margin-bottom: 8px;">₱<?php echo number_format($row['amount'], 2); ?></div>
                  <div style="font-size: 12px; font-weight: 800; color: <?php echo $status_color; ?>; background: <?php echo $status_bg; ?>; padding: 6px 12px; border-radius: 6px; display: inline-block; text-transform: uppercase;">
                      <?php echo $row['status']; ?>
                  </div>
              </div>
          </article>
          
          <?php endwhile; ?>
      <?php endif; ?>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>