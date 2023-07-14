
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>>Close Page to Continue</title>
  <meta content="OPA, gets you updated with the latest and greatest clothing trends!" name="description">
  <meta content="clothing, trends, outfit, socail media, fashion" name="keywords">

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
  <header id="header" class="d-flex align-items-center" style="min-height:100vh;max-height:100vh;">
    <div class="container d-flex flex-column align-items-center h-100 h-full">
	<div class="row">
	    
        <div class="container">
          <h2>Close this page to continue</h2>
          <p>If this page is not closed, click the close or cancel button to close it.</p>
           <p class="ios-note">Note: On iOS devices, you can use the browser's built-in close functionality to close/cancel this page.</p>
          <button class="close-btn" onclick="closePage()">Close</button>
        </div>
<br><br><br><br><br><br>
        </div>
    </div>
  </header><!-- End #header -->
      
        <script>
          function closePage() {
              window.close(); // Close the page
          }
      
          window.onload = function() {
              closePage();
          };
        </script>
  <!-- Vendor JS Files -->
  <script src="{{asset('maintenance/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('maintenance/assets/vendor/php-email-form/validate.js')}}"></script>


</body>

</html>