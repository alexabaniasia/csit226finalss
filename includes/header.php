<?php 
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  $is_admin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Maroon Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  
  <style>
    .navbar { background: #8B2635; padding: 15px 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
    
    .nav-brand-col { flex: 1; display: flex; justify-content: flex-start; }
    .nav-links-col { flex: 1; display: flex; justify-content: center; gap: 30px; }
    .nav-actions-col { flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 16px; }

    .nav-brand span { font-family: 'Nunito', sans-serif; font-size: 24px; font-weight: 800; color: white; text-decoration: none; }
    .nav-links-col a { color: rgba(255,255,255,0.85); text-decoration: none; font-weight: 600; font-size: 15px; transition: color 0.2s; }
    .nav-links-col a:hover { color: white; }
    
    .btn-outline { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
    .btn-outline:hover { background: rgba(255,255,255,0.1); }
    .btn-solid { background: white; color: #8B2635; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; transition: transform 0.2s; }
    .btn-solid:hover { transform: translateY(-2px); }
    
    .icon-link { color: white; display: flex; align-items: center; transition: transform 0.2s; }
    .icon-link:hover { transform: scale(1.1); }
    .icon-link svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  </style>
</head>
<body>
  
  <div class="bg-image"></div>

  <nav class="navbar">
    <div class="navbar-inner">
      
      <div class="nav-brand-col">
        <a href="<?php echo $is_admin ? 'admin.php' : 'home.php'; ?>" class="nav-brand" style="text-decoration:none;">
          <span>Maroon Market</span>
        </a>
      </div>

      <div class="nav-links-col">
        <?php if($is_admin): ?>
            <a href="admin.php?view=listings">Manage Listings</a>
            <a href="admin.php?view=users">Manage Users</a>
        <?php else: ?>
            <a href="home.php">Home</a>
            <a href="browse-page.php">Browse</a>
            <a href="list-item.php">List Item</a>
        <?php endif; ?>
      </div>

      <div class="nav-actions-col">
        <?php if(!isset($_SESSION['userID'])): ?>
          <a href="login.php" class="btn-outline">Login</a>
          <a href="register.php" class="btn-solid">Register</a>
        <?php else: ?>
          <span style="color: white; font-weight: 600; font-size: 14px; margin-right: 8px;">Hi, <?php echo $_SESSION['firstname']; ?>!</span>
          
          <?php if(!$is_admin): ?>
            <a href="profile.php" class="icon-link" title="Profile"><svg><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></a>
            <a href="cart.php" class="icon-link" title="Cart"><svg><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></a>
          <?php endif; ?>
          
          <a href="logout.php" class="icon-link" title="Sign Out"><svg><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></a>
        <?php endif; ?>
      </div>

    </div>
  </nav>