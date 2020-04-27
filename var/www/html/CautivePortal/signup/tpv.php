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

    <link rel="stylesheet" href="signup-styles/googlefonts.css">
    <link rel="stylesheet" href="signup-styles/material-icons.css">
    <link rel="stylesheet" href="signup-styles/material.teal-red.min.css">
    <link rel="stylesheet" href="signup-styles/styles.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
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
                    if(isset($plan)){
                    include "redsys-api/apiRedsys.php";  
                    $miObj = new RedsysAPI;
                    $url_tpv = 'https://sis-t.redsys.es:25443/sis/realizarPago'; // PASARELA DE PRUEBAS
                    //$url_tpv = 'https://sis.redsys.es/sis/realizarPago'; // PASARELA DE PRODUCCIÓN
                    $version = "HMAC_SHA256_V1"; 
                    $clave = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';
                    $name = $config['captivename'];
                    $code = '999008881';
                    $terminal='001';
                    $order= "000001123";

                    if($plan == "STANDARD"){
                        $totalpedido="1";
                    }
                    elseif($plan == "PRO"){
                        $totalpedido="3";

                    }

                    $amount=$totalpedido*100;
                    $currency = '978';
                    $consumerlng = '001';
                    $transactionType = '0';


                    $url_portal = $_SERVER['REQUEST_URI'];
                    $url_parsed = parse_url($url_portal, PHP_URL_HOST);

                    $urlMerchant = $config['domain'];
                    $urlweb_ok = $config['domain'].'/';
                    $urlweb_ko = $config['domain'].'/signup/register.php';
                    $concepto = "CAPTIVE PORTAL ".$plan." PLAN - ".$email;

                    $miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
                    $miObj->setParameter("DS_MERCHANT_CURRENCY",$currency);
                    $miObj->setParameter("DS_MERCHANT_ORDER",$order);
                    $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$code);
                    $miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
                    $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$transactionType);
                    $miObj->setParameter("DS_MERCHANT_MERCHANTURL",$urlMerchant);
                    $miObj->setParameter("DS_MERCHANT_URLOK",$urlweb_ok);      
                    $miObj->setParameter("DS_MERCHANT_URLKO",$urlweb_ko);
                    $miObj->setParameter("DS_MERCHANT_MERCHANTNAME",$name); 
                    $miObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE",$consumerlng); 
                    $miObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION",$concepto); 
 


                    $params = $miObj->createMerchantParameters();
                    $signature = $miObj->createMerchantSignature($clave);
                    }
                    else{
                            header("location:register.php");

                    }
                ?>
                
                    <form id="realizarPago" action="<?php echo $url_tpv; ?>" method="post" target="_self">
                        <input type='hidden' name='Ds_SignatureVersion' value='<?php echo $version; ?>'> 
                        <input type='hidden' name='Ds_MerchantParameters' value='<?php echo $params; ?>'> 
                        <input type='hidden' name='Ds_Signature' value='<?php echo $signature; ?>'> 
                        <input class="btn btn-lg btn-primary btn-block" type="submit" name="submitPayment" value="GO TO SECURE PAYMENT GATEWAY" />
                    </form>

                    <a href="../login/login.php" class="btn btn-link">Go back</a>
            </div>

        </div>

    </div>
    <script src="../bootstrap/js/jquery-3.5.0.min.js"></script>    
    <script src="signup-styles/material.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>
  </body>
</html>
