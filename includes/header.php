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