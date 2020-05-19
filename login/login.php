
<!-- THIS FILE IS HANDLED BY INDEX.PHP -->
<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title><?php print($config['captivename']); ?></title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?php print($config['captivename']); ?>">


    <link rel="stylesheet" href="../static/googlefonts.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../static/material.indigo-pink.min.css" />
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
            session_start();
            if(isset($_SESSION['config'])){
              print('
              <form method ="POST" action ="cpanel/cpanel.php">                  
              <div align=right>
                <button style="padding-left:40px;padding-right:40px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">CPANEL</button>
              </div>
              </form>              
              
              ');


            }
          ?>
            <br>
            <br>
        <section class="mdl-cell mdl-cell--4-col">

        <div>
          <h1>Wi-Fi login</h1>
          <img align=center style="max-width: 100%;max-height: 100%" src="login/images/logo.png">					
          <h5 style="opacity:0.6">You need to login at <strong><?php print($config['captivename']); ?></strong> to access the internet<h5>        
          <br>
            <?php
              //if the newuser.php detects an error from the form above (like empty field or wrong captcha) it will print the specific error
              if(isset($error)){
                
                print('
                <div align=center class="container">
                    '.$error.'
                </div>
                
                
                ');
              
              }
            
              ?>		
          <br>			
          <div align=center>
            <form method ="POST" action ="index.php">                  
            <div class="mdl-textfield mdl-js-textfield">
              
              <input style="margin-bottom:9px" id="email" class="mdl-textfield__input" type="text" name="email">
              <label class="mdl-textfield__label" for="email">example@example.com</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield">
              <input style="margin-bottom:9px" id="password" class="mdl-textfield__input" type="password" name="password" >
              <label class="mdl-textfield__label" for="password">Password</label>
            </div>
              <input type="hidden" name="userinfo" value="<?php echo $_SERVER['HTTP_USER_AGENT']?>">
              <button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Login</button>

            
            </form>
            <?php if(isset($_SESSION['user'])){
              print('
              <form method ="POST" action ="../signup/accountSettings.php">                  
              <div style="margin-top:15px">
              <button style="padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">ACCOUNT SETTINGS</button>
  
              </div>
              </form>              

              ');


            }?>
            <form method ="POST" action ="../signup/register.php">                  
            <div style="margin-top:15px">

              <input class="btn btn-link" type="submit" value="Don't have account?">
            </div>
            </form>

          </div>



        
        </div>
			</section>
        </div>

    </div>
    <footer class="mdl-mega-footer">
 
      <div class="mdl-mega-footer__bottom-section">
        <div class="mdl-logo"><?php print($config['captivename']); ?> - Made by JGTek (SIDIUS Captive Portal PROJECT)</div>
        <ul class="mdl-mega-footer__link-list">
          <li><a href="/licenses.php">Licenses</a></li>
          <li><a href="/privacy.php">Privacy Policy</a></li>
        </ul>
      </div>

    </footer>    
    <script src="../static/material.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
