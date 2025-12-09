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
    <link rel="canonical" href="./contact" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://connect.facebook.net">
    <link rel="preload" as="image" href="./includes/img/slider/s1.png" fetchpriority="high">
    <link rel="preload" as="image" href="./includes/img/logo/logo_dark.png" fetchpriority="high">


    <!-- Google Tag Manager -->
   
    <!-- End Google Tag Manager -->
    <!-- Google tag (gtag.js) -->
    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-S1XDJB9NL4"></script> -->
   

    <!-- favicon icon -->
    <link rel="icon" href="./includes/images/fav.jpg" type="image/jpeg">

    <!-- style sheets and font icons  -->
    <link rel="stylesheet" type="text/css" href="./includes/css/font-icons.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/theme-vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./includes/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/responsive.css" />

    <!-- Meta Pixel Code -->
    <!-- End Meta Pixel Code -->
    <title>Contact Us</title>
    <meta name="description" content="Have questions or inquiries about our ACP products? Our team at Alumina ACP is here to help. Reach out to us for prompt and reliable assistance.">
    <meta name="keywords" content="Contact Alumina">

    <meta name="og:title" content="Contact Alumina: Leaders in ACP Sheet Manufacturing">
    <meta name="og:description" content="Have questions about our ACP sheets and partition solutions? Contact Gayatri Services for expert consultation and reliable assistance with your project needs.">
    <meta name="og:url" content="./contact">
    <meta name="og:image:url" content=".//includes/img/slider/s7.png">

    <meta name="twitter:title" content="Contact Gayatri Services: Premium ACP & Partition Solutions">
    <meta name="twitter:description" content="Have questions about our ACP sheets and partition solutions? Contact Gayatri Services for expert consultation and reliable assistance with your project needs.">
    <meta name="twitter:image" content=".//includes/img/slider/s7.png">

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
                "name": "contact",
                "item": "./contact"
            }]
        }
    </script>

    <!------Head end------------->

    <!------Menu start------------->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTLCXFBB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  
<?php include './includes/header.php' ?>





<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['submit'])) {
    $name = $_POST['fname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $requirement = $_POST['requirement'];
    $message = $_POST['message'];

    // Create the email body
    $body = "
    <h3>New Contact Form Submission</h3>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Phone:</strong> {$phone}</p>
    <p><strong>City/State:</strong> {$city}</p>
    <p><strong>Requirement:</strong> {$requirement}</p>
    <p><strong>Message:</strong> {$message}</p>
    ";

    // PHPMailer setup
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'buddusarath1@gmail.com'; // your gmail
        $mail->Password = 'vmkf rcxu ulcs lzmy'; // your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('buddusarath1@gmail.com', 'Website Contact Form');
        $mail->addAddress('buddusarath1@gmail.com'); // receiver email
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = $body;

        $mail->send();
        echo "<script>alert('Thank you! Your message has been sent successfully.'); window.location.href='contact.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

    <!------Menu end------------->

    <section class="h-350px sm-h-400px xs-h-300px overlap-height cover-background" style="background: url(./includes/img/header/about.png);"></section>
    <section class="pb-0 pt-md-0 overflow-visible">
        <div class="container">
            <div class="row justify-content-center overlap-section z-index-0">
                <div class="col-12 col-lg-10 alt-font text-center bg-theme text-white padding-3-rem-tb position-relative tilt-box" data-tilt-options='{ "maxTilt": 20, "perspective": 1000, "easing": "cubic-bezier(.03,.98,.52,.99)", "scale": 1, "speed": 500, "transition": true, "reset": true, "glare": false, "maxGlare": 1 }'
                    style="background-image: url('./includes/images/blog-post-layout-02-img-pattern.png');">
                    <div class="w-1px h-90px bg-white mx-auto absolute-middle-center top-0px"></div>
                    <h1 class="main_banner font-weight-600 w-50 mx-auto md-w-70 xs-w-90">Contact Us</h1>
                    <div class="text-uppercase text-medium font-weight-500 margin-25px-bottom alt-font">
                        <a href="./main.php" class="text-white d-inline-block">Home</a><span class="margin-20px-lr w-1px h-10px bg-white d-inline-block"></span>
                        <a class="d-inline-block text-white">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- start section -->
    <section class="padding-8-half-rem-lr xl-padding-3-rem-lr pb-xs-0 lg-no-padding-lr contact-info">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 text-center margin-1-rem-bottom wow animate__fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <h2 class="h5_to_h3 alt-font font-weight-600 text-uppercase text-extra-dark-gray mb-0">Connect With Us
                    </h2>
                </div>
            </div>
            <div class="row justify-content-center margin-3-rem-top md-no-margin-tb">
                <!-- start category item -->
                <div class="col-12 col-xl-4 col-md-6 col-sm-10 shop-category-style-02 lg-margin-4-rem-bottom sm-margin-6-rem-bottom">
                    <div class="shop-product align-items-center d-flex padding-30px-lr xs-no-padding-lr">
                        <div class="shop-product-image me-0 text-center d-flex justify-content-center align-items-center wow animate__zoomIn">
                            <img class="icon-box-circled" src="./includes/img/contact/bg-location.png" alt="location icon" />
                        </div>
                        <div class="shop-product-overlay position-relative wow animate__fadeIn" data-wow-delay="0.2s">
                            <p class="h6_to_h3 alt-font text-extra-dark-gray mb-0 letter-spacing-minus-1px line-height-40px sm-line-height-30px font-weight-500 text-uppercase">
                                Reach Us</h5>
                                <p style="width: 206px;">Akkayyapalem
 ,Vishakapatanm ,Andhra Pradesh.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4 col-md-6 col-sm-10 shop-category-style-02 lg-margin-4-rem-bottom sm-margin-6-rem-bottom">
                    <div class="shop-product align-items-center d-flex padding-30px-lr xs-no-padding-lr">
                        <div class="shop-product-image me-0 text-center d-flex justify-content-center align-items-center wow animate__zoomIn">
                            <img class="icon-box-circled" src="./includes/img/contact/bg-email.png" alt="email icon" />
                        </div>
                        <div class="shop-product-overlay position-relative wow animate__fadeIn" data-wow-delay="0.2s">
                            <p class="h6_to_h3 alt-font text-extra-dark-gray mb-0 letter-spacing-minus-1px line-height-40px sm-line-height-30px  font-weight-500 text-uppercase">
                                Email Us</h5>
                                <p style="width: 206px;">
                                    <a href="/cdn-cgi/l/email-protection#7b1a170e1612151a12151f121a3b021a13141455181416"><span class="__cf_email__" >gayatriservices@gmail.com</span></a><br />
                                    <!-- <a href="/cdn-cgi/l/email-protection#d3babdb5bc93b2bfa6bebabdb2b2b0a3fdb0bcbe"><span class="__cf_email__" data-cfemail="85ecebe3eac5e4e9f0e8ecebe4e4e6f5abe6eae8">[email&#160;protected]</span></a> -->
                                </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4 col-md-6 col-sm-10 shop-category-style-02 lg-margin-4-rem-bottom sm-margin-6-rem-bottom">
                    <div class="shop-product align-items-center d-flex padding-30px-lr xs-no-padding-lr">
                        <div class="shop-product-image me-0 text-center d-flex justify-content-center align-items-center wow animate__zoomIn">
                            <img class="icon-box-circled" src="./includes/img/contact/bg.png" alt="call icon" />
                        </div>
                        <div class="shop-product-overlay position-relative wow animate__fadeIn" data-wow-delay="0.2s">
                            <p class="h6_to_h3 alt-font text-extra-dark-gray mb-0 letter-spacing-minus-1px line-height-40px sm-line-height-30px  font-weight-500 text-uppercase">
                                Call Us</h5>
                                <p style="width: 206px;"><a href="tel:+91 99255 88886">+919827343693</a></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- end section -->

    <!-- start section -->

    <section class="parallax xs-padding-15px-lr contact-form" style="background-color: #fff;">
        <div class="container">
            <div class="row">
                <div class="col-12 bg-light-gray overflow-hidden position-relative" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                    <div class="row">
                        <div class="col-12 col-md-6 cover-background sm-h-350px wow animate__fadeInLeft" data-wow-delay="0.4s" style="background: url('./includes/img/contact/contact.png');"></div>
                        <div class="col-12 col-md-6 padding-4-rem-all lg-padding-5-rem-all xs-padding-4-rem-all wow animate__fadeIn" data-wow-delay="0.4s">
                            <p>For questions or concerns please contact us via telephone or simply complete the contact form and one of our knowledgeable representatives will respond in a timely manner.</p>
                            <form action="" method="post" enctype="multipart/form-data" accept-charset="utf-8" class="alt-font text-extra-dark-gray">
    <div class="row">
        <div class="col-lg-6">
            <input type="text" name="fname" placeholder="Your Name" required />
        </div>
        <div class="col-lg-6">
            <input type="email" name="email" placeholder="Your Email" required />
        </div>
        <div class="col-lg-6">
            <input type="text" name="phone" placeholder="Your Mobile No" required />
        </div>
        <div class="col-lg-6">
            <input type="text" name="city" placeholder="City/State" required />
        </div>
        <div class="col-lg-12">
            <textarea name="requirement" rows="3" placeholder="Your Requirement"></textarea>
        </div>
        <div class="col-lg-12">
            <textarea name="message" rows="3" placeholder="Message"></textarea>
        </div>
    </div>
    <input type="submit" name="submit" value="Submit" class="btn btn-medium btn-dark-gray mb-0" />
</form>

                            <!-- Load Google reCAPTCHA v3 -->
                            <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
                            <!-- <script src="https://www.google.com/recaptcha/api.js?render=6LdpNMQrAAAAAM_JgIEDCjcnwrIicSz5Fb4M43sp"></script> -->
                            <!-- <script>
                                grecaptcha.ready(function() {
                                    grecaptcha.execute("6LdpNMQrAAAAAM_JgIEDCjcnwrIicSz5Fb4M43sp", {
                                        action: "popupinquiry"
                                    }).then(function(token) {
                                        document.getElementById("g-recaptcha-response").value = token;
                                    });
                                });
                            </script> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end section -->
    <section class="contact-map pb-0">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-12 col-sm-12 px-0">
                    <iframe style="margin-bottom: -10px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15200.86260564252!2d83.28741337699739!3d17.734475219553946!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a39432ec550fd99%3A0x32dc7ad27779eef3!2sAkkayyapalem%2C%20Visakhapatnam%2C%20Andhra%20Pradesh%20530016!5e0!3m2!1sen!2sin!4v1762604758735!5m2!1sen!2sin"
                        width="100%" height="610" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                </div>
            </div>
        </div>
    </section>

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

    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"1f5128e2200648c49f36c1f59b955b86","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}'
        crossorigin="anonymous"></script>
</body>

</html>