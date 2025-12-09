<!------Head start------------->
<?php
// Include database connection
require_once 'db.php';

// Fetch items from database
$query = "SELECT item_Code, item_Name, image_path, price FROM items WHERE stat = 'active' ORDER BY item_Id LIMIT 12";
$result = mysqli_query($conn, $query);
$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}
?>

<!-- Display the products -->



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
       <!-- style sheets and font icons  -->
    <link rel="stylesheet" type="text/css" href="./includes/css/font-icons.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/theme-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/responsive.css" />

    <title>Gayatri Services - Premium ACP Sheets & Partition Solutions</title>

    <!-- Meta Pixel Code -->
   
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
 <link rel="icon" href="./includes/images/fav.jpg" type="image/jpeg">


    <!------Head end------------->

    <!------Menu start------------->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
  
    <!-- End Google Tag Manager (noscript) -->


<?php include './includes/header.php' ?>


    <!------Menu end------------->

 <section class="p-0">
  <div class="container-fluid position-relative">
    <div class="row">
      <div
        class="swiper-container white-move p-0"
        style="width: 100%; overflow: hidden;"
        data-slider-options='{
          "slidesPerView": 1,
          "loop": true,
          "autoplay": { "delay": 3000, "disableOnInteraction": false },
          "navigation": { "nextEl": ".swiper-button-next-nav", "prevEl": ".swiper-button-previous-nav" },
          "keyboard": { "enabled": true, "onlyInViewport": true },
          "effect": "slide"
        }'
      >
        <div class="swiper-wrapper">

          <!-- Slide 1 -->
          <div class="swiper-slide cover-background">
            <picture>
              <source srcset="./includes/img/slider/s1-mobile.png" media="(max-width: 767px)">
              <img src="./includes/img/slider/s1.png" alt="Red ACP sheet project" fetchpriority="high" decoding="async" />
            </picture>
          </div>

          <!-- Slide 2 -->
          <div class="swiper-slide cover-background">
            <picture>
              <source srcset="./includes/img/slider/s2-mobile.png" media="(max-width: 767px)">
              <img loading="lazy" src="./includes/img/slider/s2.png" alt="Brown ACP sheet commercial building" decoding="async" />
            </picture>
          </div>

          <!-- Slide 3 -->
          <div class="swiper-slide cover-background">
            <picture>
              <source srcset="./includes/img/slider/s7-mobile.png" media="(max-width: 767px)">
              <img loading="lazy" src="./includes/img/slider/s7.png" alt="Commercial building ACP sheet" decoding="async" />
            </picture>
          </div>

          <!-- Slide 4 -->
          <div class="swiper-slide cover-background">
            <picture>
              <source srcset="./includes/img/slider/s8-mobile.png" media="(max-width: 767px)">
              <img loading="lazy" src="./includes/img/slider/s8.png" alt="ACP sheet grey colour" decoding="async" />
            </picture>
          </div>

          <!-- Slide 5 -->
          <div class="swiper-slide cover-background">
            <picture>
              <source srcset="./includes/img/slider/s9-mobile.png" media="(max-width: 767px)">
              <img loading="lazy" src="./includes/img/slider/s9.png" alt="Grey ACP sheet" decoding="async" />
            </picture>
          </div>

        </div>

        <!-- Navigation arrows -->
        <div class="swiper-button-next-nav swiper-button-next rounded-circle slider-navigation-style-07 d-none d-sm-flex">
          <i class="feather icon-feather-arrow-right"></i>
        </div>
        <div class="swiper-button-previous-nav swiper-button-prev rounded-circle slider-navigation-style-07 d-none d-sm-flex">
          <i class="feather icon-feather-arrow-left"></i>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ✅ Styling -->
<style>
.swiper-container {
  width: 100%;
  height: auto;
}

.swiper-slide {
  width: 100%;
  height: 450px; /* Default for large screens */
}

.swiper-slide picture,
.swiper-slide img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

/* 📱 Mobile view height */
@media (max-width: 767px) {
  .swiper-slide {
    height: 250px !important;
  }
}
</style>

<section style="background-color: #f6f7f8; padding: 80px 0; width: 100%;">
  <div class="container-fluid" style="max-width: 90%; margin: 0 auto;">
    <div class="row align-items-center">
      
      <!-- LEFT SIDE: TEXT -->
      <div class="col-lg-6 col-md-12" style="text-align: left;">
        <h2 style="font-size: 42px; font-weight: 800; margin-bottom: 25px; color: #000;">
          Welcome to <span style="color: #c93434;">Gayatri Services</span>
        </h2>
        <p style="font-size: 18px; line-height: 1.8; margin-bottom: 25px; color: #333;">
          At <strong>Gayatri Services</strong>, we are dedicated to providing high-quality 
          interior and exterior solutions that blend innovation, elegance, and durability.  
          With years of experience and a commitment to excellence, we help you transform 
          your spaces into inspiring environments that reflect your vision and style.
        </p>
        <p style="font-size: 17px; line-height: 1.7; color: #555;">
          Our expert team focuses on quality craftsmanship, customer satisfaction, 
          and sustainable practices — ensuring that every project stands the test of time.
        </p>
      </div>

      <!-- RIGHT SIDE: IMAGE -->
      <div class="col-lg-6 col-md-12 text-center mt-4 mt-lg-0">
        <div style="display: inline-block; overflow: hidden; border-radius: 15px; perspective: 1000px;">
          <img src="https://www.maplacp.com/wp-content/uploads/2024/02/aluminum-composite-panel-sheet.webp" 
               alt="Interior Design"
               class="hover-image"
               style="width: 95%; height: auto; max-width: 900px; border-radius: 15px; 
                      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
                      transition: transform 0.8s ease-in-out, box-shadow 0.8s ease;">
        </div>
      </div>

    </div>
  </div>

  <!-- HOVER EFFECT STYLING -->
  <style>
    .hover-image:hover {
      transform: rotateY(40deg); /* Rotate right 90 degrees */
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
      cursor: pointer;
    }
  </style>
</section>


<style>
/* --- Product Grid Styling --- */
.product-image-box {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: all 0.4s ease;
  margin-bottom: 30px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
}

.product-image-box:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* --- Product Image --- */
.shop-product-image {
  position: relative;
  overflow: hidden;
  height: 320px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f8f8;
}

.shop-product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-image-box:hover img {
  transform: scale(1.08);
}

/* --- Product Content --- */
.shop-product-content {
  padding: 20px;
  text-align: center;
  flex-grow: 1;
}

.product-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
  margin-bottom: 8px;
}

.product-price {
  font-size: 0.95rem;
  color: #666;
  margin-bottom: 6px;
}

.product-price:last-of-type {
  font-weight: bold;
  font-size: 1.1rem;
  color: #007bff;
}

/* --- Button Styling --- */
.btn-theme {
  background: linear-gradient(90deg, #007bff, #00b4d8);
  color: #fff;
  border: none;
  padding: 10px 10px;
  border-radius: 50px;
  font-weight: 100;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,123,255,0.25);
  display: inline-block;
}

.btn-theme:hover {
  background: linear-gradient(90deg, #0056b3, #0096c7);
  box-shadow: 0 6px 18px rgba(0,123,255,0.35);
  transform: translateY(-2px);
}

/* Responsive Fix */
@media (max-width: 767px) {
  .shop-product-image {
    height: 250px;
  }
}
</style>


<div class="container py-5">
  <div class="row">
    <?php 
    $defaultImage = '../includes/img/products/default.png';

    foreach ($items as $item): 
      $imagePath = (!empty($item['image_path']) && file_exists($item['image_path'])) 
        ? $item['image_path'] 
        : $defaultImage;
    ?>
      <div class="col-12 col-md-6 col-lg-4 d-flex">
        <div class="product-image-box w-100">
          <div class="shop-product-image">
            <a href="./products.php" class="product-view-link">
              <img 
                src="<?php echo htmlspecialchars($imagePath); ?>" 
                alt="<?php echo htmlspecialchars($item['item_Name']); ?>" 
                class="img-fluid"
                onerror="this.onerror=null;this.src='<?php echo $defaultImage; ?>';"
              >
            </a>
          </div>
          <div class="shop-product-content">
            <h4 class="product-title"><?php echo htmlspecialchars($item['item_Name']); ?></h4>
            <p class="product-price">Item Code: <?php echo htmlspecialchars($item['item_Code']); ?></p>
            <p class="product-price">₹<?php echo number_format($item['price'], 2); ?></p>
            <a href="./contact.php" class="btn-theme mt-2">Contact Us</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- View More Button -->
  <div class="text-center mt-4">
    <a href="./products.php" class="btn btn-theme btn-sm">View More</a>
  </div>
</div>






    <!-- About start section -->
   <section class="big-section overflow-visible position-relative z-index-1 wow animate__fadeIn" style="background-color: #f6f7f8; padding: 80px 0;">
  <div class="container">
    <div class="row align-items-center">

      <!-- LEFT SIDE: IMAGE -->
      <div class="col-12 col-lg-6 col-md-6 text-center mb-4 mb-lg-0 wow animate__fadeInLeft" data-wow-delay="0.3s">
        <div class="position-relative overflow-hidden rounded-3 shadow-lg" style="transition: transform 0.5s ease;">
          <img 
            src="https://www.maplacp.com/wp-content/uploads/2024/02/acp-sheets.webp" 
            alt="Premium ACP Sheets"
            class="img-fluid rounded-3 hover-rotate"
            style="width: 100%; max-height: 420px; object-fit: cover;"
          />
        </div>
      </div>

      <!-- RIGHT SIDE: TEXT -->
      <div class="col-12 col-lg-6 col-md-6 wow animate__fadeInRight" data-wow-delay="0.5s">
        <h1 class="alt-font main_heading_home text-uppercase text-extra-dark-gray font-weight-700 mb-4" style="font-size: 32px; line-height: 1.3;">
          Premium ACP Sheets & Partition Solutions Provider
        </h1>
        <div class="alt-font text-uppercase text-extra-medium font-weight-600 text-dark mb-3">
          Quality & Innovation 2025
        </div>
        <p class="text-justify text-muted" style="font-size: 16px; line-height: 1.8;">
          Gayatri Services is a trusted name in providing premium-quality ACP sheets and partition sheet solutions 
          for residential, commercial, and industrial projects. With a strong commitment to quality, innovation, 
          and customer satisfaction, we deliver materials that combine strength, style, and durability to enhance every space.
        </p>
      </div>

    </div>
  </div>
</section>

<!-- HOVER EFFECT CSS -->
<style>
  .hover-rotate:hover {
    transform: rotate(2deg);
    transition: transform 0.6s ease;
  }
</style>

    <!-- About end section -->

    <!-- About-2 start section -->
    <section class="bg-medium-gray position-relative padding-eight-top padding-three-bottom lg-padding-nine-top">
        <div class="container">
            <div class="row justify-content-lg-center">
                <div class="col-12 overflow-hidden alt-font font-weight-600 text-white text-overlap-style-02 d-none d-xl-block wow animate__fadeInDown" data-wow-delay="0.2s">Gayatri Services</div>
            </div>
        </div>
    </section>
    <!-- About-2 end section -->

    <div class="box-layout">
        <!-- Collection start section -->
        <section class="overflow-visible wow animate__fadeIn collection_slider" data-wow-delay="0.8s">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                        <h2 class="alt-font title-large-2 text-theme font-weight-700 text-uppercase mb-0">Our Products</h2>
                        <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">Premium ACP Sheets & Partition Solutions</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 px-lg-0 wow animate__fadeIn" data-wow-delay="0.2s">
                        <div class="swiper-container portfolio-classic position-relative" data-slider-options='{ "slidesPerView": 1, "spaceBetween": 30, "navigation": { "nextEl": ".swiper-button-next-nav", "prevEl": ".swiper-button-previous-nav" }, "autoplay": { "delay": 3000, "disableOnInteraction": false }, "keyboard": { "enabled": true, "onlyInViewport": true }, "breakpoints": { "1200": { "slidesPerView": 4 }, "992": { "slidesPerView": 3 }, "768": { "slidesPerView": 2 } }, "effect": "slide" }'>
                            <div class="swiper-wrapper">

                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./products.php"><img class="lazy-img"
                                                data-src="./includes/img/series/wood.png" alt="wooden acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Experience the timeless elegance of wood without the maintenance hassles, offering a perfect blend of aesthetics and durability.
                                                    </p>
                                                    <div class="portfolio-icon">
                                                        <a href="./products.php" class="rounded-circle bg-dark">
                                                        <i class="ti-arrow-right text-white"></i>
                                                    </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./products.php" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Wood
                                            Grain series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./products.php"><img class="lazy-img"
                                                data-src="./includes/img/series/marble.png" alt="marble acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Elevate your space with the luxurious appearance of marble, combining sophistication with easy-to-maintain surfaces for enduring beauty.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./products.php" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./products.php" class=" alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Marble
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./products.php"><img class="lazy-img"
                                                data-src="./includes/img/series/galexy.png" alt="galaxy acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Bring the wonder of the cosmos into your designs, with captivating patterns that withstand the test of time, adding a touch of celestial charm to any setting.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./products.php" class="rounded-circle bg-dark "><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./products.php" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Galaxy
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./products.php"><img 
                                                data-src="./includes/img/series/mirror.png" class="lazy-img"
                                                alt="mirror acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Reflect style and sophistication with mirror-finish panels that enhance the illusion of space, creating visually stunning interiors with a sleek, modern flair.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./products.php" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./products.php" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Mirror
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./products.php">
                                            <img data-src="./includes/img/series/brush.png" class="lazy-img" alt="brush acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Add texture and depth to your designs with brushed aluminum panels, showcasing a contemporary look that complements a variety of architectural styles.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./products.php" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./products.php" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Brush
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./high-glossy-acp-sheet"><img
                                                data-src="./includes/img/series/h-gloss.png" class="lazy-img"
                                                alt="high gloss acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center d-flex">
                                                <div class="hover-center">
                                                    <p class="text-dark">Achieve a polished and refined aesthetic with high-gloss panels, offering a luxurious finish that exudes elegance and modernity in any environment.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./high-glossy-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./high-glossy-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">High
                                            glossy series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./metallic-acp-sheet"><img class="lazy-img"
                                                data-src="./includes/img/series/metalic.png"
                                                alt="metalic acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">
                                                        Embrace industrial chic with metallic-finish panels, featuring a metallic sheen that adds depth and character to interiors, perfect for creating urban-inspired spaces.
                                                    </p>
                                                    <div class="portfolio-icon">
                                                        <a href="./metallic-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./metallic-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Metallic
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./plain-colored-acp-sheet"><img class="lazy-img"
                                                data-src="./includes/img/series/solid.png"
                                                alt="solid color acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center d-flex">
                                                <div class="hover-center">
                                                    <p class="text-dark">
                                                        Opt for understated elegance with solid-colored panels, providing a timeless backdrop for any design scheme, and offering versatility and sophistication.
                                                    </p>
                                                    <div class="portfolio-icon">
                                                        <a href="./plain-colored-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./plain-colored-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Solid
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./sand-acp-sheet"><img class="lazy-img"
                                                data-src="./includes/img/series/sand_series.png"
                                                alt="sand acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">
                                                        Capture the natural beauty of sand with textured panels that emulate the organic texture of beachfront landscapes, infusing spaces with warmth and serenity.
                                                    </p>
                                                    <div class="portfolio-icon">
                                                        <a href="./sand-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./sand-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Sand
                                            Cseries</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./stone-finish-acp-sheet"><img class="lazy-img"
                                                data-src="./includes/img/series/stone_series.png"
                                                alt="stone look acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Embrace the rugged charm of stone with panels that replicate the look and feel of natural stone surfaces, offering durability and authenticity without the weight and maintenance.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./stone-finish-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./stone-finish-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Stone
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide overflow-hidden">
                                    <div class="portfolio-box">
                                        <div class="portfolio-image">
                                            <a href="./velvet-acp-sheet"><img class="lazy-img"
                                                data-src="./includes/img/series/velvet_series.png"
                                                alt="velvet acp sheet" /></a>
                                            <div class="portfolio-hover align-items-center justify-content-center">
                                                <div class="hover-center">
                                                    <p class="text-dark">Indulge in the softness and luxury of velvet with panels that feature a velvety smooth surface, adding a touch of opulence and comfort to interiors, creating inviting spaces that beckon to be touched.</p>
                                                    <div class="portfolio-icon">
                                                        <a href="./velvet-acp-sheet" class="rounded-circle bg-dark"><i
                                                            class="ti-arrow-right text-white"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-caption padding-30px-top sm-padding-15px-tb">
                                            <a href="./velvet-acp-sheet" class="alt-font text-black font-weight-500 text-uppercase d-inline-block margin-5px-bottom">Velvet
                                            series</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- end slide item -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Collection end section -->
    </div>

    <section class="border-top border-color-medium-gray bg-light-gray">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <h2 class="alt-font title-large-2 text-theme font-weight-700 text-uppercase">Applications</h2>
                    <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">Versatile Solutions for Every Space</span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 position-relative p-0 wow animate__fadeIn" data-wow-delay="0.4s">
                    <div class="swiper-container h-auto padding-15px-all black-move" data-slider-options='{ "loop": false, "slidesPerView": 1, "spaceBetween": 30, "autoplay": { "delay": 3000, "disableOnInteraction": false },  "observer": true, "observeParents": true, "navigation": { "nextEl": ".swiper-button-next-nav-3", "prevEl": ".swiper-button-previous-nav-3" }, "keyboard": { "enabled": true, "onlyInViewport": true }, "breakpoints": { "1200": { "slidesPerView": 4 }, "992": { "slidesPerView": 3 }, "768": { "slidesPerView": 2 } }, "effect": "slide" }'>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-interior">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/interiror.png" alt="acp sheet for interior" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    ACP Interior</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-exterior">
                                    <figure class="m-0">
                                        <img data-src="./includes/img/preview/exterior.png" class="lazy-img" alt="acp sheet for exterior" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    ACP Exterior</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-sign-board">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/sign.png" alt="acp sheet for sign board" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    Sign Board</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-wall">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/cladding.png" alt="acp sheet for cladding" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    ACP Wall Cladding</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-ceiling">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/ceiling_acp.png" alt="acp sheet for ceiling" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    Ceiling</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-kitchen">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/kitchen.png" alt="acp sheet for kitchen" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="home_h4_to_p interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    Kitchen</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a href="./acp-sheet-for-bathroom">
                                    <figure class="m-0">
                                        <img class="lazy-img" data-src="./includes/img/preview/bathroom-acp.png" alt="acp sheet for bathroom" />
                                        <div class="opacity-very-light bg-black"></div>
                                        <figcaption>
                                            <div class="interactive-banners-content align-items-start padding-4-rem-all last-paragraph-no-margin">
                                                <p class="home_h4_to_p alt-font font-weight-500 text-white w-55 position-relative z-index-1 xl-w-80 lg-w-40 md-w-50 xs-w-60">
                                                    Bathroom</p>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-button-next-nav-3 swiper-button-next rounded-circle light slider-navigation-style-07 box-shadow-double-large" tabindex="0" role="button" aria-label="Next slide"><i class="feather icon-feather-arrow-right"></i>
                    </div>
                    <div class="swiper-button-previous-nav-3 swiper-button-prev rounded-circle light slider-navigation-style-07 box-shadow-double-large" tabindex="0" role="button" aria-label="Previous slide"><i class="feather icon-feather-arrow-left"></i></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why start section -->
    <!-- <section class="big-section pb-0" id="expertise">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-12 col-xl-12 col-lg-12 col-md-12">
                    <div class="alt-font font-weight-700 text-theme margin-6-rem-bottom  text-uppercase wow animate__fadeInRight" data-wow-delay="0.4s">
                        <h2 class="mb-0 text-border text-border-color-theme text-border-width-2px opacity-3">
                            Why Choose<br />Gayatri Services</h2>
                    </div>
                    <div class="col-12 col-xl-12 col-lg-12 col-md-10 padding-6-rem-bottom xs-padding-6-rem-bottom order-1 order-lg-0">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 align-items-center justify-content-center">
                         
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/101-water-proof.svg" alt="weather-resistant" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Weather Resistant</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/uv-resistant.svg" alt="Fire Retardant" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Fire Retardant</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/light-weight.svg" alt="Light Weight" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Light Weight</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/green-products.svg" alt="Soundproofing" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Excellent Soundproofing</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>

                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/easy-to-install.svg" alt="Expert Installation" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Expert Installation</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/easy-to-clean.svg" alt="Easy Maintenance" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Easy Maintenance</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                           
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/chemical-and-weather-resistant.svg" alt="Custom Solutions" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Custom Solutions</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                            <div class="col-12 text-center text-sm-start">
                                <div class="d-flex flex-row align-item-start margin-15px-bottom xs-margin-10px-bottom justify-content-center justify-content-sm-start">
                                    <img class="lazy-img" data-src="./includes/img/home/feature/bft-guard-borer-fungus-and-termite-resistant.svg" alt="Affordable Pricing" />
                                </div>
                                <span class="alt-font text-medium text-dark text-uppercase d-block">Affordable Pricing</span>
                                <div class="w-100 h-1px bg-medium-gray margin-2-rem-tb xs-margin-3-rem-tb"></div>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- Why end section -->

    <section class="projects-section" style="background-color: #f6f7f8; padding: 80px 0;">
  <div class="container">
    <!-- Title -->
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-8">
        <h3 class="alt-font text-uppercase fw-bold mb-2" style="color: #222; font-size: 2.5rem;">
          Projects
        </h3>
        <p class="text-uppercase" style="color: #555; letter-spacing: 1px; font-size: 1rem;">
          Never regret anything that made you smile.
        </p>
      </div>
    </div>

    <!-- Projects Grid -->
    <div class="row g-4 justify-content-center">
      <!-- Project Item -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Ahemdabad.png" alt="Ahemdabad Project" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Laxmi Celebration.png" alt="Laxmi Celebration" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Luxurico Ceramic Morbi.png" alt="Luxurico Ceramic" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Maliya.png" alt="Maliya Project" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Morbi.png" alt="Morbi Project" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Star Business Hub Kadi.png" alt="Star Business Hub" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Surat Mumbai Highway.png" alt="Surat Mumbai Highway" />
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="project-card">
          <img src="./includes/img/project/Vishakhapatnam.png" alt="Vishakhapatnam" />
        </div>
      </div>
    </div>

    <!-- Button -->
    <div class="row mt-5">
      <div class="col text-center">
        <a href="./projects" class="explore-btn">Explore More Projects</a>
      </div>
    </div>
  </div>
</section>

<style>
/* --- Project Section Styling --- */
.projects-section {
  position: relative;
  overflow: hidden;
}

.project-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.project-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.project-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.project-card:hover img {
  transform: rotate(2deg) scale(1.05);
}

/* --- Button Styling --- */
.explore-btn {
  display: inline-block;
  font-size: 1.1rem;
  text-transform: uppercase;
  color: #111;
  font-weight: 600;
  letter-spacing: 1px;
  border: 2px solid #111;
  padding: 10px 28px;
  border-radius: 30px;
  transition: all 0.3s ease;
}

.explore-btn:hover {
  background: #111;
  color: #fff;
}
</style>

    <!-- start section -->
<section class="big-section">
    <div class="container">
        
        <!-- TITLE -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn">
                <h3 class="alt-font title-large-2 home_title text-theme font-weight-700 text-uppercase mb-0">
                    Our Clients
                </h3>
                <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">
                    was accepted 10 years ago
                </span>
            </div>
        </div>

        <!-- CLIENT LOGOS -->
        <div class="row client-logo-style-06 justify-content-center">

            <div class="col-10 col-md-3 col-sm-6 border-right border-bottom border-color-black-transparent text-center xs-no-border-right wow animate__fadeIn">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/reliance.webp" alt="Reliance">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-right border-bottom border-color-black-transparent text-center sm-no-border-right wow animate__fadeIn" data-wow-delay="0.2s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/FBB.webp" alt="FBB">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-right border-bottom border-color-black-transparent text-center xs-no-border-right wow animate__fadeIn" data-wow-delay="0.3s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/IndianOil.webp" alt="Indian Oil">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-bottom border-color-black-transparent text-center wow animate__fadeIn" data-wow-delay="0.4s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/essar.webp" alt="Essar">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-right border-color-black-transparent text-center sm-border-bottom xs-no-border-right wow animate__fadeIn" data-wow-delay="0.8s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/RCB.webp" alt="RCB">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-right border-color-black-transparent text-center sm-no-border-right sm-border-bottom wow animate__fadeIn" data-wow-delay="0.7s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/Sprint.webp" alt="Sprint">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-right border-color-black-transparent text-center xs-no-border-right xs-border-bottom wow animate__fadeIn" data-wow-delay="0.6s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/SuhaniLaserWork.webp" alt="Suhani Laser Work">
                </div>
            </div>

            <div class="col-10 col-md-3 col-sm-6 border-color-black-transparent text-center wow animate__fadeIn" data-wow-delay="0.5s">
                <div class="client-box padding-4-rem-tb lg-padding-3-rem-tb xs-padding-4-rem-tb">
                    <img class="lazy-img" data-src="https://www.aluminaacp.com/includes/img/home/client/dpr.webp" alt="DPR">
                </div>
            </div>

        </div>

        <!-- BUTTON -->
      

    </div>
</section>


    <!-- end section -->

    <section class="bg-light-gray">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <!-- <span class="d-block margin-15px-bottom"><i class="line-icon-Business-ManWoman icon-large opacity-4-half"></i></span> -->
                    <h3 class="alt-font title-large-2 home_title text-theme font-weight-700 text-uppercase mb-0">
                        TESTIMONIALS </h3>
                    <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">Don't
                    just take our word for it, see what our clients say</span>
                </div>
            </div>
            <div class="row justify-content-center margin-4-rem-bottom sm-margin-4-rem-bottom xs-margin-2-half-rem-bottom">
                <!-- start testimonial item -->
                <div class="col-12 col-lg-4 col-md-6 col-sm-8 md-margin-30px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                    <div class="testimonials testimonials-style-04 last-paragraph-no-margin">
                        <div class="testimonials-bubble border-radius-5px border-all border-color-extra-light-gray bg-white padding-30px-tb padding-40px-lr margin-35px-bottom">
                            <p>Gayatri Services provided excellent ACP sheets for our office renovation. The quality is outstanding and the installation was professional. Highly recommended!</p>
                        </div>
                        <div class="author padding-20px-lr">
                            <img class="lazy-img rounded-circle w-60px h-60px margin-15px-right " data-src="./includes/img/home/woman.png" alt="Priya Sharma Review">
                            <div class="d-inline-block align-middle">
                                <span class="alt-font text-medium font-weight-500 line-height-16px text-extra-dark-gray d-block">Priya
                                Sharma</span>
                                <span class="text-very-small letter-spacing-2px"><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end testimonial item -->
                <!-- start testimonial item -->
                <div class="col-12 col-lg-4 col-md-6 col-sm-8 md-margin-30px-bottom wow animate__fadeIn" data-wow-delay="0.4s">
                    <div class="testimonials testimonials-style-04 last-paragraph-no-margin">
                        <div class="testimonials-bubble border-radius-5px border-all border-color-extra-light-gray bg-white padding-30px-tb padding-40px-lr margin-35px-bottom">
                            <p>Everyone Very Supporting & Give 100% Quality Product , Really I’m Happy to Use Gayatri Services products 😊.</p>
                        </div>
                        <div class="author padding-20px-lr">
                            <img class="lazy-img rounded-circle w-60px h-60px margin-15px-right " data-src="./includes/img/home/man.png" alt="Dilpesh Patel Review">
                            <div class="d-inline-block align-middle">
                                <span class="alt-font text-medium font-weight-500 line-height-16px text-extra-dark-gray d-block">Rajesh
                                Kumar</span>
                                <span class="text-very-small letter-spacing-2px"><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end testimonial item -->
                <!-- start testimonial item -->
                <div class="col-12 col-lg-4 col-md-6 col-sm-8 wow animate__fadeIn" data-wow-delay="0.6s">
                    <div class="testimonials testimonials-style-04 last-paragraph-no-margin">
                        <div class="testimonials-bubble border-radius-5px border-all border-color-extra-light-gray bg-white padding-30px-tb padding-40px-lr margin-35px-bottom">
                            <p>Excellent ACP cladding panels for our commercial building. The quality is top-notch and the pricing is very competitive. Gayatri Services delivered exactly what they promised.</p>
                        </div>
                        <div class="author padding-20px-lr">
                            <img class="lazy-img rounded-circle w-60px h-60px margin-15px-right " data-src="./includes/img/home/man.png" alt="Tushar Kavar Review">
                            <div class="d-inline-block align-middle">
                                <span class="alt-font text-medium font-weight-500 line-height-16px text-extra-dark-gray d-block">Amit
                                Patel</span>
                                <span class="text-very-small letter-spacing-2px"><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i><i
                                    class="fas fa-star text-orange"></i><i class="fas fa-star text-orange"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end testimonial item -->
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 text-center wow animate__zoomIn" data-wow-delay="0.6s">
                    <img data-src="./includes/img/home/rating.png" class="lazy-img" alt="satisfied customers rating" />
                </div>
            </div>
        </div>
    </section>

    <!-- start section -->
    <!-- <section class="parallax big-section" data-parallax-background-ratio="0.2" style="background-image:url('./includes/img/home/video.png');">
        <div class="opacity-full bg-nero-gray"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-8 col-sm-10 position-relative text-center overlap-gap-section wow animate__fadeIn" data-wow-delay="0.2s">
                    <a href="https://www.youtube.com/watch?v=JZTkxeK0Jpo" class="popup-youtube video-icon-box video-icon-large position-relative d-inline-block margin-3-half-rem-bottom">
                    <span>
                        <span class="video-icon bg-white">
                            <i class="icon-simple-line-control-play text-tussock"></i>
                            <span class="video-icon-sonar"><span class="video-icon-sonar-afr bg-white"></span></span>
                        </span>
                    </span>
                </a>
                    <h4 class="alt-font text-white font-weight-600 margin-45px-bottom sm-margin-25px-bottom">A Tour of Our Cutting-Edge Production Facility</h4>
                    <span class="text-white alt-font text-uppercase letter-spacing-2px">Explore the ALUMINA Factory</span>
                </div>
            </div>
        </div>
    </section> -->
    <!-- end section -->

    <!------Footer start ------------->
 
    <!-- start footer -->
    <?php include './includes/footer.php'?>
    <!-- end footer -->

   



    <!-- start scroll to top -->
    <a class="scroll-top-arrow" href="javascript:void(0);"><i class="feather icon-feather-arrow-up"></i></a>
    <!-- end scroll to top<!------Footer end ------------->

    <!------Script start ------------->
    <!-- javascript -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script type="text/javascript" src="./includes/js/jquery.min.js"></script>
    <script type="text/javascript" src="./includes/js/theme-vendors.min.js"></script>
    <script type="text/javascript" src="./includes/js/main.js"></script>
    <script src="./includes/js/lazyload.js"></script>

    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=65ca11d7088410001972809b&product=inline-share-buttons&source=platform" async="async"></script>

  

    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=65ca11d7088410001972809b&product=inline-share-buttons&source=platform" async="async"></script><script>
    var url = 'https://wati-integration-service.clare.ai/ShopifyWidget/shopifyWidget.js?95758';
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = url;

    var options = {
        "enabled": true,
        "chatButtonSetting": {
            "backgroundColor": "#4dc247",
            "ctaText": "",
            "borderRadius": "25",
            "marginLeft": "20",
            "marginBottom": "20",
            "marginRight": "50",
            "position": "left"
        },
        "brandSetting": {
            "brandName": "Gayatri Services",
            "brandSubTitle": "Aluminium Composite Panel ACP manufacturer in Morbi, Gujarat, India.",
            "brandImg": "./includes/img/logo/favi-w.png",
            "welcomeText": "Hi there!\nHow can I help you?",
            "messageText": "WhatsApp Inquiry From Website",
            "backgroundColor": "#304a73",
            "ctaText": "Start Chat",
            "borderRadius": "25",
            "autoShow": false,
            "phoneNumber": "919827343693"  // ✅ Just the phone number, no URL or symbols
        }
    };

    s.onload = function() {
        CreateWhatsappChatWidget(options);
    };

    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
</script>

    <script>
        let hasShown = false;

        document.addEventListener('mouseleave', function(e) {
            if (e.clientY < 10 && !hasShown) {
                document.getElementById('exitPopup').style.display = 'flex';
                hasShown = true;
            }
        });

        document.querySelectorAll('.lightbox .close').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('exitPopup').style.display = 'none';
            });
        });
    </script>


    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "Gayatri Services",
            "image": "./includes/img/home/video.png",
            "@id": "./",
            "url": "./",
            "telephone": "+91 99255 88886",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "42P3 At Chachavadarda, Jamnagar Malia highway, Malia",
                "addressLocality": "Morbi",
                "addressRegion": "GJ",
                "postalCode": "363660",
                "addressCountry": "IN"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": 22.9492407,
                "longitude": 70.6891197
            },
            "openingHoursSpecification": [{
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": [
                        "Monday",
                        "Tuesday",
                        "Wednesday",
                        "Thursday",
                        "Friday",
                        "Saturday"
                    ],
                    "opens": "08:00",
                    "closes": "20:00"
                },
                {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": "Sunday",
                    "opens": "00:00",
                    "closes": "00:00"
                }
            ],
            "sameAs": [
                "https://www.facebook.com/aluminaacp/",
                "https://in.pinterest.com/aluminaacpsheet/",
                "https://www.linkedin.com/company/alumina-acp",
                "https://www.instagram.com/aluminaacp/"
            ],
            "priceRange": "₹₹"
        }
    </script>
    <!------Script end ------------->

    <!-- Modal -->
    

    <script>
        // In JS: conditionally initialize simpler swiper on mobile
        if (window.innerWidth < 768) {
            new Swiper('.swiper-container', {
                slidesPerView: 1,
                loop: true,
                autoplay: false,
                effect: 'slide',
            });
        }
    </script>

    <script>
        setTimeout(function() {
            $('#sessional').modal('show');

            $('.close').on('click', function() {
                $('#sessional').modal('hide');
            })

        }, 6000)
    </script>

    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"1f5128e2200648c49f36c1f59b955b86","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
        crossorigin="anonymous"></script>
</body>

</html>