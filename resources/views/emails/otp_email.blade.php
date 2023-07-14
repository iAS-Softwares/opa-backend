
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
<table align="center" cellspacing="0" id="container" cellpadding="0=
" class="body">
  <tbody>
  <tr>
    <td>
      <table cellspacing="0" id="content" cellpadding="0">
        <tbody>
        <tr>
          <td id="header">
            <table cellspacing="0" cellpadding="0">
              <tbody>
              <tr>
                <td width="250" id="logo">
<img src="" id="customLogo"/>                </td>
                <td width="250" valign="top" align="right" id="titl=
e"><p>Verify your new OPA account</p></td>
              </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>
          <td id="verificationMsg">
            <p>To verify your email address, please use the following One T=
ime Password (OTP):</p>
            <p class="otp">{{$data['otp']}}</p>
          </td>
        </tr>

        <tr>
          <td id="accountSecurity">
            <p>Do not share this OTP with anyone. OPA takes your account=
 security very seriously. OPA Customer Service will never ask you to dis=
close or verify your OPA password, OTP, credit card, or banking account =
number. If you receive a suspicious email with a link to update your accoun=
t information, do not click on the link=E2=80=94instead, report the email t=
o OPA for investigation. </p>
          </td>
        </tr>

        <tr>
          <td id="closing">
            <p>Thank you!</p>
          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table>
<img width="1" height="1" src="" /></body>
</html>
