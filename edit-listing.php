<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID'])){
        header("Location: login.php");
        exit();
    }

    if(!isset($_GET['id'])){
        header("Location: my-listings.php");
        exit();
    }

    $listingID = intval($_GET['id']);
    $userID = $_SESSION['userID'];
    $msg = "";

    $query = "SELECT l.*, i.name, i.description, i.itemCondition 
            FROM listings l 
            JOIN items i ON l.itemID = i.itemID 
            WHERE l.listingID = '$listingID' AND i.ownerID = '$userID'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) == 0){
        echo "<div style='padding: 100px; text-align: center;'>Listing not found or you do not have permission to edit it.</div>";
        exit();
    }

    $item = mysqli_fetch_assoc($result);

    if(isset($_POST['btnUpdateListing'])){
        $location = mysqli_real_escape_string($connection, $_POST['location']);
        $condition = mysqli_real_escape_string($connection, $_POST['condition']);
        $description = mysqli_real_escape_string($connection, $_POST['description']);

        mysqli_begin_transaction($connection);
        try {
            $stmt1 = $connection->prepare("UPDATE items SET itemCondition = ?, description = ? WHERE itemID = ?");
            $stmt1->bind_param("ssi", $condition, $description, $item['itemID']);
            $stmt1->execute();

            $stmt2 = $connection->prepare("UPDATE listings SET location = ? WHERE listingID = ?");
            $stmt2->bind_param("si", $location, $listingID);
            $stmt2->execute();

            mysqli_commit($connection);
            $msg = "Listing updated successfully!";
            
            $item['location'] = $location;
            $item['itemCondition'] = $condition;
            $item['description'] = $description;

        } catch (Exception $e) {
            mysqli_rollback($connection);
            $msg = "Error updating listing.";
        }
    }
?>

<div class="page">
<div class="bg-image"></div>
<main style="position: relative; z-index: 10; padding: 60px 20px; display: flex; justify-content: center;">
    <div style="width: 100%; max-width: 600px; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family: 'Nunito', sans-serif; font-size: 24px; color: #1a1a1a; margin: 0;">Edit Listing</h2>
        <a href="my-listings.php" style="color: #8B2635; font-size: 14px; font-weight: 600; text-decoration: none;">&larr; Back to My Listings</a>
    </div>

    <p style="background: #f5f5f5; padding: 10px 15px; border-radius: 8px; font-weight: 700; color: #333; margin-bottom: 20px;">
        Item: <?php echo htmlspecialchars($item['name']); ?>
    </p>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="display: flex; flex-direction: column; gap: 15px;">
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Condition</label>
                <select name="condition" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
                    <option value="new" <?php if($item['itemCondition'] == 'new') echo 'selected'; ?>>New</option>
                    <option value="good" <?php if($item['itemCondition'] == 'good') echo 'selected'; ?>>Good</option>
                    <option value="fair" <?php if($item['itemCondition'] == 'fair') echo 'selected'; ?>>Fair</option>
                    <option value="poor" <?php if($item['itemCondition'] == 'poor') echo 'selected'; ?>>Poor</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Meetup Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($item['location']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit;">
            </div>
        </div>

        <div>
            <label style="font-size: 14px; font-weight: 700; color: #1a1a1a;">Description</label>
            <textarea name="description" required rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; font-family: inherit; resize: none;"><?php echo htmlspecialchars($item['description']); ?></textarea>
        </div>

        <button type="submit" name="btnUpdateListing" style="margin-top: 10px; padding: 14px; border-radius: 8px; border: none; background: #8B2635; color: white; font-weight: bold; cursor: pointer;">Save Changes</button>
    </form>

    </div>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>