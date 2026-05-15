<?php 
    $custom_css = 'style.css';
    session_start();
    if(!isset($_SESSION['userID'])){
        echo "<script>alert('Please log in to list an item.'); window.location.href='login.php';</script>";
        exit;
    }

    include 'connect.php'; 
    require_once 'includes/header.php'; 

    if(isset($_POST['btnSubmit'])){
        $submitter_id = $_SESSION['userID'];
        $title = $_POST['txtItemName'];
        $type = $_POST['selListingType']; 
        $category = $_POST['selCategory'];
        $description = $_POST['txtDescription'];
        $condition = $_POST['selCondition'];
        $location = $_POST['txtLocation'];

        $price = 'NULL';
        $rental_fee = 'NULL';
        $rental_price_per_day = 'NULL';
        $deposit_amount = 'NULL';
        $max_days = 'NULL';
        $avail_from = 'NULL';
        $avail_to = 'NULL';

        if($type == 'sale'){
            $price = !empty($_POST['numSalePrice']) ? $_POST['numSalePrice'] : 'NULL';
        } else if($type == 'rent'){
            $max_days = !empty($_POST['numMaxRentalDays']) ? $_POST['numMaxRentalDays'] : 'NULL';
            $rental_fee = !empty($_POST['numRentalFee']) ? $_POST['numRentalFee'] : 'NULL';
            $deposit_amount = !empty($_POST['numDepositAmount']) ? $_POST['numDepositAmount'] : 'NULL';
            $rental_price_per_day = !empty($_POST['numRentalPricePerDay']) ? $_POST['numRentalPricePerDay'] : 'NULL';
            $avail_from = !empty($_POST['dateRentFrom']) ? "'".$_POST['dateRentFrom']."'" : 'NULL';
            $avail_to = !empty($_POST['dateRentTo']) ? "'".$_POST['dateRentTo']."'" : 'NULL';
        } else if($type == 'borrow'){
            $max_days = !empty($_POST['numMaxBorrowTime']) ? $_POST['numMaxBorrowTime'] : 'NULL';
            $avail_from = !empty($_POST['dateBorrowFrom']) ? "'".$_POST['dateBorrowFrom']."'" : 'NULL';
            $avail_to = !empty($_POST['dateBorrowTo']) ? "'".$_POST['dateBorrowTo']."'" : 'NULL';
        }

        $sql = "INSERT INTO listing_submissions 
                (submitterID, listingType, title, category, description, itemCondition, location, price, rentalFee, rentalPricePerDay, depositAmount, maxDays, availabilityFrom, availabilityTo, status) 
                VALUES 
                (".$submitter_id.", '".$type."', '".$title."', '".$category."', '".$description."', '".$condition."', '".$location."', ".$price.", ".$rental_fee.", ".$rental_price_per_day.", ".$deposit_amount.", ".$max_days.", ".$avail_from.", ".$avail_to.", 'pending')";

        if(mysqli_query($connection, $sql)){
            echo "<script>alert('Listing submitted successfully! Awaiting admin approval.'); window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Error submitting listing: " . mysqli_error($connection) . "');</script>";
        }
    }
?>

<main style="max-width: 750px; margin: 40px auto; padding: 20px;">
  <div style="background: #fff; padding: 40px; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
    
    <h2 style="color: #8B2635; margin-bottom: 25px; border-bottom: 2px solid #8B2635; padding-bottom: 12px; font-size: 26px;">List a New Item</h2>

    <form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 16px;">
      
      <div>
        <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Item Name</label>
        <input type="text" name="txtItemName" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
      </div>

      <div style="display: flex; gap: 16px;">
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Listing Type</label>
          <select name="selListingType" id="listingType" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
            <option value="borrow">Borrow</option>
            <option value="sale">Sale</option>
            <option value="rent">Rent</option>
          </select>
        </div>
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Category</label>
          <select name="selCategory" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
            <option value="books">Books</option>
            <option value="supplies">School Supplies</option>
            <option value="gadgets">Gadgets</option>
            <option value="uniforms">Uniforms</option>
            <option value="others">Others</option>
          </select>
        </div>
      </div>

      <div>
        <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Description</label>
        <textarea name="txtDescription" required rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; resize: vertical;"></textarea>
      </div>

      <div style="display: flex; gap: 16px;">
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Condition</label>
          <select name="selCondition" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
            <option value="new">Brand New</option>
            <option value="good">Good</option>
            <option value="fair">Fair</option>
            <option value="poor">Poor</option>
          </select>
        </div>
        <div style="flex: 1;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px; color: #333;">Location / Meetup</label>
          <input type="text" name="txtLocation" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
        </div>
      </div>

      <div style="padding: 20px; background: #fafafa; border: 2px dashed #ccc; border-radius: 8px; margin-top: 10px; text-align: center;">
        <label style="font-weight: 600; display: block; margin-bottom: 10px; color: #555;">Upload Image (Optional)</label>
        <input type="file" name="fileImages[]" accept="image/*" style="font-size: 14px; color: #666;">
      </div>

      <div id="fields-borrow" style="padding: 20px; background: #fdfdfd; border-radius: 8px; border: 1px solid #eaeaea; margin-top: 10px;">
        <strong style="color: #8B2635; font-size: 16px;">Borrow Settings</strong>
        <label style="display: block; margin-top: 12px; font-weight: 600; font-size: 14px;">Max Borrow Time (Days)</label>
        <input type="number" name="numMaxBorrowTime" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 6px;">
      </div>

      <div id="fields-sale" style="display: none; padding: 20px; background: #fdfdfd; border-radius: 8px; border: 1px solid #eaeaea; margin-top: 10px;">
        <strong style="color: #8B2635; font-size: 16px;">Sale Settings</strong>
        <label style="display: block; margin-top: 12px; font-weight: 600; font-size: 14px;">Price (₱)</label>
        <input type="number" name="numSalePrice" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 6px;">
      </div>

      <div id="fields-rent" style="display: none; padding: 20px; background: #fdfdfd; border-radius: 8px; border: 1px solid #eaeaea; margin-top: 10px;">
        <strong style="color: #8B2635; font-size: 16px;">Rent Settings</strong>
        <div style="display: flex; gap: 15px; margin-top: 12px;">
            <div style="flex: 1;">
                <label style="display: block; font-weight: 600; font-size: 14px;">Price Per Day (₱)</label>
                <input type="number" name="numRentalPricePerDay" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 6px;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: 600; font-size: 14px;">Security Deposit (₱)</label>
                <input type="number" name="numDepositAmount" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-top: 6px;">
            </div>
        </div>
      </div>

      <button type="submit" name="btnSubmit" class="maroon-btn" style="width: 100%; padding: 16px; font-size: 18px; margin-top: 20px;">Submit Listing</button>
    </form>
  </div>
</main>

<script>
  const listingType = document.getElementById('listingType');
  function switchType(type) {
    document.getElementById('fields-borrow').style.display = (type === 'borrow') ? 'block' : 'none';
    document.getElementById('fields-sale').style.display = (type === 'sale') ? 'block' : 'none';
    document.getElementById('fields-rent').style.display = (type === 'rent') ? 'block' : 'none';
  }
  listingType.addEventListener('change', () => switchType(listingType.value));
  switchType(listingType.value);
</script>

<?php require_once 'includes/footer.php'; ?>