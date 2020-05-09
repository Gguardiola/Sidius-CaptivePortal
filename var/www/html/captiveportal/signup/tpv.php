<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Redirecting to the payment gateway...</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">

    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="../static/googlefonts.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css" />
    <link rel="stylesheet" href="../static/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
    #view-source {
      position: fixed;
      display: block;
      right: 0;
      bottom: 0;
      margin-right: 40px;
      margin-bottom: 40px;
      z-index: 900;
    }
    </style>
  </head>
  <body>
    <div class="demo-layout mdl-layout mdl-layout--fixed-header mdl-js-layout mdl-color--grey-100">
      <header class="demo-header mdl-layout__header mdl-layout__header--scroll mdl-color--grey-100 mdl-color-text--grey-800">
        <div class="mdl-layout__header-row">
          <span class="mdl-layout-title"><?php print($config['captivename']); ?></span>
          <div class="mdl-layout-spacer"></div>
        </div>
      </header>
      <div class="demo-ribbon"></div>
      <main class="demo-main mdl-layout__content">
        <div align=center class="demo-container mdl-grid">
          <div class="mdl-cell mdl-cell--2-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
          <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--8-col">
            
                <?php



                  /////////////////////////////////////////////////////////////
                  /////////////// PAYMENT GATEWAY CONFIGURATION  //////////////
                  /////////////////////////////////////////////////////////////

                    //if $plan exists it means that the user selected a plan and confirmed the privacy policy and TOS.
                    if(isset($plan)){
                      //loading the API
                      include "redsys-api/apiRedsys.php";  
                      $myObj = new RedsysAPI;
                      //payment gateway for testing
                      $url_tpv = 'https://sis-t.redsys.es:25443/sis/realizarPago';

                      //payment gateway for production
                      //$url_tpv = 'https://sis.redsys.es/sis/realizarPago';
                      $version = "HMAC_SHA256_V1"; 
                      //this key is provided by the bank (in this case is a testing key)
                      $key = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';

                      //$name is the name of the commerce. In this case gets the captive portal name from the config.php
                      $name = $config['captivename'];

                      //testing merchant code and terminal
                      $code = '999008881';
                      $terminal='001';

                      //the order code is a combination of the current time without format and the letter of the selected plan
                      $order= time();

                      ////////// PLAN CONFIGURATION //////////
                      if($plan == "STANDARD"){
                          $price="1";
                          $order = $order."-S";

                          //$securityquery is made to prevent users from inserting data into the DB without going through the register.php
                        
                          $securityquery = "SELECT email from $config[db_tableauth] where email = '$email'";

                          $sql = mysqli_query($db, $securityquery);
                          $data = mysqli_fetch_row($sql);    

                          //if the user doesn't exist, it will insert all the user data as UNPAID until the payment gateway returns the verification.
                          if($data == NULL){
                            $query = "INSERT into $config[db_tableauth] values('$order','$email', SHA2('$password',512),'STANDARD',now(),TIMESTAMP('9999-00-00',  '00:00:00'),'UNPAID');";

                            $sql = mysqli_query($db, $query);
                            $data = mysqli_fetch_row($sql);                 
                          }
                          else{
                            header("location:../index.php");

                          }
                      }
                      elseif($plan == "PRO"){
                          $price="3";
                          $order = $order."-P";

                          $securityquery = "SELECT email from $config[db_tableauth] where email = '$email'";

                          $sql = mysqli_query($db, $securityquery);
                          $data = mysqli_fetch_row($sql);    

                          if($data == NULL){
                            $query = "INSERT into $config[db_tableauth] values('$order','$email', SHA2('$password',512),'PRO',now(),TIMESTAMP('9999-00-00',  '00:00:00'),'UNPAID');";
                            $sql = mysqli_query($db, $query);
                            $data = mysqli_fetch_row($sql);  
                          }
                          else{
                            header("location:../index.php");

                          }                                                                                              

                      }

                      $amount=$price*100;

                      //more API settings...
                      $currency = '978';
                      $consumerlng = '002';
                      $transactionType = '0';

                      //if the captive portal have Https, the payment gateway will redirect the user through this URLs instead http
                      if($_SERVER['HTTPS'] == "on"){
                        //payment verification
                        $urlMerchant = 'https://'.$config['domain'].'/signup/bd-notification.php';
                        //payment success
                        $urlweb_ok = 'https://'.$config['domain'].'/signup/bd-notification.php';
                        //payment failed
                        $urlweb_ko = 'https://'.$config['domain'].'/signup/payment-nook.php';


                      }
                      else{
                        $urlMerchant = 'http://'.$config['domain'].'/signup/bd-notification.php';
                        $urlweb_ok = 'http://'.$config['domain'].'/signup/bd-notification.php';
                        $urlweb_ko = 'http://'.$config['domain'].'/signup/payment-nook.php';
                      }

                      //custom concept of the payment
                      $concept = "CAPTIVE PORTAL ".$plan." PLAN - ".$email;

                      //API stuff
                      $myObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
                      $myObj->setParameter("DS_MERCHANT_CURRENCY",$currency);
                      $myObj->setParameter("DS_MERCHANT_ORDER",$order);
                      $myObj->setParameter("DS_MERCHANT_MERCHANTCODE",$code);
                      $myObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
                      $myObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$transactionType);
                      $myObj->setParameter("DS_MERCHANT_MERCHANTURL",$urlMerchant);
                      $myObj->setParameter("DS_MERCHANT_URLOK",$urlweb_ok);      
                      $myObj->setParameter("DS_MERCHANT_URLKO",$urlweb_ko);
                      $myObj->setParameter("DS_MERCHANT_MERCHANTNAME",$name); 
                      $myObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE",$consumerlng); 
                      $myObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION",$concept); 
  


                      $params = $myObj->createMerchantParameters();
                      $signature = $myObj->createMerchantSignature($key);
                    }
                    else{
                      header("location:../index.php");

                    }
                ?>
                    <!--PAYMENT GATEWAY BUTTON-->
                    <img style="max-width: 100%;max-height: 100%;" src="images/paymentcard.png"><br>
                    <form id="realizarPago" action="<?php echo $url_tpv; ?>" method="post" target="_self">
                        <input type='hidden' name='Ds_SignatureVersion' value='<?php echo $version; ?>'> 
                        <input type='hidden' name='Ds_MerchantParameters' value='<?php echo $params; ?>'> 
                        <input type='hidden' name='Ds_Signature' value='<?php echo $signature; ?>'> 
                        <button  name="submitPayment" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SECURE PAYMENT</button>
                    </form>
                    <br>
                    <a href="../index.php" class="btn btn-link">Go to login page</a>
            </div>

        </div>

    </div>
    <script src="../static/material.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
