<footer class="site-footer">
    <div class="footer-top">
      <div class="footer-brand">
        <div class="footer-logo-row">
          <img src="images/logo.png" alt="Maroon Mart Logo" />
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

</div> <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('#adminTable').length) {
            $('#adminTable').DataTable();
        }
    });
</script>
</body>
</html>