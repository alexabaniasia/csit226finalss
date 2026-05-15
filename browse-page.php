<?php 
    $custom_css = 'style.css'; 
    include 'connect.php'; 
    require_once 'includes/header.php'; 

    $filter_category = isset($_GET['category']) ? $_GET['category'] : '';
    $filter_type = isset($_GET['type']) ? $_GET['type'] : '';

    $query = "SELECT l.listingID, l.listingType, i.name, i.itemCondition, i.category, u.firstName 
              FROM listings l, items i, users u 
              WHERE l.itemID = i.itemID 
              AND i.ownerID = u.userID 
              AND l.listingStatus = 'active'";

    if ($filter_category != '') {
        $query .= " AND i.category = '" . mysqli_real_escape_string($connection, $filter_category) . "'";
    }
    if ($filter_type != '') {
        $query .= " AND l.listingType = '" . mysqli_real_escape_string($connection, $filter_type) . "'";
    }

    $query .= " ORDER BY l.datePosted DESC";
    $result = mysqli_query($connection, $query);
?>

<main style="max-width: 1100px; margin: 40px auto; padding: 20px;">
  
  <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
      <h1 style="color: #8B2635; margin: 0 0 5px 0; font-size: 32px;">Browse Listings</h1>
      <p style="color: #666; margin: 0;">Discover items available on campus.</p>
    </div>
  </div>

  <form method="GET" style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #eaeaea; box-shadow: 0 4px 10px rgba(0,0,0,0.02); margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-end;">
    <div style="flex: 1;">
      <label style="font-weight: 600; font-size: 14px; margin-bottom: 6px; display: block; color: #333;">Category</label>
      <select name="category" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
        <option value="">All Categories</option>
        <option value="books" <?php if($filter_category=='books') echo 'selected'; ?>>Books</option>
        <option value="electronics" <?php if($filter_category=='electronics') echo 'selected'; ?>>Electronics</option>
        <option value="supplies" <?php if($filter_category=='supplies') echo 'selected'; ?>>School Supplies</option>
        <option value="uniforms" <?php if($filter_category=='uniforms') echo 'selected'; ?>>Uniforms</option>
      </select>
    </div>
    <div style="flex: 1;">
      <label style="font-weight: 600; font-size: 14px; margin-bottom: 6px; display: block; color: #333;">Listing Type</label>
      <select name="type" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
        <option value="">All Types</option>
        <option value="sale" <?php if($filter_type=='sale') echo 'selected'; ?>>For Sale</option>
        <option value="rent" <?php if($filter_type=='rent') echo 'selected'; ?>>For Rent</option>
        <option value="borrow" <?php if($filter_type=='borrow') echo 'selected'; ?>>For Borrow</option>
      </select>
    </div>
    <button type="submit" class="maroon-btn" style="padding: 10px 24px; height: 41px;">Apply Filters</button>
    <a href="browse-page.php" style="padding: 10px 15px; background: #f0f0f0; color: #555; text-decoration: none; border-radius: 6px; font-weight: 600; height: 41px; box-sizing: border-box;">Clear</a>
  </form>

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">

    <?php
      if(mysqli_num_rows($result) == 0){
          echo "<div style='grid-column: 1 / -1; background: #fff; padding: 40px; text-align: center; border-radius: 12px; border: 1px dashed #ccc; color: #666;'>No items found matching your filters.</div>";
      }

      while($row = mysqli_fetch_assoc($result)): 
          $listing_id = $row['listingID'];
          $type = $row['listingType'];
          
          $price_display = '';
          $badge_color = '#888'; 
          
          if($type == 'sale'){
              $sale_query = mysqli_query($connection, "SELECT price FROM sale_listings WHERE listingID = $listing_id");
              $sale_row = mysqli_fetch_assoc($sale_query);
              $price_display = '₱' . number_format($sale_row['price'], 2);
              $badge_text = 'For Sale';
              $badge_color = '#af5353';
          } else if($type == 'rent'){
              $rent_query = mysqli_query($connection, "SELECT rentalPricePerDay FROM rental_listings WHERE listingID = $listing_id");
              $rent_row = mysqli_fetch_assoc($rent_query);
              $price_display = '₱' . number_format($rent_row['rentalPricePerDay'], 2) . ' <span style="font-size:14px; color:#888;">/day</span>';
              $badge_text = 'For Rent';
              $badge_color = '#4a79d6';
        } else if($type == 'borrow'){
              $price_display = '<span style="color: #3a8a3a;">Free</span>';
              $badge_text = 'For Borrow';
              $badge_color = '#9a6b2b';
          }
    ?>
    
    <div class="feature-card" style="padding: 24px; display: flex; flex-direction: column;">
      <div style="background: <?php echo $badge_color; ?>; color: white; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 15px; align-self: flex-start;">
          <?php echo $badge_text; ?>
      </div>
      
      <h3 style="margin: 0 0 12px 0; color: #222; font-size: 20px; line-height: 1.3;"><?php echo $row['name']; ?></h3>
      <p style="margin: 0 0 6px 0; font-size: 14px; color: #666;"><strong>Category:</strong> <?php echo ucfirst($row['category']); ?></p>
      <p style="margin: 0 0 6px 0; font-size: 14px; color: #666;"><strong>Condition:</strong> <?php echo ucfirst($row['itemCondition']); ?></p>
      <p style="margin: 0 0 15px 0; font-size: 14px; color: #666;"><strong>Seller:</strong> <?php echo $row['firstName']; ?></p>
      
      <div style="font-size: 24px; font-weight: 800; color: #1a1a1a; margin-top: auto; padding-top: 15px; margin-bottom: 20px; border-top: 1px solid #f0f0f0;">
        <?php echo $price_display; ?>
      </div>

      <button class="maroon-btn" style="width: 100%; padding: 12px;" onclick="window.location.href='item-details.php?id=<?php echo $row['listingID']; ?>';">
        View Details
      </button>
    </div>

    <?php endwhile; ?>

  </div>
</main>

<?php require_once 'includes/footer.php'; ?>