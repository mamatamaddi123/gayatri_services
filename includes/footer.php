<footer style="background-color: #324b74; color: white; padding: 50px 0 20px; position: relative; font-family: 'Poppins', sans-serif;">
  <div class="container">

    <!-- Logo -->
    <div style="text-align: center; margin-bottom: 30px;">
      <a href="./main.php">
        <img src="./includes/images/logo.jpg" alt="Gayatri Services Logo"
             style="height: 45px; width: auto; object-fit: contain;">
      </a>
    </div>

    <!-- Footer Content -->
    <div class="row text-center text-md-start" style="row-gap: 30px;">

      <!-- Contact -->
      <div class="col-lg-3 col-md-6 col-sm-12">
        <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Contact</h5>
        <p style="font-size: 15px; margin-bottom: 10px;">
          <i class="feather icon-feather-mail" style="color: red; margin-right: 8px;"></i>
          gayatriservices@gmail.com
        </p>
        <p style="font-size: 15px;">
          <i class="feather icon-feather-phone-call" style="color: red; margin-right: 8px;"></i>
          +91 9827343693 <br>
          <span style="font-size: 13px; color: #ccc;">(Customer Care)</span>
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-3 col-md-6 col-sm-12">
        <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Quick Links</h5>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <li style="margin-bottom: 8px;"><a href="./main.php" class="footer-link">Home</a></li>
          <li style="margin-bottom: 8px;"><a href="./about.php" class="footer-link">About</a></li>
          <li style="margin-bottom: 8px;"><a href="./products.php" class="footer-link">Products</a></li>
          <li style="margin-bottom: 8px;"><a href="./projects.php" class="footer-link">Projects</a></li>
          <li style="margin-bottom: 8px;"><a href="./gallery.php" class="footer-link">Gallery</a></li>
          <li><a href="./contact.php" class="footer-link">Contact Us</a></li>
        </ul>
      </div>

      <!-- Address -->
      <div class="col-lg-3 col-md-6 col-sm-12">
        <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Address</h5>
        <p style="font-size: 15px; line-height: 1.6; margin: 0;">
          <i class="feather icon-feather-map-pin" style="color: red; margin-right: 8px;"></i>
          Akkayyapalem,<br> Vishakhapatnam,<br> Andhra Pradesh.
        </p>
      </div>

      <!-- Social -->
      <div class="col-lg-3 col-md-6 col-sm-12 text-center">
        <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">Follow Us</h5>
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
          <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-pinterest"></i></a>
        </div>
      </div>

    </div>

    <!-- Divider -->
    <div style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 40px; padding-top: 15px; text-align: center;">
      <p style="margin: 0; font-size: 13px; color: #ccc;">
        © 2025 Gayatri Services. All Rights Reserved.
      </p>
    </div>

  </div>
</footer>

<!-- Footer Styling -->
<style>
  @media (max-width: 768px) {
    footer {
      padding: 40px 0 15px;
    }
    footer h5 {
      font-size: 17px !important;
    }
    footer p, footer a {
      font-size: 14px !important;
    }
  }

  .footer-link {
    color: white;
    text-decoration: none;
    font-size: 15px;
    transition: color 0.3s ease;
  }

  .footer-link:hover {
    color: red;
  }

  .social-icon {
    background-color: red;
    color: white;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }

  .social-icon:hover {
    background-color: white;
    color: red;
    transform: translateY(-3px);
  }
</style>
