
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>OPA - Coming Soon</title>
  <meta content="OPA, gets you updated with the latest and greatest clothing trends!" name="description">
  <meta content="clothing, trends, outfit, socail media, fashion" name="keywords">

<meta name="facebook-domain-verification" content="dgtt1lmm3mm2naaj5iavoj7hqlzmma" />

  <!-- Favicons -->
  <link href="{{asset('maintenance/assets/img/favicon.png')}}" rel="icon">
  <link href="{{asset('maintenance/assets//img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,600,600i,700,700i|Baloo:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('maintenance/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('maintenance/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{asset('maintenance/assets//css/style.css')}}" rel="stylesheet">

</head>

<body>

<div class="area" >
            <ul class="circles">
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
            </ul>
    </div >
  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex flex-column align-items-center">
	<div class="row">
		<div class="col-12 col-md-6 align-self-center text-center text-md-left pb-5 pb-md-0">
		<img style="padding-left:4px;" src="{{asset('icon.png')}}">
      <h1 class="mt-1 large-text">OPA</h1>
      <h2>We're busy shoping, will be back soon!</h2>
	  <?php $k='
      <div class="countdown d-flex justify-content-center" data-count="2023/12/3">
        <div>
          <h3>%d</h3>
          <h4>Days</h4>
        </div>
        <div>
          <h3>%h</h3>
          <h4>Hours</h4>
        </div>
        <div>
          <h3>%m</h3>
          <h4>Minutes</h4>
        </div>
        <div>
          <h3>%s</h3>
          <h4>Seconds</h4>
        </div>
      </div>
	  '; ?>
      <div class="subscribe mr-3">
<hr><br>
        <h4 class="text-left">Subscribe now to get the latest updates!</h4>
        <form action="{{route('newsletter.request')}}" method="post" role="form" class="php-email-form">
          <div class="subscribe-form">
            <input type="email" name="email" required><input type="submit" value="Subscribe">
          </div>
          <div class="mt-2">
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request was sent. Thank you!</div>
            <div class="sent-message2">You are already subscribed. Thank you!</div>
          </div>
        </form>
      </div>
	  </div>
		<div class="col-12 col-md-6 right-side text-center" style="border-bottom: solid 5px black;">
			<img src="{{asset('maintenance/assets/img/mobile.png')}}" style="max-width: calc(70%) !important;">
		</div>

      <div class="social-links text-center">
        <a href="https://twitter.com/theopaapp" class="twitter"><i class="bi bi-twitter"></i></a>
        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
        <a href="https://instagram.com/theopaapp" class="instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
      </div>

    </div>
  </header><!-- End #header -->

  <main id="main">

    <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container">

        <div class="section-title">
          <h2>About Us</h2>
          <p>We are your one stop fashion trends destination!</p>
        </div>

        <div class="row mt-2">
          <div class="col-lg-4 col-md-6 icon-box">
            <div class="icon"><i class="bi bi-bar-chart"></i></div>
            <h4 class="title"><a href="">Latest Trends</a></h4>
            <p class="description"></p>
          </div>
          <div class="col-lg-4 col-md-6 icon-box">
            <div class="icon"><i class="bi bi-basket"></i></div>
            <h4 class="title"><a href="">Outfits</a></h4>
            <p class="description"></p>
          </div>
          <div class="col-lg-4 col-md-6 icon-box">
            <div class="icon"><i class="bi bi-brightness-high"></i></div>
            <h4 class="title"><a href="">Share your opinion</a></h4>
            <p class="description"></p>
          </div>
        </div>

      </div>
    </section><!-- End About Us Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>OPA</span></strong>. All Rights Reserved
      </div>
    </div>
  </footer><!-- End #footer -->

  <!-- Vendor JS Files -->
  <script src="{{asset('maintenance/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('maintenance/assets/vendor/php-email-form/validate.js')}}"></script>


</body>

</html>