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

    if(isset($_POST['action']) && isset($_POST['listing_id'])){
        $list_id = $_POST['listing_id'];
        if($_POST['action'] == 'delete'){
            mysqli_query($connection, "UPDATE listings SET listingStatus = 'deleted' WHERE listingID = $list_id");
            echo "<script>alert('Listing removed.');</script>";
        } else if($_POST['action'] == 'hide'){
            mysqli_query($connection, "UPDATE listings SET listingStatus = 'closed' WHERE listingID = $list_id");
            echo "<script>alert('Listing hidden from marketplace.');</script>";
        } else if($_POST['action'] == 'activate'){
            mysqli_query($connection, "UPDATE listings SET listingStatus = 'active' WHERE listingID = $list_id");
            echo "<script>alert('Listing is now active!');</script>";
        }
    }

    $query = "SELECT l.listingID, l.listingStatus, l.listingType, i.name, i.itemCondition 
              FROM listings l, items i 
              WHERE l.itemID = i.itemID 
              AND i.ownerID = $user_id 
              AND l.listingStatus != 'deleted'
              ORDER BY l.datePosted DESC";
    $result = mysqli_query($connection, $query);
?>

<main style="max-width: 1000px; margin: 40px auto; padding: 20px;">
  <div style="margin-bottom: 30px;">
    <a href="profile.php" style="color: #8B2635; text-decoration: none; font-weight: bold; font-size: 14px;">← Back to Profile</a>
    <h1 style="color: #8B2635; margin: 15px 0 5px 0; font-size: 32px;">My Listings</h1>
    <p style="color: #666; margin: 0; font-size: 16px;">Manage your inventory on Maroon Market.</p>
  </div>

  <div style="display: flex; flex-direction: column; gap: 16px;">
    <?php if(mysqli_num_rows($result) == 0): ?>
      <div style="background: #fff; padding: 40px; text-align: center; border-radius: 12px; border: 1px dashed #ccc; color: #666;">
        You haven't listed any items yet. <br><br>
        <a href="list-item.php" class="maroon-btn" style="padding: 10px 20px;">Create a Listing</a>
      </div>
    <?php endif; ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <article class="feature-card" style="padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <h4 style="margin: 0 0 8px 0; font-size: 20px; color: #222;"><?php echo $row['name']; ?></h4>
        <p style="margin: 0; color: #666; font-size: 14px;">
          Type: <strong style="text-transform: capitalize; color: #333;"><?php echo $row['listingType']; ?></strong> &nbsp;|&nbsp; 
          Condition: <strong style="color: #333;"><?php echo ucfirst($row['itemCondition']); ?></strong> &nbsp;|&nbsp;
          Status: <strong style="color: <?php echo ($row['listingStatus']=='active') ? '#2e7d32' : '#d32f2f'; ?>"><?php echo ucfirst($row['listingStatus']); ?></strong>
        </p>
      </div>
      
      <div style="display: flex; gap: 10px;">
          <?php if($row['listingStatus'] == 'active'): ?>
              <form method="post">
                  <input type="hidden" name="listing_id" value="<?php echo $row['listingID']; ?>">
                  <input type="hidden" name="action" value="hide">
                  <button type="submit" style="padding: 10px 16px; border-radius: 6px; border: 1px solid #ddd; background: #fff; font-weight: bold; cursor: pointer; transition: background 0.2s;">Hide</button>
              </form>
          <?php else: ?>
              <form method="post">
                  <input type="hidden" name="listing_id" value="<?php echo $row['listingID']; ?>">
                  <input type="hidden" name="action" value="activate">
                  <button type="submit" style="padding: 10px 16px; border-radius: 6px; border: 1px solid #c3e6cb; background: #e6ffe6; color: #006600; font-weight: bold; cursor: pointer;">Activate</button>
              </form>
          <?php endif; ?>
          
          <form method="post" onsubmit="return confirm('Are you sure you want to delete this listing?');">
              <input type="hidden" name="listing_id" value="<?php echo $row['listingID']; ?>">
              <input type="hidden" name="action" value="delete">
              <button type="submit" style="padding: 10px 16px; border-radius: 6px; border: 1px solid #f5c6cb; background: #fff5f5; color: #b3261e; font-weight: bold; cursor: pointer;">Delete</button>
          </form>
      </div>
    </article>
    <?php endwhile; ?>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>