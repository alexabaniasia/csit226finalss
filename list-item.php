<?php
    require_once 'includes/header.php';
    include 'connect.php';

    // Redirect to login if guest tries to access this page
    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    // Block Admins from accessing marketplace user features
    if(strtolower($_SESSION['role']) == 'admin'){
        header("Location: admin.php");
        exit();
    }

    $msg = "";
    $msg_class = "";

    if(isset($_POST['btnSubmit'])){
        $submitterID = $_SESSION['userID'];
        $title = mysqli_real_escape_string($connection, $_POST['itemName']);
        $type = $_POST['listingType'];
        $category = $_POST['category'];
        $description = mysqli_real_escape_string($connection, $_POST['description']);
        $condition = $_POST['condition'];
        $location = mysqli_real_escape_string($connection, $_POST['location']);
        
        // Initialize subtype variables
        $price = "NULL";
        $rentalFee = "NULL";
        $rentalPricePerDay = "NULL";
        $depositAmount = "NULL";
        $maxDays = "NULL";

        if($type == 'sale') {
            $price = floatval($_POST['salePrice']);
        }
        else if($type == 'rental') {
            $rentalPricePerDay = floatval($_POST['rentalPricePerDay']);
            $maxDays = intval($_POST['maxRentalDays']);
            $rentalFee = floatval($_POST['rentalFee']);
            $depositAmount = floatval($_POST['depositAmount']);
        }
        else if($type == 'borrow') {
            $maxDays = intval($_POST['maxBorrowTime']);
        }

        // Insert into listing_submissions table based on your new schema
        $sql_listing = "INSERT INTO listing_submissions 
                        (submitterID, listingType, title, category, description, itemCondition, location, price, rentalFee, rentalPricePerDay, depositAmount, maxDays, status) 
                        VALUES 
                        ('$submitterID', '$type', '$title', '$category', '$description', '$condition', '$location', $price, $rentalFee, $rentalPricePerDay, $depositAmount, $maxDays, 'pending')";
        
        if(mysqli_query($connection, $sql_listing)){
            $new_submission_id = mysqli_insert_id($connection);

            // ==========================================
            // IMAGE UPLOAD LOGIC
            // ==========================================
            if(isset($_FILES['itemImages']) && !empty($_FILES['itemImages']['name'][0])) {
                $uploadDir = 'uploads/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileCount = count($_FILES['itemImages']['name']);
                for($i = 0; $i < $fileCount; $i++) {
                    $fileName = basename($_FILES['itemImages']['name'][$i]);
                    // Create a unique file name to prevent overwriting images with the same name
                    $uniqueFileName = time() . '_' . uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $fileName);
                    $targetFilePath = $uploadDir . $uniqueFileName;
                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

                    // Allow certain file formats
                    $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
                    if(in_array($fileType, $allowTypes)){
                        // Upload file to server
                        if(move_uploaded_file($_FILES['itemImages']['tmp_name'][$i], $targetFilePath)){
                            // Insert image path into the database
                            $sortOrder = $i;
                            $stmt_img = $connection->prepare("INSERT INTO listing_submission_images (submissionID, imagePath, sortOrder) VALUES (?, ?, ?)");
                            $stmt_img->bind_param("isi", $new_submission_id, $targetFilePath, $sortOrder);
                            $stmt_img->execute();
                        }
                    }
                }
            }

            $msg = "Listing submitted successfully! Awaiting admin approval.";
            $msg_class = "success";
        } else {
            $msg = "Error creating listing: " . mysqli_error($connection);
            $msg_class = "error";
        }
    }
?>

<main style="position: relative; z-index: 9999; padding: 60px 20px;">
<section style="display: flex; flex-direction: column; align-items: center;">
    <div style="text-align: center; margin-bottom: 24px;">
    <h1 style="font-family: 'Nunito', sans-serif; font-size: 36px; font-weight: 800; color: #1a1a1a;">List Item</h1>
    </div>

    <div style="width: 100%; max-width: 980px; background: rgba(255,255,255,0.98); padding: 40px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
    
    <?php if($msg != ""): ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; <?php echo ($msg_class == 'success') ? 'background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3;' : 'background: #fff5f5; color: #b3261e; border: 1px solid #f2c0c0;'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        
        <div class="upload-zone" id="uploadZone" style="border: 2px dashed #ccc; border-radius: 12px; padding: 40px; text-align: center; position: relative; margin-bottom: 25px; background: #fafafa; cursor: pointer; transition: 0.2s;">
            <input type="file" accept="image/*" multiple id="fileInput" name="itemImages[]" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;" />
            <div class="upload-icon" style="color: #888;">
            <svg viewBox="0 0 24 24" style="width: 48px; height: 48px; stroke: currentColor; fill: none; stroke-width: 1.5;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div class="upload-label" style="font-size: 16px; font-weight: 700; color: #1a1a1a; margin-top: 10px;">Upload photo/s</div>
            <div class="upload-sub" style="font-size: 13px; color: #888; margin-top: 4px;">Click to browse or drag & drop</div>
            <div class="preview-grid" id="previewGrid" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 20px; position: relative; z-index: 3;"></div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 2;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Item Name</label>
                <input type="text" name="itemName" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Listing Type</label>
                <select name="listingType" id="listingType" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;" onchange="switchType(this.value)">
                    <option value="sale">Sale</option>
                    <option value="borrow">Borrow</option>
                    <option value="rental">Rental</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Category</label>
                <select name="category" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
                    <option value="" disabled selected>Select category</option>
                    <option value="books">Books</option>
                    <option value="school supplies">School Supplies</option>
                    <option value="gadgets">Gadgets</option>
                    <option value="uniforms">Uniforms</option>
                    <option value="others">Others</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Description</label>
            <textarea name="description" required style="width: 100%; height: 100px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; resize: none; font-family: inherit;"></textarea>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Condition</label>
                <select name="condition" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
                    <option value="new">New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div style="flex: 2;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Location / Meetup (Campus)</label>
                <input type="text" name="location" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
        </div>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <div id="fields-sale" style="margin-bottom: 20px;">
            <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Sale Price (₱)</label>
            <input type="number" name="salePrice" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
        </div>

        <div id="fields-borrow" style="display: none; margin-bottom: 20px;">
            <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Max Borrow Time (Days)</label>
            <input type="number" name="maxBorrowTime" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
        </div>

        <div id="fields-rental" style="display: none; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Price Per Day (₱)</label>
                <input type="number" name="rentalPricePerDay" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Max Rental Days</label>
                <input type="number" name="maxRentalDays" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Base Rental Fee (₱)</label>
                <input type="number" name="rentalFee" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Deposit (₱)</label>
                <input type="number" name="depositAmount" step="0.01" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
            <button type="reset" style="padding: 14px 40px; border-radius: 8px; border: 1px solid #ccc; background: #fff; color: #555; font-weight: 600; cursor: pointer;">Clear</button>
            <button type="submit" name="btnSubmit" style="padding: 14px 50px; border-radius: 8px; border: none; background: #8B2635; color: white; font-weight: bold; cursor: pointer;">Submit Listing</button>
        </div>
    </form>
    </div>
</section>
</main>

<script>
function switchType(type) {
    document.getElementById('fields-sale').style.display = (type === 'sale') ? 'block' : 'none';
    document.getElementById('fields-borrow').style.display = (type === 'borrow') ? 'block' : 'none';
    document.getElementById('fields-rental').style.display = (type === 'rental') ? 'flex' : 'none';
}

// IMAGE PREVIEW SCRIPT
const fileInput = document.getElementById('fileInput');
const previewGrid = document.getElementById('previewGrid');
const uploadZone = document.getElementById('uploadZone');

fileInput.addEventListener('change', () => {
    previewGrid.innerHTML = '';
    const files = Array.from(fileInput.files);
    
    if (files.length > 0) {
        uploadZone.style.borderColor = '#8B2635';
        uploadZone.style.background = '#fff';
    } else {
        uploadZone.style.borderColor = '#ccc';
        uploadZone.style.background = '#fafafa';
    }

    files.forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.width = '80px';
        img.style.height = '80px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '8px';
        img.style.border = '1px solid #ddd';
        previewGrid.appendChild(img);
    };
    reader.readAsDataURL(file);
    });
    
    const label = uploadZone.querySelector('.upload-label');
    label.textContent = files.length > 0 ? `${files.length} photo(s) selected` : 'Upload photo/s';
});
</script>

<?php require_once 'includes/footer.php'; ?>