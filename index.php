<?php
    require_once 'includes/header.php';
    include 'readrecords.php'; 
?>

<section class="hero" style="position: relative; height: 640px; display: flex; flex-direction: column; align-items: center; overflow: hidden;">
<div class="hero-bg" style="position: absolute; inset: 0; background: url('https://cit.edu/wp-content/uploads/2023/07/GLE-Building.jpg') center/cover no-repeat; background-attachment: fixed;"></div>

<div class="hero-tint" style="position: absolute; inset: 0; background: rgba(40, 15, 20, 0.6); mix-blend-mode: multiply;"></div>

<div class="hero-content" style="position: relative; z-index: 10; text-align: center; margin-top: 140px; color: #fff; max-width: 800px; padding: 0 20px;">
    <h1 class="hero-title" style="font-family: 'Nunito', sans-serif; font-size: 56px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; text-shadow: 0 4px 12px rgba(0,0,0,0.3);">Everything You Need,<br>From People Around You</h1>
    <p class="hero-subtitle" style="font-size: 20px; opacity: 0.9; margin-bottom: 40px; font-weight: 500;">Buy, sell, rent, or borrow items easily within the CIT-U campus</p>
    <a href="browse.php" class="hero-btn" style="display: inline-block; background: #fff; color: #8B2635; padding: 16px 36px; border-radius: 12px; font-weight: 800; font-size: 16px; transition: 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">BROWSE LISTINGS</a>
</div>
</section>

<div class="page" style="padding-top: 40px; min-height: auto; background: transparent;">
<div class="main" style="padding-top: 0; position: relative; z-index: 2;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; background: rgba(255, 255, 255, 0.9); padding: 20px 28px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); backdrop-filter: blur(4px);">
    <div>
        <h2 style="font-family: 'Nunito', sans-serif; font-size: 32px; font-weight: 800; color: #1a1a1a; margin: 0;">Featured Listings</h2>
        <p style="color: #555; margin-top: 6px; font-size: 15px; font-weight: 500;">Discover items recently posted by the CIT-U community</p>
    </div>
    <a href="browse.php" style="color: #8B2635; font-weight: 700; font-size: 15px; background: #fff; padding: 10px 18px; border-radius: 8px; border: 1px solid rgba(139,38,53,0.2); transition: 0.2s;">Explore all &rarr;</a>
    </div>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
    <?php 
    if (mysqli_num_rows($resultset_featured) > 0):
        while($row = mysqli_fetch_assoc($resultset_featured)): 
    ?>
        <div class="product-card" onclick="window.location.href='view-item.php?id=<?php echo $row['listingID']; ?>';" style="background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.2s; position: relative; cursor: pointer;">
        
        <?php if($row['type'] == 'sale'): ?>
            <div class="listing-badge badge-sale" style="position: absolute; top: 12px; left: 12px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; z-index: 2; background: rgba(251, 244, 244, 0.95); color: #af5353; border: 1px solid #e9cfcf;">For Sale</div>
        <?php elseif($row['type'] == 'rent'): ?>
            <div class="listing-badge badge-rent" style="position: absolute; top: 12px; left: 12px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; z-index: 2; background: rgba(241, 247, 255, 0.95); color: #4a79d6; border: 1px solid #d0e0f3;">For Rent</div>
        <?php elseif($row['type'] == 'borrow'): ?>
            <div class="listing-badge badge-borrow" style="position: absolute; top: 12px; left: 12px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; z-index: 2; background: rgba(251, 247, 234, 0.95); color: #9a6b2b; border: 1px solid #e6dcc5;">For Borrow</div>
        <?php endif; ?>

        <div class="product-img-wrap" style="width: 100%; aspect-ratio: 4 / 3; background: #eae5e5; display: flex; align-items: center; justify-content: center; color: #aaa; overflow: hidden;">
            <?php if(!empty($row['imagePath'])): ?>
                <img src="<?php echo htmlspecialchars($row['imagePath']); ?>" alt="Item Photo" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            <?php endif; ?>
        </div>
        
        <div class="product-body" style="padding: 18px; display: flex; flex-direction: column; gap: 4px;">
            <div class="product-name" style="font-size: 17px; font-weight: 700; color: #1a1a1a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($row['title']); ?></div>
            <div class="product-meta" style="font-size: 13px; color: #666; font-weight: 500;">Condition: <span style="color: #222; font-weight: 600;"><?php echo htmlspecialchars($row['condition']); ?></span></div>
            
            <?php if($row['type'] == 'rent' || $row['type'] == 'borrow'): ?>
                <div class="product-meta" style="font-size: 13px; color: #666;">Max Term: <span style="color: #222; font-weight: 600;"><?php echo $row['maxDays']; ?> days</span></div>
            <?php endif; ?>

            <div class="product-price-row" style="display: flex; align-items: baseline; gap: 4px; margin-top: 10px;">
            <?php if($row['type'] == 'borrow'): ?>
                <span class="product-price" style="font-size: 19px; font-weight: 800; color: #2e7d32;">Free</span>
            <?php else: ?>
                <span class="product-price" style="font-size: 19px; font-weight: 800; color: #8B2635;">₱<?php echo number_format($row['price'], 2); ?></span>
                <?php if($row['type'] == 'rent'): ?>
                <span class="product-price-unit" style="font-size: 13px; color: #666; font-weight: 400;">/day</span>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            
            <div style="height: 1px; background: #eee; margin: 12px 0 6px;"></div>
            <div class="product-meta" style="font-size: 12px; color: #777; display: flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            Seller: <strong style="color: #444; font-weight: 600;"><?php echo htmlspecialchars($row['firstName']); ?></strong>
            </div>
        </div>
        </div>
    <?php 
        endwhile; 
    else:
    ?>
        <div style="grid-column: 1/-1; background: #fff; padding: 40px; text-align: center; border-radius: 16px; color: #666; font-weight: 500;">No featured listings available right now.</div>
    <?php endif; ?>
    </div>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>