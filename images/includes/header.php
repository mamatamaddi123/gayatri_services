<header class="header-with-topbar">
  <nav class="navbar navbar-expand-lg navbar-boxed navbar-dark bg-transparent header-light fixed-top header-reverse-scroll" style="transition: all 0.3s ease;">
    <div class="container-fluid nav-header-container">

      <!-- LEFT SIDE: BRAND -->
      <div class="col-6 col-lg-2 me-auto ps-lg-0">
        <a class="navbar-brand" href="./main.php"
           style="color: red !important; font-size: 28px; font-weight: 700; line-height: 1.1;">
          GAYATRI <br> SERVICES
        </a>
      </div>

      <!-- CENTER MENU -->
      <div class="col-auto col-lg-7 menu-order px-lg-0">
        <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation" style="border: none; background: none;">
          <span style="display:block;width:25px;height:2px;background:white;margin:5px 0;"></span>
          <span style="display:block;width:25px;height:2px;background:white;margin:5px 0;"></span>
          <span style="display:block;width:25px;height:2px;background:white;margin:5px 0;"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav alt-font">

            <li class="nav-item">
              <a href="./main.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'index.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'blue !important' : 'black'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'blue !important' : 'green'; ?>'">
                Home
              </a>
            </li>

            <li class="nav-item">
              <a href="./about.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'about.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'blue !important' : 'black'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'blue !important' : 'black'; ?>'">
                About
              </a>
            </li>

            <li class="nav-item">
              <a href="./products.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'products.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'blue !important' : 'black'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'blue !important' : 'black'; ?>'">
                Products
              </a>
            </li>

            <li class="nav-item">
              <a href="./projects.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'projects.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'blue !important' : 'black'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'blue !important' : 'black'; ?>'">
                Projects
              </a>
            </li>

            <li class="nav-item">
              <a href="./gallery.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'gallery.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'blue !important' : 'black'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'blue !important' : 'black'; ?>'">
                Gallery
              </a>
            </li>

            <li class="nav-item d-block d-sm-none">
              <a href="./catalogue"
                 class="nav-link"
                 style="color:white; font-weight:500; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='white'">
                E-Catalogue
              </a>
            </li>

            <li class="nav-item">
              <a href="./contact.php"
                 class="nav-link <?php if(basename($_SERVER['PHP_SELF']) == 'contact.php'){echo 'active';} ?>"
                 style="color: <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'blue !important' : 'white'; ?>;
                        font-weight:500; position:relative; transition: all 0.3s ease; text-decoration:none;"
                 onmouseover="this.style.color='red !important'"
                 onmouseout="this.style.color='<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'blue !important' : 'white'; ?>'">
                Contact Us
              </a>
            </li>

          </ul>
        </div>
      </div>

      <!-- RIGHT SIDE BUTTON -->
      <div class="col-auto col-lg-3 d-flex justify-content-end">
        <button class="btn btn_catalogue d-none d-sm-block alt-font justify-content-end"
                style="background-color:red; border:none; padding:10px 20px; border-radius:5px; transition: all 0.3s ease;">
          <a class="text-white" href="./contact.php"
             style="color:white !important; text-decoration:none; font-weight:600;"
             onmouseover="this.parentElement.style.backgroundColor='blue'"
             onmouseout="this.parentElement.style.backgroundColor='red'">
            <span>Get Start</span>
          </a>
        </button>
      </div>

    </div>
  </nav>
</header>