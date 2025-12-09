<?php
// Include database connection
require_once 'db.php';

// Fetch all categories
$category_query = "SELECT catgId, catgName FROM category ORDER BY catgName";
$category_result = mysqli_query($conn, $category_query);
$categories = [];
if ($category_result) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $categories[] = $row;
    }
}

// Get selected category from URL
$selected_catgId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch items from database
$query = "SELECT item_Code, item_Name, image_path, price FROM items WHERE stat = 'active'";
if ($selected_catgId > 0) {
    $query .= " AND catgId = " . $selected_catgId;
}
$query .= " ORDER BY item_Id";

$result = mysqli_query($conn, $query);
$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}
?>
<!------Head start------------->
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
    <link rel="canonical" href="./metallic-acp-sheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="preload" as="image" href="./includes/img/slider/s1.png" fetchpriority="high">
    <link rel="preload" as="image" href="./includes/img/logo/logo_dark.png" fetchpriority="high">

<title>Products </title>
    <!-- Google Tag Manager -->
    
    <!-- favicon icon -->
     <link rel="icon" href="./includes/images/fav.jpg" type="image/jpeg">

    <!-- style sheets and font icons  -->
    <link rel="stylesheet" type="text/css" href="./includes/css/font-icons.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/theme-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/responsive.css" />

    <style>
        .category-sidebar {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .category-sidebar li {
            margin-bottom: 10px;
        }
        .category-sidebar a {
            display: block;
            padding: 10px 15px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .category-sidebar a:hover {
            background-color: #e9ecef;
            color: #000;
        }
        .category-sidebar a.active {
            background-color: #304a73;
            color: #fff;
        }
    </style>

    <!-- Meta Pixel Code -->
   
    <!-- End Meta Pixel Code -->
    <!------Head end------------->

    <!-- <title>Metallic ACP Sheet | Gleaming Metallic ACP Sheet By Alumina</title> -->
    <meta name="description" content="Discover Gayatri Services' premium ACP sheet collection and partition solutions. Elevate your space with high-quality materials in various finishes and textures.">

    <meta name="og:title" content="Premium ACP Sheets & Partition Solutions by Gayatri Services">
    <meta name="og:description" content="Discover Gayatri Services' premium ACP sheet collection and partition solutions. Elevate your space with high-quality materials in various finishes and textures.">
    <meta name="og:url" content="./metallic-acp-sheet">
    <meta name="og:image:url" content="./includes/img/products/catagory-product/metallic/cover/black-silver_AM-103_PGSA.png">

    <meta name="twitter:title" content="Premium ACP Sheets & Partition Solutions by Gayatri Services">
    <meta name="twitter:description" content="Discover Gayatri Services' premium ACP sheet collection and partition solutions. Elevate your space with high-quality materials in various finishes and textures.">
    <meta name="twitter:image" content="./includes/img/products/catagory-product/metallic/cover/black-silver_AM-103_PGSA.png">
    <script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "home",
                "item": "./"
            }, {
                "@type": "ListItem",
                "position": 2,
                "name": "metallic acp sheet",
                "item": "./metallic-acp-sheet"
            }]
        }
    </script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [{
                "@type": "Question",
                "name": "What are the benefits of using metallic ACP sheets?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Metallic ACP sheets add a modern edge to any project with their sleek metallic finish. They are lightweight, durable, and easy to install, making them a popular choice for both interior and exterior applications."
                }
            }, {
                "@type": "Question",
                "name": "Can metallic ACP sheets be used in high-temperature areas?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, metallic ACP sheets are heat-resistant, making them suitable for use in high-temperature areas like kitchens and fireplaces. However, it's important to ensure proper ventilation to prevent heat buildup."
                }
            }, {
                "@type": "Question",
                "name": "Do metallic ACP sheets require special maintenance?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Metallic ACP sheets are easy to maintain! Simply clean with a mild detergent and water to remove dirt and grime. However, due to its matte finish, this sheet is more likely to have dents and stains."
                }
            }, {
                "@type": "Question",
                "name": "Do metallic ACP sheets fade over time?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Our metallic ACP sheets are UV-resistant and designed to maintain their color and finish over time, minimizing fading even with prolonged exposure to sunlight."
                }
            }, {
                "@type": "Question",
                "name": "Can metallic ACP sheets be used in coastal areas?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, our metallic ACP sheets are corrosion-resistant, making them suitable for coastal areas."
                }
            }]
        }
    </script>
    <!------Menu start------------->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
   
<?php include './includes/header.php' ?>
    <!------Menu end------------->

    <div class="alumina_header page-header page-header_align_center header_banner alumina_banner_outer" style="background-image:url('./includes/img/header/about.png')">
        <div class="page-header_wrapper text-center">
            <div class="wgl-container">
                <div class="page-header_content header_margin">
                    <h1 class="page-header_title text-white f-s-banner lh-3">
                       Home | Products</h1>
                 
                </div>
            </div>
        </div>
    </div>
    <section class="collection_page">
        <div class="container">
            <!-- <div class="row">
                <div class="col-lg-12 mb-5 collection_content">
                    <p class="text-dark">Gayatri Services offers premium ACP sheets and partition solutions with a wide range of finishes and textures. Our products combine strength, style, and durability to enhance every space, suitable for both interior and exterior applications.</p>
                    <p class="text-dark">
                        Available in various colors and finishes including metallic, wooden, marble, and more. Choose Gayatri Services for quality materials with expert installation and competitive pricing.
                    </p>
                </div>
            </div> -->
            <div class="row">
                <div class="col-lg-3">
                    <div class="sidebar">
                        <h5 class="font-weight-700">Categories</h5>
                        <ul class="category-sidebar">
                            <li>
                                <a href="products.php" class="<?php echo ($selected_catgId == 0) ? 'active' : ''; ?>">All Products</a>
                            </li>
                            <?php foreach ($categories as $category) : ?>
                                <li>
                                    <a href="products.php?category_id=<?php echo $category['catgId']; ?>" class="<?php echo ($selected_catgId == $category['catgId']) ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($category['catgName']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-xl-9 col-md-12 blog-content">

                    <ul class="blog-grid blog-wrapper grid grid-loading grid-3col xl-grid-3col lg-grid-3col md-grid-2col sm-grid-2col xs-grid-1col gutter-extra-large">
                        <li class="grid-sizer"></li>
                        <?php
                        // Loop through items from database
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                // Sanitize output
                                $itemCode = htmlspecialchars($item['item_Code'] ?? '', ENT_QUOTES, 'UTF-8');
                                $itemName = htmlspecialchars($item['item_Name'] ?? '', ENT_QUOTES, 'UTF-8');
                                $imagePath = htmlspecialchars($item['image_path'] ?? './includes/img/products/default.png', ENT_QUOTES, 'UTF-8');
                                $price = isset($item['price']) ? number_format($item['price'], 0) : '0';

                                // Create URL-friendly slug from item name
                                $slug = strtolower(str_replace(' ', '-', $itemName));
                        ?>
                        <li class="grid-item wow animate__fadeIn">
                            <a href="./contact.php">
                                <div class="blog-post border-radius-5px bg-white box-shadow-medium">
                                    <div class="blog-post-image">
                                        <?php 
                                        // Fix image path handling
                                        $imagePath = $item['image_path'];
                                        
                                        // Remove leading slash if present
                                        $imagePath = ltrim($imagePath, '/');
                                        
                                        // Check if image exists
                                        if (!empty($imagePath) && file_exists($imagePath)) {
                                            $imgUrl = $imagePath;
                                        } else {
                                            // Fallback to default image
                                            $imgUrl = '../includes/img/products/default.png';
                                        }
                                        ?>
                                       

                                             <img loading="lazy" 
     src="<?php echo htmlspecialchars($imgUrl); ?>" 
     alt="<?php echo htmlspecialchars($item['item_Name']); ?>"
     style="width: 100%; height: 250px; object-fit: cover; border-radius: 5px; transition: transform 0.3s ease;"
     onmouseover="this.style.transform='scale(1.05)';"
     onmouseout="this.style.transform='scale(1)';">





                                    </div>
                                    <div class="post-details padding-15px-all text-center">
                                        <p class="text-small text-uppercase text-medium-gray margin-5px-bottom mb-0">
                                            <?php echo htmlspecialchars($item['item_Code']); ?>
                                        </p>
                                        <h6 class="alt-font font-weight-500 text-extra-dark-gray margin-5px-bottom">
                                            <?php echo htmlspecialchars($item['item_Name']); ?>
                                        </h6>
                                        <p class="text-medium text-theme font-weight-600 mb-0">
                                            ₹ <?php echo htmlspecialchars($item['price']); ?>/sq ft
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php
                            }
                        } else {
                            // Display message if no items found
                            echo '<li class="col-12"><p class="text-center">No products available in this category.</p></li>';
                        }
                        ?>
                    </ul>

                </div>
            </div>
        </div>
    </section>

    <section class="bg-light-gray wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <h2 class="alt-font title-large-2 text-theme font-weight-700 text-uppercase mb-0"> Advantages</h2>
                    <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">the
					Crafted to exceed expectations</span>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-lg-4 justify-content-center">
                <!-- start services item -->
                <div class="col-md-9 md-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <div class="feature-box h-100 text-start box-shadow-large box-shadow-double-large-hover bg-white padding-2-rem-all lg-padding-3-rem-all md-padding-4-half-rem-all">
                        <div class="feature-box-content">
                            <span class="margin-15px-bottom d-block text-extra-medium">01</span>
                            <h6 class="alt-font font-weight-600 d-block text-extra-dark-gray">Sleek metallic luster</h6>

                            <div class="h-1px bg-medium-gray margin-25px-bottom w-100"></div>

                        </div>
                        <div class="feature-box-overlay bg-white"></div>
                    </div>
                </div>
                <!-- end services item -->
                <!-- start services item -->
                <div class="col-md-9 md-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeIn;">
                    <div class="feature-box h-100 text-start box-shadow-large box-shadow-double-large-hover bg-white padding-2-rem-all lg-padding-3-rem-all md-padding-4-half-rem-all">
                        <div class="feature-box-content">
                            <span class="margin-15px-bottom d-block text-extra-medium">02</span>
                            <h6 class="alt-font font-weight-600 d-block text-extra-dark-gray">Reflective look</h6>
                            <div class="h-1px bg-medium-gray margin-25px-bottom w-100"></div>
                        </div>
                        <div class="feature-box-overlay bg-white"></div>
                    </div>
                </div>
                <!-- end services item -->
                <!-- start services item -->
                <div class="col-md-9 wow animate__fadeIn" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeIn;">
                    <div class="feature-box h-100 text-start box-shadow-large box-shadow-double-large-hover bg-white padding-2-rem-all lg-padding-3-rem-all md-padding-4-half-rem-all">
                        <div class="feature-box-content">
                            <span class="margin-15px-bottom d-block text-extra-medium">03</span>
                            <h6 class="alt-font font-weight-600 d-block text-extra-dark-gray">Suitable for futuristic designs</h6>
                            <div class="h-1px bg-medium-gray margin-25px-bottom w-100"></div>
                        </div>
                        <div class="feature-box-overlay bg-white"></div>
                    </div>
                </div>
                <div class="col-md-9 wow animate__fadeIn" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeIn;">
                    <div class="feature-box h-100 text-start box-shadow-large box-shadow-double-large-hover bg-white padding-2-rem-all lg-padding-3-rem-all md-padding-4-half-rem-all">
                        <div class="feature-box-content">
                            <span class="margin-15px-bottom d-block text-extra-medium">04</span>
                            <h6 class="alt-font font-weight-600 d-block text-extra-dark-gray">Enhances architectural look</h6>
                            <div class="h-1px bg-medium-gray margin-25px-bottom w-100"></div>
                        </div>
                        <div class="feature-box-overlay bg-white"></div>
                    </div>
                </div>
                <!-- end services item -->
            </div>
        </div>
    </section>

    <!------Application Section start ------------->
    <section class="border-top border-color-medium-gray inspired_g_new">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7 col-sm-8 text-center margin-3-rem-bottom md-margin-3-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <h2 class="alt-font title-large-2 text-theme font-weight-700 text-uppercase">Inspired By</h2>
                    <span class="d-inline-block alt-font text-extra-dark-gray text-large text-uppercase font-weight-500 letter-spacing-1px margin-20px-bottom margin-10px-top">Every moment is a fresh beginning. </span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 position-relative p-0 wow animate__fadeIn" data-wow-delay="0.4s">
                    <div class="swiper-container h-auto padding-15px-all black-move" data-slider-options='{ "loop": true, "slidesPerView": 1, "spaceBetween": 30, "autoplay": { "delay": 3000, "disableOnInteraction": false },  "observer": true, "observeParents": true, "navigation": { "nextEl": ".swiper-button-next-nav-3", "prevEl": ".swiper-button-previous-nav-3" }, "keyboard": { "enabled": true, "onlyInViewport": true }, "breakpoints": { "1200": { "slidesPerView": 4 }, "992": { "slidesPerView": 3 }, "768": { "slidesPerView": 2 } }, "effect": "slide" }'>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/interiror.png" alt="acp sheet for interior" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">ACP Interior</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/exterior.png" alt="acp sheet for exterior" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">ACP Exterior</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/sign.png" alt="acp sheet for sign board" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">Sign Board</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/cladding.png" alt="acp sheet for cladding" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">ACP Wall Cladding</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/ceiling_acp.png" alt="acp sheet for ceiling" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">Ceiling</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/kitchen.png" alt="acp sheet for kitchen" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="home_h4_to_p interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="alt-font font-weight-500  position-relative z-index-1 ">Kitchen</p>
                                        </div>
                                    </figcaption>
                                </a>
                            </div>
                            <div class="swiper-slide box-shadow-small box-shadow-extra-large-hover interactive-banners-style-09 lg-margin-30px-bottom xs-margin-15px-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                                <a>
                                    <figure class="m-0">
                                        <img src="./includes/img/preview/bathroom-acp.png" alt="acp sheet for bathroom" />
                                        <div class="opacity-very-light bg-black"></div>
                                    </figure>
                                    <figcaption>
                                        <div class="interactive-banners-content align-items-start text-center padding-2-rem-all last-paragraph-no-margin">
                                            <p class="home_h4_to_p alt-font font-weight-500  position-relative z-index-1 ">Bathroom</p>
                                        </div>
                                    </figcaption>
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
    <!------Application Section end ------------->


    <!-- Testimonial start section -->
    <section class="bg-light-gray">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-4 col-sm-8 text-center text-lg-start md-margin-5-rem-bottom wow animate__fadeIn" data-wow-delay="0.2s">
                    <span class="alt-font font-weight-500 text-yellow-ochre-light text-uppercase d-block margin-15px-bottom">Loved
					by our customers</span>
                    <h5 class="alt-font font-weight-700 text-uppercase text-extra-dark-gray letter-spacing-minus-1px m-0">
                        What our clients are saying about our products</h5>
                </div>
                <div class="col-xl-7 offset-xl-1 col-lg-8 position-relative p-0 wow animate__fadeIn" data-wow-delay="0.4s">
                    <div class="swiper-container h-auto padding-15px-all black-move" data-slider-options='{ "loop": true, "slidesPerView": 1, "spaceBetween": 30, "autoplay": { "delay": 3000, "disableOnInteraction": false },  "observer": true, "observeParents": true, "navigation": { "nextEl": ".swiper-button-next-nav-3", "prevEl": ".swiper-button-previous-nav-3" }, "keyboard": { "enabled": true, "onlyInViewport": true }, "breakpoints": { "1200": { "slidesPerView": 1 }, "992": { "slidesPerView": 1 }, "768": { "slidesPerView": 1 } }, "effect": "slide" }'>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Best quality at good rates.</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Vijay Girglani</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Best quality with good collection with advanced service</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Anu Solanki</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Very Good Quality & Excellent Service Everyone is Very Supportive & Gives 100% Quality Products, Really Happy to Use Gayatri Services Products 😊</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Dilpesh Patel</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Superb quality products with the best price</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Shivam</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Bbest ACP range with choice and have good quality with the best rate.</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Shiv Kashi</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Very good product with good durability at best price</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Tushar Kavar</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="feature-box feature-box-left-icon-middle">
                                    <div class="feature-box-icon margin-50px-right xs-margin-15px-right">
                                        <img class="rounded-circle w-180px h-180px sm-w-150px sm-h-150px xs-w-80px xs-h-80px" src="./includes/img/user.png" alt="user" />
                                    </div>
                                    <div class="feature-box-content">
                                        <p class="w-85 lg-w-100">Good response and excellent product quality</p>
                                        <div class="text-extra-dark-gray alt-font text-uppercase font-weight-600 line-height-20px">Ronil Chandrala</div>
                                        <!-- <span class="alt-font text-small text-uppercase">Microsoft Design</span> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
    <!-- Testimonial end section -->

    <section class="collection_faq">
        <div class="container-fluid padding-twelve-lr xl-padding-ten-lr lg-padding-three-lr">
            <div class="row">
                <div class="col-12 text-center margin-7-rem-bottom">
                    <span class="d-block alt-font margin-5px-bottom">Clarify Your Queries</span>
                    <h2 class="alt-font text-extra-dark-gray font-weight-600 mb-0">Frequently Asked Questions</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-12 wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <div class="panel-group accordion-event accordion-style-03" id="accordion1" data-active-icon="fa-angle-down" data-inactive-icon="fa-angle-right">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel bg-light-gray box-shadow-small border-radius-5px">
                                    <div class="panel-heading">
                                        <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion1" href="#collapseOne" aria-expanded="false">
                                            <div class="panel-title">
                                                <span class="alt-font text-extra-dark-gray d-inline-block font-weight-500">What are the benefits of using metallic ACP sheets?</span>
                                                <i class="indicator fas text-fast-blue icon-extra-small fa-angle-right"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="panel-body">Metallic ACP sheets add a modern edge to any project with their sleek metallic finish. They are lightweight, durable, and easy to install, making them a popular choice for both interior and exterior applications.</div>
                                    </div>
                                </div>
                                <div class="panel bg-light-gray box-shadow-small border-radius-5px">
                                    <div class="panel-heading">
                                        <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion1" href="#collapseTwo" aria-expanded="false">
                                            <div class="panel-title">
                                                <span class="alt-font text-extra-dark-gray d-inline-block font-weight-500">Can metallic ACP sheets be used in high-temperature areas?</span>
                                                <i class="indicator fas fa-angle-right text-fast-blue icon-extra-small"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="panel-body">Yes, metallic ACP sheets are heat-resistant, making them suitable for use in high-temperature areas like kitchens and fireplaces. However, it's important to ensure proper ventilation to prevent heat buildup.</div>
                                    </div>
                                </div>
                                <div class="panel bg-light-gray box-shadow-small border-radius-5px">
                                    <div class="panel-heading">
                                        <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion1" href="#collapseThree" aria-expanded="false">
                                            <div class="panel-title">
                                                <span class="alt-font text-extra-dark-gray d-inline-block font-weight-500">Do metallic ACP sheets require special maintenance?</span>
                                                <i class="indicator fas fa-angle-right text-fast-blue icon-extra-small"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="panel-body">Metallic ACP sheets are easy to maintain! Simply clean with a mild detergent and water to remove dirt and grime. However, due to its matte finish, this sheet is more likely to have dents and stains.</div>
                                    </div>
                                </div>
                                <div class="panel bg-light-gray box-shadow-small border-radius-5px">
                                    <div class="panel-heading">
                                        <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion1" href="#collapseFour" aria-expanded="false">
                                            <div class="panel-title">
                                                <span class="alt-font text-extra-dark-gray d-inline-block font-weight-500">Do metallic ACP sheets fade over time?</span>
                                                <i class="indicator fas fa-angle-right text-fast-blue icon-extra-small"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseFour" class="panel-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="panel-body">Our metallic ACP sheets are UV-resistant and designed to maintain their color and finish over time, minimizing fading even with prolonged exposure to sunlight.</div>
                                    </div>
                                </div>
                                <div class="panel bg-light-gray box-shadow-small border-radius-5px">
                                    <div class="panel-heading">
                                        <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-parent="#accordion1" href="#collapseFive" aria-expanded="false">
                                            <div class="panel-title">
                                                <span class="alt-font text-extra-dark-gray d-inline-block font-weight-500">Can metallic ACP sheets be used in coastal areas?</span>
                                                <i class="indicator fas fa-angle-right text-fast-blue icon-extra-small"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseFive" class="panel-collapse collapse" data-bs-parent="#accordion1">
                                        <div class="panel-body">Yes, our metallic ACP sheets are corrosion-resistant, making them suitable for coastal areas.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!------Footer start ------------->
    
    <!-- start footer -->
    <?php include './includes/footer.php' ?>
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

    <!------Script end ------------->


    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"1f5128e2200648c49f36c1f59b955b86","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
        crossorigin="anonymous"></script>
</body>

</html>