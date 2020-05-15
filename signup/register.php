
<!--Include the config file-->
<?php $config = include "../config.php"; ?>



<!-- THIS FILE IS HANDLED BY NEWUSER.PHP -->

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
    /*block the captcha from being copied*/
    #captchaContainer{
    -webkit-user-select: none;
        -moz-user-select: -moz-none;
          -ms-user-select: none;
              user-select: none;
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
                //start the session to save the generated captcha
                session_start();
                //declaration of the function that will generate the captcha from the recived length
                function randomText($length) {
                  $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
                  
                  for($i=0;$i<$length;$i++) {
                      $key .= $pattern{rand(0,35)};
                  }
                  return $key;
                }
                
              ///////////////////////////////////////////////
              ///////////// EMAIL AND PASSWORD FORM /////////
              ///////////////////////////////////////////////

                $captcha= randomText(8);
                //the session gets the captcha value
                $_SESSION['captcha'] = $captcha;   
                //if these variables do not exist, it means that you have accessed to the sign up page for the first time. register.php will show the first page.
                if(!isset($newuser_data) and !isset($plan) and !isset($_POST["contract"])){

                  //this form is sended via POST to newuser.php
                  print('
                  <h4>Page 1/3 - User information</h4>
                  <br>
                  ');

                  //if the newuser.php detects an error from the form above (like empty field or wrong captcha) it will print the specific error
                  if(isset($error)){
  
                    print('
                    <div align=center class="container">
                          '.$error.'
                    </div>
                    
                    
                    ');
                
                }

                print('
                  <div align=center>
                    <form method ="POST" action ="newuser.php">                  
                      <div class="mdl-textfield mdl-js-textfield">
                          <input style="margin-bottom:9px" id="email" class="mdl-textfield__input" type="email" name="email">
                          <label class="mdl-textfield__label" for="email">example@example.com</label>
                      </div>
                      <div class="mdl-textfield mdl-js-textfield">
                          <input style="margin-bottom:9px" id="password" class="mdl-textfield__input" type="password" name="password">
                          <label class="mdl-textfield__label" for="password">Password</label>
                      </div>
                      <div class="mdl-textfield mdl-js-textfield">
                          <input style="margin-bottom:9px" id="passwordConfirm" class="mdl-textfield__input" type="password" name="passwordConfirm">
                          <label class="mdl-textfield__label" for="passwordConfirm">Confirm password</label>
                      </div>
                      <label id=captchaContainer class="alert alert-dark" for="captcha">'.$captcha.'</label>
                      
                      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label ">
                          
                          <input id="captcha" style="margin-bottom:9px" class="mdl-textfield__input" type="text" name="captcha">
                          <label class="mdl-textfield__label " for="captcha">Type the letters you see above.</label>        
                          
                      </div>
                          
                          <button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Next page</button>

                    </form>
                  </div>
                  <br>
                  <br>



                ');

                }
                  
                //////////////////////////////////////////
                ///////////// PLAN SELECTION FORM ////////
                //////////////////////////////////////////

                  //if $newuser_data exists and $plan doesn't exists it means that the user has already entered the email and the password correctly, but still no plan selected. register.php will show the second page.
                  if(isset($newuser_data) and !isset($plan)){
                    //the form sends via POST the plan, email and the password for insert it into the login table
                      print('
                      <h4>Page 2/3 - Select your plan</h4>
                      <br>

                      <div style="margin-left: -4%;-webkit-box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);-moz-box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);width: 317px;height: 490px;overflow-x: scroll;overflow-y:hidden">
                      <div style="margin-top:10px;width:925px;">
                        <div style="display:inline-block;width:300px;height: 476px;padding:5%"class="card">
                            <div align=center>
                                <h3>FREE</h3>

                            <p>Choose this plan if you only want to access the internet for casual stuff and you dont care about the speed.<p>
                            <br>

                            <h2>0€<h2>
                            <br>
                            <form method="POST" action="newuser.php">
                                <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                <input type="hidden" name="password" value="'.$newuser_data[1].'">                    
                                <button type="submit" name="free" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SELECT PLAN</button>
                            </form>
                            </div>
                        </div>

                        <div style="display:inline-block;vertical-align:top;width:300px;height: 476px;padding:5%" class=" card">
                            <div align=center>
                                <h3>STANDARD</h3>

                                <p>Choose this plan if you only want to access the internet for casual stuff and you want more speed.<p>
                                <br>
                                <br>
                                <h2>1€<h2>
                                <br>
                                <form method="POST" action="newuser.php">
                                    <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                    <input type="hidden" name="password" value="'.$newuser_data[1].'">
                                    <button type="submit" name="standard" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SELECT PLAN</button>
                                </form>
                            </div>
                        </div>
                        
                        <div style="display:inline-block;vertical-align:top;width:300px;height: 476px;padding:5%" class="card">
                            <div align=center>
                            <h3>PRO</h3>

                            <p>Choose this plan if you only want to access the internet for important or business stuff and you want more speed.<p>
                            <br>
                            <h2>3€<h2>
                            <br>
                            <form method="POST" action="newuser.php">
                                <input type="hidden" name="username" value="'.$newuser_data[0].'">
                                <input type="hidden" name="password" value="'.$newuser_data[1].'">
                                <button type="submit" name="pro" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SELECT PLAN</button>

                            </form>
                            </div>
                        </div>
                      </div>
                      </div>
                      <br>

                  
                      ');

                  }


                  ////////////////////////////////////////////////////////////////////////////////////////////////
                  /////////////// PRIVACY POLICY AND TOS AGREEMENT AND REDIRECTION TO THE PAYMENT GATEWAY ////////
                  ////////////////////////////////////////////////////////////////////////////////////////////////


                  //if $plan exists it means that the user has selected a plan and also have an email and password. register.php will show the third page.
                  if(isset($plan)){

                    print('
                    <h4>Page 3/3 - Confirmation</h4>
                    <br>
                    <div style="text-align: justify;text-justify: inter-word;">                          
                      <p>You selected the <strong>'.$plan.'</strong> plan. You can change this in the future through the login page.</p>
                      ');
                      
                      //if the selected plan is FREE it will show that the user will be redirected to the login page.
                      if($plan == "FREE"){
                        print("<p>When you click on CREATE ACCOUNT, you will automatically redirected to the login page.</p>");

                        
                      }
                      //otherwise, if the selected plan is STANDARD or PRO, it will show that the user will be redirected to the payment gateway
                      else{
                        print("<p>When you click on CREATE ACCOUNT, you will automatically redirected to the payment gateway.</p>");
                      }
                      
                      if(isset($error)){
                        print('
                        <div align=center class="container">
                              '.$error.'
                        </div>
                        
                        
                        ');
              
                      }

                      //finally, it will send via POST the email, password and plan to be inserted into de database as UNPAID or FREE

                      //IMPORTANT: the UNPAID users they will be updated as PAID when the payment is completed and verified
                      print('
                      <form method="POST" action"newuser.php">
                          <input type="hidden" name="username" value="'.$_POST['username'].'">
                          <input type="hidden" name="password" value="'.$_POST['password'].'">                    
                          <input type="hidden" name="plan_concept" value="'.$plan.'">   
                          <label style ="display:inline" class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="contract">
                            <input  value ="agree" type="checkbox" name="contract" id="contract" class="mdl-checkbox__input">
                            <span class="mdl-checkbox__label"></span>
                          </label>                          
                          <small style ="display:inline" id="contractInfo" class="form-text text-muted">I confirm that i have read, consent and agree with the <a target="_blank" href="/privacy.php">Privacy Policy</a> and <a href="/terms.html">Terms of Service</a>.</small><br><br>
          
                          <button style="margin-left:24%" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">CREATE ACCOUNT</button>
                      </form>
                    </div>
                    <br>
                    <br>

                    ');
                  }
          
              ?>


          </div>
          <a href="../index.php" class="btn btn-link">Go to login page</a>
        </div>

    </div>
    <script src="../static/material.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
