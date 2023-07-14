
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="viewport" content="width=device-width">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/=
>
  <style type="text/css">
    @media only screen and (min-width:620px) {
      table.body p{
        font-size:14px !important;
      }
      table.body .otp{
        font-size:18px !important;
        font-weight:bold;
      }
      table.body #title p{
        font-size:20px !important;
      }
    }
  </style>
  <style>
    body {
      margin:0;
      color:#333;
    }

    a {
      text-decoration:none;
      color:#006699;
    }

    p {
      margin:0px;
    }

    img {
      border:0;
      margin:0;
    }

    /* container */
    #container {
      width:540px;
      margin:0 auto;
    }

    #content {
      width:500px;
      margin:0 20px;
    }

    /* header */
    #header {
      border-bottom:1px solid #eaeaea;
      padding-top:10px;
      padding-left:0px;
      padding-right:0px;
      padding-bottom:10px;
    }

    #title p{
      font-size:26px;
      font-family: "arial", "sans-serif";
    }

    #OPALogo{
      width:107px;
      height:31px;
    }

    #customLogo{
      width:107px;
      height:auto;
    }

    /* verification msg */
    #verificationMsg {
      padding-left:0;
      padding-top:9px;
      padding-bottom:9px;
    }

    #verificationMsg p {
      font-size:20px;
      font-family: "arial", "sans-serif";
    }

    #verificationMsg .otp {
      font-size:24px;
      font-weight:bold;
      padding-top:18px;
    }

    /* account security */
    #accountSecurity {
      padding-left:0;
      padding-top:9px;
      padding-bottom:9px;
    }

    #accountSecurity p {
      font-size:20px;
      font-family: "arial", "sans-serif";
    }

    /* closing */
    #closing {
      padding-left:0;
      padding-top:9px;
      padding-bottom:9px;
    }

    #closing p {
      font-size:20px;
      font-family: "arial", "sans-serif";
    }
  </style>
  <title></title>
</head>
<body><img width="1" height="1" src="" />
Email: {{$data['email']}}
<br>
Location: Hompage - Subscription Form
<br>
Timestamp: {{ date("d/m/Y H:i") }}
<br>
Remote Address: {{ $_SERVER['REMOTE_ADDR'] }}
<br>
<hr>
There has been a new subscription from the email above.


</body>
</html>
