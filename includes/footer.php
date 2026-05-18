<?php
    $isAdmin = isset($_SESSION['userID']) && isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin';
?>

<?php if ($isAdmin): ?>
    <footer class="site-footer" style="padding: 24px 60px; border-top: 4px solid #8B2635; margin-top: auto; background: #111;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img src="images/logo.png" alt="Maroon Market Logo" style="width: 28px; height: 28px; filter: grayscale(100%) brightness(200%);" />
                <span style="font-family: 'Nunito', sans-serif; font-size: 18px; font-weight: 800; color: #fff;">
                    Maroon Market <span style="color: #777; font-weight: 600; font-size: 14px; margin-left: 6px;">| Admin Console</span>
                </span>
            </div>
            
            <div style="display: flex; gap: 24px; font-size: 13px; font-weight: 600;">
                <a href="admin.php" style="color: #aaa; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#aaa'">Listings</a>
                <a href="admin-users.php" style="color: #aaa; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#aaa'">Users</a>
                <a href="admin-reports.php" style="color: #aaa; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#aaa'">Reports</a>
            </div>
            
            <div style="color: #555; font-size: 12px; font-weight: 600;">
                © <?php echo date('Y'); ?> CIT-U Maroon Market
            </div>
        </div>
    </footer>

<?php else: ?>
    <footer class="site-footer">
        <div class="footer-top">
        <div class="footer-brand">
            <div class="footer-logo-row">
            <img src="images/logo.png" alt="Maroon Market Logo" />
            <span>Maroon Market</span>
            </div>
            <p>Your campus marketplace for buying, selling, renting, and borrowing with confidence.</p>
        </div>
        <div class="footer-links">
            <h4>Marketplace</h4>
            <a href="index.php">Home</a>
            <a href="browse.php">Browse</a>
            <a href="list-item.php">List an Item</a>
        </div>
        <div class="footer-links">
            <h4>Support</h4>
            <a href="#">Help Center</a>
            <a href="#">Safety Tips</a>
            <a href="#">Contact Us</a>
        </div>
        <div class="footer-links">
            <h4>Legal</h4>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Community Rules</a>
        </div>
        </div>
        <div class="footer-bottom">
        <p>© <?php echo date('Y'); ?> Maroon Market. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>

</div> <div id="mySimpleModal" class="simple-modal-overlay">
    <div class="simple-modal-box">
        <h3 style="margin-top:0; color: #1a1a1a;">Are you sure?</h3>
        <p id="modalMessage" style="color: #555; margin-bottom: 20px;">Message goes here</p>
        <div>
            <button type="button" onclick="closeModal()" class="simple-modal-btn btn-cancel">Cancel</button>
            <a id="modalProceed" href="#" class="simple-modal-btn btn-proceed">Proceed</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="js/admin.js"></script>
</body>
</html>