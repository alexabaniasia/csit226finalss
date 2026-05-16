<?php
    require_once 'includes/header.php';
    include 'readrecords.php'; // Pulls in dynamically filtered $resultset_browse
?>

<div class="page">
<div class="bg-image"></div>
<main class="main">
    <form method="GET" action="browse.php" class="content-card">

    <aside class="filter-sidebar">
        <div class="filter-title">Filters</div>
        <div style="height: 1px; background: rgba(0,0,0,0.1); margin-bottom: 20px;"></div>

        <div class="filter-group">
        <div class="filter-group-label">Listing Type</div>
        <?php $types = isset($_GET['type']) ? $_GET['type'] : []; ?>
        <label class="filter-checkbox">
            <input type="checkbox" name="type[]" value="rental" <?php if(empty($types) || in_array('rental', $types)) echo 'checked'; ?>> For Rent
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="type[]" value="borrow" <?php if(empty($types) || in_array('borrow', $types)) echo 'checked'; ?>> For Borrow
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="type[]" value="sale" <?php if(empty($types) || in_array('sale', $types)) echo 'checked'; ?>> For Sale
        </label>
        </div>

        <div class="filter-group">
        <div class="filter-group-label">Category</div>
        <?php $cats = isset($_GET['category']) ? $_GET['category'] : []; ?>
        <label class="filter-checkbox">
            <input type="checkbox" name="category[]" value="books" <?php if(in_array('books', $cats)) echo 'checked'; ?>> Books
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="category[]" value="school supplies" <?php if(in_array('school supplies', $cats)) echo 'checked'; ?>> School Supplies
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="category[]" value="gadgets" <?php if(in_array('gadgets', $cats)) echo 'checked'; ?>> Gadgets
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="category[]" value="uniforms" <?php if(in_array('uniforms', $cats)) echo 'checked'; ?>> Uniforms
        </label>
        <label class="filter-checkbox">
            <input type="checkbox" name="category[]" value="others" <?php if(in_array('others', $cats)) echo 'checked'; ?>> Others
        </label>
        </div>
        
        <button type="submit" class="primary-btn" style="margin-top: 20px;">Apply Filters</button>
        <?php if(!empty($_GET)): ?>
            <a href="browse.php" style="display: block; text-align: center; margin-top: 10px; color: #888; font-size: 13px; font-weight: 600;">Clear Filters</a>
        <?php endif; ?>
    </aside>

    <div class="right-panel">
        <div class="filter-bar">
        <div class="search-box">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#888" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/></svg>
            <input type="text" name="search" placeholder="Search listings..." autocomplete="off" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            <button type="submit" style="display: none;"></button>
        </div>
        </div>

        <div class="product-grid">
        <?php 
        if(mysqli_num_rows($resultset_browse) > 0): 
            while($row = mysqli_fetch_assoc($resultset_browse)): 
        ?>
            <div class="product-card" style="cursor: pointer;" onclick="window.location.href='view-item.php?id=<?php echo $row['listingID']; ?>';">
            <?php if($row['type'] == 'sale'): ?>
                <div class="listing-badge badge-sale">For Sale</div>
            <?php elseif($row['type'] == 'rental' || $row['type'] == 'rent'): ?>
                <div class="listing-badge badge-rent">For Rent</div>
            <?php elseif($row['type'] == 'borrow'): ?>
                <div class="listing-badge badge-borrow">For Borrow</div>
            <?php endif; ?>

            <div class="product-img-wrap" style="width: 100%; aspect-ratio: 4 / 3; background: #eae5e5; display: flex; align-items: center; justify-content: center; color: #aaa; overflow: hidden;">
                <?php if(!empty($row['imagePath'])): ?>
                    <img src="<?php echo htmlspecialchars($row['imagePath']); ?>" alt="Item Photo" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                <?php endif; ?>
            </div>
            
            <div class="product-body">
                <div class="product-name"><?php echo htmlspecialchars($row['title']); ?></div>
                <div class="product-meta">Condition: <?php echo ucfirst(htmlspecialchars($row['condition'])); ?></div>
                
                <div class="product-price-row">
                <?php if($row['type'] == 'borrow'): ?>
                    <span class="product-price" style="color: #3a8a3a;">Free</span>
                <?php else: ?>
                    <span class="product-price">₱<?php echo number_format($row['price'], 2); ?></span>
                    <?php if($row['type'] == 'rental' || $row['type'] == 'rent'): ?>
                    <span class="product-price-unit">/day</span>
                    <?php endif; ?>
                <?php endif; ?>
                </div>
            </div>
            </div>
        <?php 
            endwhile; 
        else: 
        ?>
            <p style="color: #888; font-size: 16px; margin-top: 20px;">No active listings matched your filters. Try clearing them and searching again!</p>
        <?php endif; ?>
        </div>
    </div>

    </form>
</main>
</div>

<?php require_once 'includes/footer.php'; ?>