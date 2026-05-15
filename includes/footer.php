<footer style="background: #1a1a1a; color: #fff; padding: 60px 40px 20px; margin-top: 60px; position: relative; z-index: 2;">
    <div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px;">
      
      <div style="grid-column: span 2;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="width: 32px; height: 32px; background: #8B2635; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-weight: 900; font-family: 'Nunito', sans-serif; font-size: 18px;">M</span>
            </div>
            <h2 style="font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 24px; margin: 0; color: white;">Maroon Market</h2>
        </div>
        <p style="color: #bbb; line-height: 1.6; font-size: 14px; max-width: 300px;">Your campus marketplace for buying, selling, renting, and borrowing with confidence.</p>
      </div>

      <div>
        <h4 style="font-size: 16px; margin: 0 0 20px 0; font-weight: 700; color: white;">
            <?php echo $is_admin ? 'Admin Settings' : 'Marketplace'; ?>
        </h4>
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <?php if($is_admin): ?>
              <a href="admin.php?view=listings" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">Manage Listings</a>
              <a href="admin.php?view=users" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">Manage Users</a>
          <?php else: ?>
              <a href="home.php" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">Home</a>
              <a href="browse-page.php" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">Browse Items</a>
              <a href="list-item.php" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">List an Item</a>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <h4 style="font-size: 16px; margin: 0 0 20px 0; font-weight: 700; color: white;">Support</h4>
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <a href="#" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">Help Center</a>
          <a href="#" style="color: #bbb; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#bbb'">System Logs</a>
        </div>
      </div>

    </div>
    
    <div style="max-width: 1100px; margin: 0 auto; border-top: 1px solid #333; padding-top: 20px; text-align: center; color: #888; font-size: 13px;">
      © 2026 Maroon Market. All rights reserved.
    </div>
  </footer>
</body>
</html>