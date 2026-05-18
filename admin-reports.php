<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Maroon Market</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="page">
  <div class="bg-image"></div>

  <nav class="navbar">
    <div class="navbar-inner" style="max-width: 1200px; margin: 0 auto; position: relative;">
      
      <div class="nav-links">
        <?php if(isset($_SESSION['userID']) && strtolower($_SESSION['role']) == 'admin'): ?>
            <a href="admin.php">Listings</a>
            <a href="admin-users.php">Users</a>
            <a href="admin-reports.php">Reports</a>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="browse.php">Browse</a>
            <?php if(isset($_SESSION['userID'])): ?>
                <a href="list-item.php">List Item</a>
            <?php endif; ?>
        <?php endif; ?>
      </div>

      <div class="nav-logo">
        <a href="index.php" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
            <div class="nav-logo-icon">
              <img src="images/logo.png" alt="Logo" style="width:40px; height:40px; object-fit: contain;" />
            </div>
            <span class="nav-logo-text" style="letter-spacing: -0.5px;">MaroonMarket</span>
        </a>
      </div>

      <div class="nav-auth">
        <?php if(!isset($_SESSION['userID'])): ?>
            <div class="nav-auth-guest">
                <a href="login.php" style="color: white; text-decoration: none; font-weight: 600; padding: 8px 16px;">Login</a>
                <a href="register.php" class="nav-auth-btn">Register</a>
            </div>
        <?php else: ?>
            <div class="nav-auth-user">
              <?php if(strtolower($_SESSION['role']) != 'admin'): ?>
                  <a href="cart.php" class="nav-icon-btn" aria-label="Cart" title="Cart">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                  </a>
              <?php endif; ?>
              
              <a href="profile.php" class="nav-icon-btn" aria-label="Profile" title="Profile">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </a>
              
              <a href="logout.php" class="nav-icon-btn" aria-label="Logout" title="Logout">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
              </a>
            </div>
        <?php endif; ?>
      </div>

    </div>
  </nav>

<?php
    require_once 'includes/header.php';
    include 'connect.php';

    if(!isset($_SESSION['userID']) || strtolower($_SESSION['role']) != 'admin'){
        header("Location: index.php");
        exit();
    }

    $msg = "";

    if(isset($_POST['btnAction'])){
        $reportID = intval($_POST['report_id']);
        $action = $_POST['btnAction'];
        $note = mysqli_real_escape_string($connection, $_POST['moderatorNote']);
        $now = date('Y-m-d H:i:s');

        if($action == 'dismiss'){
            mysqli_query($connection, "UPDATE listing_reports SET status = 'dismissed', moderatorNote = '$note', resolvedAt = '$now' WHERE reportID = '$reportID'");
            $msg = "Report dismissed successfully.";
        }
        else if($action == 'delete_listing'){
            $listingID = intval($_POST['listing_id']);
            
            mysqli_query($connection, "UPDATE listings SET listingStatus = 'deleted' WHERE listingID = '$listingID'");
            mysqli_query($connection, "UPDATE listing_reports SET status = 'resolved', moderatorNote = '$note', resolvedAt = '$now' WHERE reportID = '$reportID'");
            $msg = "Offending listing has been removed and report resolved.";
        }
    }

    $reports_query = mysqli_query($connection, "
        SELECT r.*, l.listingStatus, i.name as itemName, u.firstName, u.lastName 
        FROM listing_reports r
        JOIN listings l ON r.listingID = l.listingID
        JOIN items i ON l.itemID = i.itemID
        JOIN users u ON r.reporterID = u.userID
        WHERE r.status = 'open'
        ORDER BY r.createdAt DESC
    ");
?>

<div class="page">
<div class="bg-image"></div>
<div style="position: relative; z-index: 10; max-width: 1100px; margin: 0 auto; padding: 40px 18px 60px;">
    
    <div style="margin-bottom: 24px;">
    <h1 style="margin:0; font-family: 'Nunito', sans-serif; font-weight:800; font-size: 32px; color: #1a1a1a;">Reported Listings</h1>
    <p style="color: #555;">Review items flagged by the community for safety or fraud violations.</p>
    </div>

    <?php if($msg != ""): ?>
        <div style="background: #f2fbf5; color: #1f7a34; border: 1px solid #b6ddc3; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: rgba(255,255,255,.95); border: 1px solid rgba(0,0,0,.08); border-radius: 16px; padding: 24px;">
    
    <?php if(mysqli_num_rows($reports_query) == 0): ?>
        <div style="text-align: center; padding: 60px 20px; color: #666; background: #fafafa; border-radius: 12px; border: 1px dashed #ccc;">
            <svg width="48" height="48" fill="none" stroke="#aaa" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <h3 style="font-size: 18px; color: #1a1a1a; margin-bottom: 8px;">All Clear!</h3>
            <p style="font-size: 14px;">There are no active reports to review. The community is safe.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 16px;">
            <?php while($report = mysqli_fetch_assoc($reports_query)): ?>
            <div style="border: 1px solid #f2c0c0; border-radius: 12px; padding: 16px; background: #fffaf9;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    
                    <div style="flex: 1;">
                        <h4 style="color: #b3261e; font-size: 16px; font-weight: 800; margin-bottom: 4px;">Reason: <?php echo htmlspecialchars($report['reason']); ?></h4>
                        <p style="font-size: 14px; color: #1a1a1a; margin-bottom: 8px;"><strong>Item:</strong> <?php echo htmlspecialchars($report['itemName']); ?></p>
                        <p style="font-size: 13px; color: #555; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                            "<?php echo nl2br(htmlspecialchars($report['details'])); ?>"
                        </p>
                        <p style="font-size: 12px; color: #888; margin-top: 8px;">Reported by: <?php echo htmlspecialchars($report['firstName'] . ' ' . $report['lastName']); ?> on <?php echo date('M d, Y', strtotime($report['createdAt'])); ?></p>
                    </div>
                    
                    <form method="post" action="admin-reports.php" style="display: flex; flex-direction: column; gap: 8px; min-width: 280px; background: #fff; padding: 12px; border-radius: 8px; border: 1px solid #e0dada;">
                        <input type="hidden" name="report_id" value="<?php echo $report['reportID']; ?>">
                        <input type="hidden" name="listing_id" value="<?php echo $report['listingID']; ?>">
                        
                        <label style="font-size: 12px; font-weight: 700; color: #1a1a1a;">Moderator Note:</label>
                        <textarea name="moderatorNote" placeholder="Explain your action (optional)..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 12px; resize: none; font-family: inherit; height: 60px;"></textarea>
                        
                        <div style="display: flex; gap: 6px; margin-top: 4px;">
                            <a href="view-item.php?id=<?php echo $report['listingID']; ?>" target="_blank" style="flex: 1; display: flex; align-items: center; justify-content: center; background: #1a1a1a; color: #fff; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">View Item</a>
                            <button type="submit" name="btnAction" value="delete_listing" style="flex: 1; background: #fce8e6; color: #c5221f; border: 1px solid #f2c0c0; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;" onclick="showFormModal('Delete this listing permanently?', this, event);">Delete Listing</button>
                            <button type="submit" name="btnAction" value="dismiss" style="flex: 1; background: #fff; color: #666; border: 1px solid #ccc; padding: 6px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Dismiss</button>
                        </div>
                    </form>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    </div>
</div>
</div>
<?php require_once 'includes/footer.php'; ?>