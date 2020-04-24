<?php $config = include "../config.php"; ?>
<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title><?php print($config['captivename']); ?></title>

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
            <div class="demo-crumbs mdl-color-text--grey-500">
              User information &gt; Select your plan &gt; Confirmation
            </div>
            <br>
            <br>
            <section class="mdl-cell mdl-cell--4-col">
              <div>
              <h1>Sign Up</h1>
              <h5 style="opacity:0.6">Create your account. It only takes a minute.<h5>        
              <?php

                  session_start();
                  #Funcion que genera el capcha a partir de una cadena de numeros y letras
                  function randomText($length) {
                  $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
                  #Recoge un valor random y lo añade al captcha hasta acabar la longitud
                  for($i=0;$i<$length;$i++) {
                      $key .= $pattern{rand(0,35)};
                  }
                  return $key;
                  }

                  #llama a la funcion para generar el captcha
                  $captcha= randomText(8);
                  #inicia la sesion captcha para poder pasarla a login y comparar con el post introducido por el usuario
                  $_SESSION['captcha'] = $captcha;     
                if(!isset($newuser_data) and !isset($todo_ok) and !isset($_POST["contract"])){
                  print('
                  <h4>Page 1/3 - User information</h4>
                  <br>
                  <div align=center>
                    <form method ="POST" action ="newuser.php">                  
                      <div>
                          <input class="form-control" type="email" name="email" placeholder="example@example.com"><br>
                          <input class="form-control" type="password" name="password" placeholder="Password"><br>
                          <input class="form-control" type="password" name="passwordConfirm" placeholder="Confirm password"><br>
                          <label class="alert alert-dark" for="captcha">'.$captcha.'</label><input class="form-control" type="text" name="captcha">
                          <small id="captchaInfo" class="form-text text-muted">Type the characters you see in the picture above.</small><br><br>
                          <input class="btn btn-primary" type="submit" value="Next page">

                      </div>
                    </form>
                  </div>
                  <br>
                  <br>



                  ');
                  
                      if(isset($error)){
                        
                          print('
                          <div align=center class="container">
                                '.$error.'
                          </div>
                          
                          
                          ');
                      
                      }
                  }


                  if(isset($newuser_data) and !isset($todo_ok)){
                      print('
                      <h4>Page 2/3 - Select your plan</h4>
                      <br>
                      <div>
                        <div style="margin-bottom:15%;padding:5%"class="card">
                            <div align=center>
                                <h3>FREE</h3>

                            <p>Choose this plan if you only want to access the internet for casual stuff and you dont care about the speed.<p>
                            <br>
                            <br>
                            <h2>0€<h2>
                            <br>
                            <form method="POST" action="newuser.php">
                                <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                <input type="hidden" name="password" value="'.$newuser_data[1].'">                    
                                <input type="submit" name="free" class="btn btn-primary btn-lg btn-block" value="SELECT PLAN">
                            </form>
                            </div>
                        </div>

                        <div style="margin-bottom:15%;padding:5%" class=" card">
                            <div align=center>
                                <h3>STANDARD</h3>

                                <p>Choose this plan if you only want to access the internet for casual stuff and you want more speed.<p>
                                <br>
                                <br>
                                <h2>0,99€<h2>
                                <br>
                                <form method="POST" action="newuser.php">
                                    <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                    <input type="hidden" name="password" value="'.$newuser_data[1].'">
                                    <input type="submit" name="standard" class="btn btn-primary btn-lg btn-block" value="SELECT PLAN">
                                </form>
                            </div>
                        </div>
                        
                        <div style="margin-bottom:15%;padding:5%" class="card">
                            <div align=center>
                            <h3>PRO</h3>

                            <p>Choose this plan if you only want to access the internet for important or business stuff and you want more speed.<p>
                            <br>
                            <h2>2,99€<h2>
                            <br>
                            <form method="POST" action="newuser.php">
                                <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                <input type="hidden" name="password" value="'.$newuser_data[1].'">
                                <input type="submit" name="pro" class="btn btn-primary btn-lg btn-block" value="SELECT PLAN">

                            </form>
                            </div>
                        </div>
                      </div>
                      <br>
                  
                  
                  
                      ');

                  }

                  if(isset($todo_ok)){
                      if($todo_ok == "free"){
                          print('
                          <p>Page 3/3 - Confirmation</p>
                          <br>
                          <div style="text-align: justify;text-justify: inter-word;">                          
                            <p>You selected the <strong>FREE</strong> plan. You can change this in the future through the login page.</p>
                            <p>When you click on CREATE ACCOUNT, you will automatically redirected to the login page</p>
                            <form method="POST" action"newuser.php">
                                <input type="hidden" name="username" value="'.$_POST['username'].'">
                                <input type="hidden" name="password" value="'.$_POST['password'].'">                    
                                <input style ="display:inline" type="checkbox" name="contract" value="agree">
                                <small style ="display:inline" id="contractInfo" class="form-text text-muted">I confirm that i have read, consent and agree with the <a target="_blank" href="/privacy.php">Privacy Policy</a> and <a href="/terms.html">Terms of Service</a>.</small><br><br>
                
                                <input class="btn btn-primary btn-lg btn-block" type="submit" value="CREATE ACCOUNT">
                            </form>
                          </div>
                          <br>
                          <br>

                          ');

                          if(isset($error)){
                            print('
                            <div align=center class="container">
                                  '.$error.'
                            </div>
                            
                            
                            ');
                          }
                      }
                  }
          
              ?>


          </div>
          <a href="../login/login.php" class="btn btn-link">Go back</a>
        </div>

    </div>
    <script src="signup-styles/material.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>
  </body>
</html>
