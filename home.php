<?php 
    $custom_css = 'style.css'; 
    include 'connect.php'; 
    require_once 'includes/header.php'; 
?>

<style>
  .hero-section {
    position: relative;
    background: linear-gradient(rgba(139, 38, 53, 0.85), rgba(70, 15, 25, 0.95)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2000&auto=format&fit=crop') center/cover;
    color: white;
    padding: 120px 20px;
    text-align: center;
    margin-bottom: 70px;
  }
  .hero-content h1 { font-size: 52px; margin-bottom: 20px; font-family: 'Nunito', sans-serif; font-weight: 800; line-height: 1.1; }
  .hero-content p { font-size: 20px; max-width: 650px; margin: 0 auto 35px; opacity: 0.9; line-height: 1.5; }
  
  .section-wrapper { max-width: 1100px; margin: 0 auto 80px; padding: 0 20px; }
  .section-header { text-align: center; margin-bottom: 45px; }
  .section-header h2 { font-size: 34px; color: #8B2635; margin-bottom: 12px; font-family: 'Nunito', sans-serif; font-weight: 800; }
  .section-header p { color: #666; font-size: 17px; max-width: 600px; margin: 0 auto; line-height: 1.5; }

  .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }

  /* Feature/Benefit Boxes */
  .benefit-box { padding: 35px; background: #fff; border-top: 4px solid #8B2635; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
  .benefit-box h3 { font-size: 20px; margin-bottom: 12px; color: #1a1a1a; }
  .benefit-box p { color: #666; line-height: 1.6; margin: 0; }
  
</style>

<main class="main">
  
  <section class="hero-section">
    <div class="hero-content">
      <h1 style="color: white;">Everything You Need,<br>From People Around You</h1>
      <p>Buy, sell, rent, or borrow items easily within the CIT-U campus. Safe, local, and student-first.</p>
      <a href="browse-page.php" class="maroon-btn" style="background: white; color: #8B2635 !important; padding: 16px 36px; font-size: 18px; border-radius: 50px;">Browse Listings</a>
    </div>
  </section>

  <section class="section-wrapper">
    <div class="section-header">
      <h2>How It Works</h2>
      <p>Follow these simple steps to start buying, selling, renting, or borrowing items on campus.</p>
    </div>
    <div class="grid-3 text-center">
      <div class="feature-card" style="padding: 40px 20px; text-align: center;">
        <img src="https://c.animaapp.com/1cGq5ZFm/img/undraw-web-search-7oif-1.svg" alt="Browse" style="width: 120px; height: 120px; margin-bottom: 25px;">
        <h3 style="color: #8B2635; margin-bottom: 12px; font-size: 20px;">1. Browse Items</h3>
        <p style="color: #666; line-height: 1.6; font-size: 15px;">Quickly find items by keyword that match what you need for the semester.</p>
      </div>
      <div class="feature-card" style="padding: 40px 20px; text-align: center;">
        <img src="https://c.animaapp.com/1cGq5ZFm/img/undraw-contact-us-kcoa-1.svg" alt="Contact" style="width: 120px; height: 120px; margin-bottom: 25px;">
        <h3 style="color: #8B2635; margin-bottom: 12px; font-size: 20px;">2. Contact Seller</h3>
        <p style="color: #666; line-height: 1.6; font-size: 15px;">Easily reach out to the person offering the item for details, negotiations, or arrangements.</p>
      </div>
      <div class="feature-card" style="padding: 40px 20px; text-align: center;">
        <img src="https://c.animaapp.com/1cGq5ZFm/img/undraw-online-shopping-hgf6-1.svg" alt="Buy" style="width: 120px; height: 120px; margin-bottom: 25px;">
        <h3 style="color: #8B2635; margin-bottom: 12px; font-size: 20px;">3. Complete Exchange</h3>
        <p style="color: #666; line-height: 1.6; font-size: 15px;">Complete the transaction safely on campus and receive the item according to the listing type.</p>
      </div>
    </div>
  </section>

  <section class="section-wrapper">
    <div class="section-header">
      <h2>Why Choose Maroon Market?</h2>
      <p>Built for students, by students. Discover what makes our campus marketplace the smartest way to get what you need.</p>
    </div>
    <div class="grid-3">
      <div class="benefit-box">
        <h3>Secure Transactions</h3>
        <p>Get safer exchanges through clear listing details, transparent pricing, and verified campus users.</p>
      </div>
      <div class="benefit-box">
        <h3>Flexible Options</h3>
        <p>Buy permanently, rent for short-term needs, or borrow items to save more every semester.</p>
      </div>
      <div class="benefit-box">
        <h3>Community Driven</h3>
        <p>Support fellow students and reduce waste by keeping useful items in circulation around campus.</p>
      </div>
    </div>
  </section>

</main>

<?php require_once 'includes/footer.php'; ?>