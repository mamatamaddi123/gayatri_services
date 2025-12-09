<?php
session_start();
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'admin') {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="crud.css"/>
</head>
<body class="admin-body">
  
  <!-- Sidebar -->
<aside class="sidebar">
  <div class="brand">
    <span class="brand-title">Gayatri Services</span>
    <span class="online-dot" title="Online"></span>
  </div>
  <ul class="menu">
    <li class="menu-item active">
      <span class="mi-icon"><a href="dashboard.php">Dashboard</a></span>
    </li>

    <!-- Masters dropdown -->
    <li class="menu-item has-dropdown">
      <a href="javascript:void(0)" class="dropdown-toggle">
        <span class="mi-icon">📚</span><span>Masters ▾</span>
      </a>
      <ul class="submenu">
        <li><a href="admin.php?page=branches">Branches</a></li>
        <li><a href="admin.php?page=brand">Brand</a></li>
        <li><a href="admin.php?page=category">Category</a></li>
        <li><a href="admin.php?page=uom">UOM</a></li>
        <li><a href="admin.php?page=items">Items</a></li>
        <li><a href="admin.php?page=customers">Customers</a></li>
        <li><a href="admin.php?page=suppliers">Suppliers</a></li>
        <li><a href="admin.php?page=account_groups">Account Groups</a></li>
        <li><a href="admin.php?page=admins">Add Admins</a></li>
      </ul>
    </li>

    <!-- Transactions -->
    <li class="menu-item has-dropdown">
      <a href="javascript:void(0)" class="dropdown-toggle">
        <span class="mi-icon">💳</span><span>Transaction ▾</span>
      </a>
      <ul class="submenu">
        <li><a href="admin.php?page=stock_in">Stock-in</a></li>
        <li><a href="admin.php?page=stock_out">Stock-out</a></li>
      </ul>
    </li>

    <li class="menu-item"><span class="mi-icon">📝</span><span>Booking</span></li>
    
    <!-- Reports dropdown -->
    <li class="menu-item has-dropdown">
      <a href="javascript:void(0)" class="dropdown-toggle">
        <span class="mi-icon">📊</span><span>Reports ▾</span>
      </a>
      <ul class="submenu">
        <li><a href="admin.php?page=stock_in_report">Stock In Report</a></li>
        <li><a href="admin.php?page=stock_out_report">Stock Out Report</a></li>
        <li><a href="admin.php?page=inventory_summary">Inventory Summary</a></li>
      </ul>
    </li>
    
    <li class="menu-item"><span class="mi-icon">🚪</span><a href="./admin/manage-products.php">Products</a></li>

    <li class="menu-item"><span class="mi-icon">🚪</span><a href="./admin/gallery_manager.php">Gallery</a></li>

    <li class="menu-item"><span class="mi-icon">🔒</span><span>Change Password</span></li>
    <li class="menu-item"><span class="mi-icon">🚪</span><a href="index.php">Logout</a></li>
  </ul>
</aside>
  <!-- Main Content -->
  <main class="content">
    <header >
      <div class="tb-left"></div>
      <!-- <div class="tb-right">
        <span class="user-icon">👤</span>
        <span class="welcome-text">Welcome <?php echo htmlspecialchars($_SESSION['userName']); ?></span>
      </div> -->
    </header>

   <!-- ✅ Alert Section -->
<?php if (isset($_SESSION['message'])): ?>
  <div id="flash-message" class="alert <?php echo isset($_SESSION['alert_type']) ? $_SESSION['alert_type'] : 'success'; ?>">
    <span class="alert-text"><?php echo $_SESSION['message']; ?></span>
    <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php 
      unset($_SESSION['message']); 
      unset($_SESSION['alert_type']);
    ?>
  </div>
<?php endif; ?>




    <!-- Dynamic content loader -->
    <div class="panel-placeholder">
      <?php
      include("db.php");
      include("crud_component.php");

      if (isset($_GET['page'])) {
          $page = $_GET['page'];
          $file = "components/$page.php";
          if (file_exists($file)) include($file);
          else echo "<p>Select a master from the sidebar.</p>";
      } else {
          // echo "<p>Welcome to Admin Dashboard! Select a master from sidebar.</p>";
      }
      ?>
    </div>
  </main>

  <!-- JS for dropdown -->
  <script>
  // Sidebar dropdown
  document.querySelectorAll(".dropdown-toggle").forEach(toggle => {
    toggle.addEventListener("click", function() {
      const parent = this.parentElement;
      parent.classList.toggle("open");
    });
  });

  // Flash message fade in/out
  window.addEventListener("DOMContentLoaded", () => {
    const msg = document.getElementById("flash-message");
    if (msg) {
      msg.classList.add("show");   // fade in
      setTimeout(() => {
        msg.classList.remove("show"); // fade out
      }, 3000); // disappears after 3 sec
    }
  });
</script>

</body>
</html>
