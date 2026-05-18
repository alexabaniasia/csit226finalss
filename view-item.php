<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $id = intval($_GET['id']);
    
    $query = "SELECT l.listingID, l.listingType as type, l.listingStatus as status, l.location,
                    i.name as title, i.itemCondition as `condition`, i.description, 
                    u.firstName, u.lastName, u.userID as ownerID,
                    s.price as salePrice, 
                    r.rentalPricePerDay, r.maxDays as rentMaxDays, r.deposit, 
                    b.maxDays as borrowMaxDays
            FROM listings l
            JOIN items i ON l.itemID = i.itemID 
            JOIN users u ON i.ownerID = u.userID 
            LEFT JOIN sale_listings s ON l.listingID = s.listingID
            LEFT JOIN rental_listings r ON l.listingID = r.listingID
            LEFT JOIN borrow_listings b ON l.listingID = b.listingID
            WHERE l.listingID = $id";
            
    $result = mysqli_query($connection, $query);
    if(mysqli_num_rows($result) == 0) {
        echo "<div style='padding: 100px; text-align: center; font-size: 20px;'>Item not found or has been removed.</div>";
        exit();
    }
    
    $item = mysqli_fetch_assoc($result);
    $img_query = mysqli_query($connection, "SELECT imagePath FROM listing_images WHERE listingID = '$id' ORDER BY sortOrder ASC LIMIT 1");
    $img_data = mysqli_fetch_assoc($img_query);
    $main_image = $img_data ? $img_data['imagePath'] : null;

    $price = 0;
    $maxDays = 0;
    if($item['type'] == 'sale') $price = $item['salePrice'];
    if($item['type'] == 'rental') { $price = $item['rentalPricePerDay']; $maxDays = $item['rentMaxDays']; }
    if($item['type'] == 'borrow') $maxDays = $item['borrowMaxDays'];
?>

<div class="page">
<div class="bg-image"></div>
<main class="main" style="display: flex; justify-content: center; padding-top: 60px;">
    
    <div style="background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); width: 100%; max-width: 900px; padding: 32px; display: grid; grid-template-columns: 1fr 1.05fr; gap: 32px; align-items: start;">
    
    <div style="position: relative; background: #e5e0e0; border-radius: 12px; aspect-ratio: 1 / 1.05; display: flex; align-items: center; justify-content: center; overflow: hidden; min-height: 360px;">
        <?php if($main_image): ?>
            <img src="<?php echo htmlspecialchars($main_image); ?>" alt="Item Image" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.2" style="width: 80px; height: 80px;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m3 9 4-4 4 4 4-4 4 4"/><circle cx="8.5" cy="15.5" r="1.5"/></svg>
        <?php endif; ?>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #c0a0a0, #8b6060); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: white;">
                <?php echo strtoupper(substr($item['firstName'], 0, 1)); ?>
            </div>
            <div>
                <div style="font-size: 15px; font-weight: 700; color: #1a1a1a;"><?php echo htmlspecialchars($item['firstName'] . ' ' . $item['lastName']); ?></div>
                <div style="font-size: 13px; color: #888;">Active Campus Seller</div>
            </div>
            </div>
            <a href="report-listing.php?id=<?php echo $item['listingID']; ?>" style="font-size: 13px; color: #b3261e; font-weight: 600; text-decoration: none; padding: 6px 12px; background: #fff5f5; border-radius: 6px;">Report Issue</a>
        </div>

        <h1 style="font-size: 26px; font-weight: 800; color: #1a1a1a; margin-top: 4px;"><?php echo htmlspecialchars($item['title']); ?></h1>
        
        <div>
            <?php if($item['type'] == 'sale'): ?>
                <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background: #fbf4f4; color: #af5353; border: 1px solid #e9cfcf;">For Sale</span>
            <?php elseif($item['type'] == 'rental'): ?>
                <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background: #f1f7ff; color: #4a79d6; border: 1px solid #d0e0f3;">For Rent</span>
            <?php elseif($item['type'] == 'borrow'): ?>
                <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background: #fbf7ea; color: #9a6b2b; border: 1px solid #e6dcc5;">For Borrow</span>
            <?php endif; ?>
        </div>

        <div style="margin-top: 10px;">
        <?php if($item['type'] == 'borrow'): ?>
            <div style="font-size: 32px; font-weight: 800; color: #2e7d32;">Free</div>
        <?php else: ?>
            <div style="display: flex; align-items: baseline; gap: 6px;">
                <span style="font-size: 20px; font-weight: 700; color: #1a1a1a;">₱</span>
                <span style="font-size: 40px; font-weight: 800; color: #1a1a1a; line-height: 1;"><?php echo number_format($price, 2); ?></span>
                <?php if($item['type'] == 'rental'): ?>
                    <span style="font-size: 15px; color: #888;">/ day</span>
                <?php endif; ?>
            </div>
            <?php if($item['type'] == 'rental' && $item['deposit'] > 0): ?>
                <div style="font-size: 14px; color: #666; margin-top: 4px;">with <strong>₱<?php echo number_format($item['deposit'], 2); ?></strong> refundable deposit</div>
            <?php endif; ?>
        <?php endif; ?>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'own_item'): ?>
            <div style="color: #b3261e; background: #fff5f5; border: 1px solid #f2c0c0; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-top: 10px;">
                You cannot purchase or add your own item to the cart.
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
            <form action="checkout.php" method="POST" style="flex: 1;">
                <input type="hidden" name="direct_checkout" value="1">
                <input type="hidden" name="listing_id" value="<?php echo $item['listingID']; ?>">
                <button type="submit" style="width: 100%; padding: 14px; background: #1a1a1a; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#8B2635'" onmouseout="this.style.background='#1a1a1a'">
                    <?php 
                        if($item['type'] == 'sale') echo "Buy Now";
                        else if($item['type'] == 'rental') echo "Request Rental";
                        else echo "Request Borrow";
                    ?>
                </button>
            </form>

            <form action="add-to-cart.php" method="POST" style="flex: 1;">
                <input type="hidden" name="add_to_cart" value="1">
                <input type="hidden" name="listing_id" value="<?php echo $item['listingID']; ?>">
                <button type="submit" style="width: 100%; padding: 14px; background: #fff; color: #1a1a1a; border: 2px solid #e0dada; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#1a1a1a'" onmouseout="this.style.borderColor='#e0dada'">
                    Add to Cart
                </button>
            </form>
        </div>

        <div style="border: 1px solid #e0dada; border-radius: 10px; margin-top: 10px; background: #fafafa; padding: 16px;">
            <div style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Item Details</div>
            <div style="font-size: 14px; color: #555; line-height: 1.6;">
                <strong>Condition:</strong> <?php echo ucfirst(htmlspecialchars($item['condition'])); ?><br>
                <strong>Meetup Location:</strong> <?php echo htmlspecialchars($item['location']); ?><br>
                <?php if($item['type'] == 'rental' || $item['type'] == 'borrow'): ?>
                    <strong>Max Keep Time:</strong> <?php echo $maxDays; ?> days<br>
                <?php endif; ?>
                <br>
                <strong>Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($item['description'] ?? 'No description provided.')); ?>
            </div>
        </div>

    </div>
    </div>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>