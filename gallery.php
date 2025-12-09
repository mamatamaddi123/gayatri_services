<?php
// Database connection
$conn = new mysqli("mysql5047.site4now.net", "a26f8d_gayatri", "Gayatri@2025", "db_a26f8d_gayatri");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM gallery_images ORDER BY id DESC"; // No LIMIT
$result = $conn->query($sql);
?>


<!doctype html>
<html class="no-js" lang="en" class="overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1" />
    <meta name="author" content="Gayatri Services">
    <meta name="robots" content="index, follow">
    <meta name="YahooSeeker" content="all,index, follow">
    <meta name="organization-Email" content="info@gayatriservices.com">
    <meta name="language" content="en">
    <meta name="country" content="India" />
    <meta name="coverage" content="Worldwide" />
    <meta name="distribution" content="global">
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Gayatri Services" />
    <link rel="canonical" href="./" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="preload" as="image" href="./includes/img/slider/s1.png" fetchpriority="high">
    <link rel="preload" as="image" href="./includes/img/logo/logo_dark.png" fetchpriority="high">


    <!-- Google Tag Manager -->
    
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
 

    <!-- favicon icon -->
     <link rel="icon" href="./includes/images/fav.jpg" type="image/jpeg">

    <!-- style sheets and font icons  -->
    <link rel="stylesheet" type="text/css" href="./includes/css/font-icons.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/theme-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/responsive.css" />

    <title>Gayatri Services - Premium ACP Sheets & Partition Solutions</title>

    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '584918883843475');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
			src="https://www.facebook.com/tr?id=584918883843475&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
    <meta name="description" content="Gayatri Services - Trusted provider of premium ACP sheets and partition solutions for residential, commercial, and industrial projects. Quality materials with expert installation.">
    <meta name="keywords" content="ACP Sheets, Partition Sheets, Aluminum Composite Panels, Cladding Panels, Signage Boards, Interior Partitions">
    <style>
        .bg-transparent {
            background-color: #fff !important;
        }

        nav .menu-order ul li a {
            color: #000 !important;
        }

        .navbar.navbar-dark .navbar-nav>.nav-item.dropdown.megamenu:hover>a,
        .navbar.navbar-dark .navbar-nav>.nav-item.dropdown.simple-dropdown:hover>a {
            color: #000 !important;
        }

        @media only screen and (min-width: 919px) {
            header .navbar .navbar-brand .default-logo {
                position: relative !important;
            }
        }
    </style>
    <meta name="og:title" content="Gayatri Services - Premium ACP Sheets & Partition Solutions">
    <meta name="og:description" content="Trusted provider of premium ACP sheets and partition solutions for residential, commercial, and industrial projects. Quality materials with expert installation."
    <meta name="og:url" content="./">
    <meta name="og:image:url" content=".//includes/img/slider/s7.png">

    <meta name="twitter:title" content="Gayatri Services - Premium ACP Sheets & Partition Solutions">
    <meta name="twitter:description" content="Trusted provider of premium ACP sheets and partition solutions for residential, commercial, and industrial projects. Quality materials with expert installation."
    <meta name="twitter:image" content=".//includes/img/slider/s7.png">

    <!------Head end------------->

    <!------Menu start------------->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTLCXFBB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->


<?php include './includes/header.php' ?>

  
  <title> Gallery</title>
  <style>
   

   

    .gallery-section {
      padding: 60px 5%;
      max-width: 1200px;
      margin: auto;
    }

    .gallery-title {
      text-align: center;
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 40px;
      color: #222;
      position: relative;
      margin-top: 15px;
    }

    .gallery-title::after {
      content: "";
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, #007bff, #00c6ff);
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
    }

    .gallery-item {
      position: relative;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .gallery-item:hover {
      transform: scale(1.04);
    }

    .gallery-item img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      display: block;
      border-radius: 15px;
      transition: transform 0.4s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.1);
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.4s ease;
      border-radius: 15px;
    }

    .gallery-item:hover .overlay {
      opacity: 1;
    }

    .overlay span {
      color: white;
      font-size: 1.8rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    /* Lightbox Modal */
    .lightbox {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .lightbox img {
      max-width: 90%;
      max-height: 90%;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }

    .lightbox.active {
      display: flex;
    }

    .lightbox::after {
      content: "×";
      position: absolute;
      top: 20px;
      right: 35px;
      font-size: 2.5rem;
      color: #fff;
      cursor: pointer;
      font-weight: bold;
    }

    @media (max-width: 600px) {
      .gallery-title {
        font-size: 2rem;
      }
      .gallery-item img {
        height: 200px;
      }
    }
  </style>



<section class="gallery-section">
  <h2 class="gallery-title">Our Gallery</h2>
  <div class="gallery-grid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="gallery-item" data-img="<?php echo $row['image_path']; ?>">
          <img src="../admin/<?php echo $row['image_path']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
          <div class="overlay"><span><?php echo htmlspecialchars($row['title']); ?></span></div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No images available.</p>
    <?php endif; ?>
  </div>
</section>

<div class="lightbox" id="lightbox">
  <img src="" alt="Expanded Image">
</div>


<?php include './includes/footer.php'; ?>

<script>
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = lightbox.querySelector('img');

  document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', () => {
      const imgSrc = item.getAttribute('data-img');
      lightboxImg.src = imgSrc;
      lightbox.classList.add('active');
    });
  });

  lightbox.addEventListener('click', () => {
    lightbox.classList.remove('active');
  });
</script>
</html>
