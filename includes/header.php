<link rel="stylesheet" href="includes/css/header.css">
<header class="header-with-topbar">
  <nav class="navbar navbar-expand-lg navbar-boxed navbar-dark bg-transparent header-light fixed-top header-reverse-scroll">
    <div class="container-fluid nav-header-container">

      <!-- LEFT SIDE: BRAND -->
      <div class="col-6 col-lg-2 me-auto ps-lg-0">
        <a class="navbar-brand" href="./main.php">
          <img src="./includes/images/logo.jpg" alt="Gayatri Services Logo">
        </a>
      </div>

      <!-- CENTER MENU -->
      <div class="col-auto col-lg-7 menu-order px-lg-0">

        <!-- HAMBURGER BUTTON (Mobile) -->
        <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-label="Toggle navigation">
          <div class="toggler-icon">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </button>

        <!-- MENU LINKS -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav alt-font text-center text-lg-start">
            <li class="nav-item"><a href="./main.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="./about.php" class="nav-link">About</a></li>
            <li class="nav-item"><a href="./products.php" class="nav-link">Our Products</a></li>
            <li class="nav-item"><a href="./projects.php" class="nav-link">Projects</a></li>
            <li class="nav-item"><a href="./gallery.php" class="nav-link">Gallery</a></li>
            <li class="nav-item"><a href="./contact.php" class="nav-link">Contact Us</a></li>

            <!-- ⭐ LOGIN BUTTON HERE -->
            <li class="nav-item mt-4 mt-lg-2">
              <a href="./index.php" class="btn btn-warning"
                 style="padding: 6px 18px; border-radius: 6px; font-weight: 600; color: #000;">
                Login
              </a>
            </li>

          </ul>
        </div>
      </div>

    </div>
  </nav>
</header>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggler = document.querySelector(".navbar-toggler");
    const icon = toggler.querySelector(".toggler-icon");
    const menu = document.getElementById("navbarNav");

    toggler.addEventListener("click", function () {
      icon.classList.toggle("open");
    });

    menu.addEventListener("hidden.bs.collapse", function () {
      icon.classList.remove("open");
    });

    document.querySelectorAll(".navbar-nav .nav-link").forEach(link => {
      link.addEventListener("click", () => {
        const collapse = bootstrap.Collapse.getInstance(menu);
        if (collapse && menu.classList.contains("show")) {
          collapse.hide();
          icon.classList.remove("open");
        }
      });
    });
  });
</script>
