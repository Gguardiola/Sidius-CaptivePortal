<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Account Settings</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Account Settings">


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
        <div class="demo-container mdl-grid">
          <div class="mdl-cell mdl-cell--2-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
          <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--8-col">
          <h1 align=center>Account Settings</h1>
          <?php 
            $config = include "../config.php";
            //starting session and splitting it to get the user and the role separately
            session_start(); $email = $_SESSION['user'][0]; $role = $_SESSION['user'][1];


            /////// PAYMENT STATUS CHECKER ///////


            //if the payment status is UNPAID. the upgrade button its gonna display the user plan so he can finish the purchase
            $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
            # In case of error connecting send client back to login.
            if (!$db) {
              #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
              print("ERROR: Database is not available.");
            }
            # Database is up, querying...
            
            else {
                $query = "SELECT payment_status from $config[db_tableauth] where email = '$email'";
                $sql = mysqli_query($db, $query);
                $paymentChecker = mysqli_fetch_row($sql);   
                $paymentChecker = $paymentChecker[0];
            }
     
            
                  ////////////////////////////////////////////////
                  ///////////////MAIN PAGE: BUTTONS///////////////
                  ///////////////////////////////////////////////


            //this page checks if the user is actually logged. After that, it will display the settings buttons.
            if(isset($_SESSION['user'])){
                //if there is not action, redirect to action=main
                if($_GET['action'] == NULL){
                    header("Location: accountSettings.php?action=main");
                }

                if($_GET['action'] == "main"){
                    print('

                    <br>
                    <h5 style="opacity:0.6"><strong>User logged: </strong>'.$email.'<h5>
                    <h5 style="opacity:0.6"><strong>Current plan: </strong>'.$role.'<h5>
                    ');

                    if($paymentChecker == "UNPAID"){
                        print('<h5 style="opacity:0.6;color:#d8573b"><strong>PLAN '.$paymentChecker.'</strong><h5>');


                    }

                    print('
                    <br>
                    <form style="display:inline-block;margin-right:10px;" method ="POST" action ="accountSettings.php?action=logout">                  
                        <button style="padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">LOG OUT</button>
                    </form>
                    
                    <form style="display:inline-block;margin-right:10px;" method ="POST" action ="accountSettings.php?action=upgrade">                  
                        <button style="padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">UPGRADE PLAN</button>
                    </form>
                  
                    <form style="display:inline-block;margin-top:10px;" method ="POST" action ="accountSettings.php?action=changepass">                  
                        <button style="padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">CHANGE PASSWORD</button>
                    </form>
                    <br>
                    <br>
                    <h5 style="color:#d8573b">Danger zone</h5>
                    <hr style="border: solid #d8573b 1px;">
                    <div>
                    <form method ="POST" action ="accountSettings.php?action=delete">                  
                        <button  style="background-color:#d8573b;padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">DELETE ACCOUNT</button>
                    </form>
                    </div>
                    ');
                }



                  /////////////// LOGOUT PAGE //////////////


                elseif($_GET['action'] == "logout"){
                    session_destroy();
                    unset($_SESSION['user']);
                    //INCLUDING THE IPTABLES CONCESSION REMOVER
                    $logout = true;
                    include "../cpanel/iptablesUserHandler.php";
                    header("location: ../index.php");                


                }


                  ///////////////////////////////////////////////////////////
                  ///////////////UPGRADE PAGE: PLAN SELECTION///////////////
                  /////////////////////////////////////////////////////////

                //the same that register.php and newuser.php. The user needs to choose his new plan

                //if the payment status is unpaid, the user can click on the same plan, otherwise the button will be DISABLED

                //if the user plan its better than FREE, the free button will be DISABLED too
                elseif($_GET['action'] == "upgrade"){
                    //FREE PLAN
                    if(isset($_POST["free"])){
                        $email = $_POST['username'];
                        $order = time()."-F";

                        $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
                        # In case of error connecting send client back to login.
                        if (!$db) {
                          #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
                          print("ERROR: Database is not available.");
                        }
                        # Database is up, querying...
                        
                        else {
                            $query = "UPDATE $config[db_tableauth] SET order_ID = '$order' where email = '$email'";
                            $sql = mysqli_query($db, $query);
                            $data = mysqli_fetch_row($sql);   

                            $query = "UPDATE $config[db_tableauth] SET payment_status = 'FREE' where email = '$email'";
                            $sql = mysqli_query($db, $query);
                            $data = mysqli_fetch_row($sql);                     

                            $query = "UPDATE $config[db_tableauth] SET role = 'FREE' where email = '$email'";
                            $sql = mysqli_query($db, $query);
                            $data = mysqli_fetch_row($sql);  
                            

                            //SESSION UPDATE
                            //if the user finally changed his plan, we need to update the session so the Account Settings MAIN PAGE display the new plan.
                            $query = "SELECT role from $config[db_tableauth] where order_ID = '$order'";
                            $sql = mysqli_query($db, $query);
                            $sessionReload = mysqli_fetch_row($sql);  

                            $_SESSION['user'] = [$email,$sessionReload[0]];
                
                        }

                        header("location: accountSettings.php");
                    }

                    //STANDARD PLAN
                    elseif(isset($_POST["standard"])){
                        $email = $_POST['username'];
                        $upgrade = true;
                        $plan = "STANDARD";
                        include "tpv.php";
                    }
                    //PRO PLAN
                    elseif(isset($_POST["pro"])){
                        $email = $_POST['username'];
                        $upgrade = true;
                        $plan = "PRO";
                        include "tpv.php";
                    }

                    //PRINTING THE UPGRADE PLAN PAGE
                    else{
                        print('
                        <div align=center>
                            <h4>Select your new plan</h4>
                            <br>

                            <div style="width:auto;-webkit-box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);-moz-box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);box-shadow: inset -4px -2px 25px -2px rgba(0,0,0,0.41);height: 490px;overflow-x: scroll;overflow-y:hidden;border-top: 2px solid #494bb2;border-bottom: 2px solid #494bb2;border-left:2px solid #d4d4d4;border-right:2px solid #d4d4d4">
                            <div style="margin-top:10px;width:925px;">
                            <div style="display:inline-block;width:300px;height: 476px;padding:5%"class="card">
                                <div align=center>
                                    <h3>FREE</h3>

                                <p>Choose this plan if you only want to access the internet for casual stuff and you dont care about the speed.<p>
                                <br>

                                <h2>0€<h2>
                                <br>
                                <form method="POST" action="accountSettings.php?action=upgrade">
                                    <input type="hidden" name="username" value="'.$email.'">
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
                                    <form method="POST" action="accountSettings.php?action=upgrade">
                                        <input type="hidden" name="username" value="'.$email.'">
                                        ');
                                        if($role == "STANDARD" and $paymentChecker != "UNPAID" or $role == "PRO" and $paymentChecker != "UNPAID"){
                                            print('<button type="submit" name="standard" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" disabled>SELECT PLAN</button>');
        
                                        }
                                        else{
                                            print('<button type="submit" name="standard" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SELECT PLAN</button>');
        
                                        }
                                        print('

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
                                <form method="POST" action="accountSettings.php?action=upgrade">
                                    <input type="hidden" name="username" value="'.$email.'">
                                    ');
                                    if($role == "PRO" and $paymentChecker != "UNPAID"){
                                        print('<button type="submit" name="pro" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" disabled>SELECT PLAN</button>');
    
                                    }
                                    else{
                                        print('<button type="submit" name="pro" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">SELECT PLAN</button>');
    
                                    }
                                    print('
                                </form>
                                </div>
                            </div>
                            </div>
                            </div>
                            <br>
                            <a href="accountSettings.php" class="btn btn-link">Go back</a>
                        </div>

                    
                        ');
                    }


                }


                  ////////////////////////////////////////////////////////
                  ///////////////CHANGE PASSWORD PAGE ////////////////////
                  ////////////////////////////////////////////////////////


                elseif($_GET['action'] == "changepass"){
                    if(isset($_POST["oldpassword"])){

                        //if one of these fields is empty, it will return and error to the accountSettings.php?action=changepass
                        if($_POST["oldpassword"] == NULL or $_POST["password"] == NULL or $_POST["passwordConfirm"] == NULL){
                            $error = '<p align=center class="alert alert-danger">Please, fill all the fields!</p>';
                            print($error);
                        }
                        else{
                            //password and password confirmation checker
                            $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
                            if (!$db) {
                                #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
                                $error = "<p align=center class='alert alert-danger'>ERROR: Database is not available.</p>";
                                print($error);
                            }
                              # Database is up, querying...
                            else {                
                                $oldpass = mysqli_real_escape_string($db, $_POST['oldpassword']);
                                $query = "SELECT * FROM $config[db_tableauth] WHERE password = SHA2('$oldpass',512)";
                                $sql = mysqli_query($db, $query);
                                $data = mysqli_fetch_row($sql);
            
                                //if the query returns NULL it means that the old password was typed wrong
                                if($data == NULL){
                                    $error = '<p align=center class="alert alert-danger">Incorrect old password!</p>';
                                    print($error);
                                }
                                else{

                                    if($_POST["password"] == $_POST["passwordConfirm"]){

                                
                                        /////////////// PASSWORD POLICY ///////////////
                            
                                        
                                        if(strlen($_POST["password"]) < 6){
                                            $error = "<p align=center class='alert alert-danger'>The password must be at least 6 characters long.</p>";
                                            print($error);
                                        }
                                        elseif(strlen($_POST["password"]) > 16){
                                            $error = "<p align=center class='alert alert-danger'>The password can't be more than 16 characters long.</p>";
                                            print($error);
                                        }
                                        elseif (!preg_match('`[a-z]`',$_POST["password"])){
                                            $error = "<p align=center class='alert alert-danger'>The password must contain at least one lowercase letter.</p>";
                                            print($error);
                                        }
                                        elseif (!preg_match('`[A-Z]`',$_POST["password"])){
                                            $error = "<p align=center class='alert alert-danger'>The password must contain at least one uppercase letter.</p>";
                                            print($error);
                                        }
                                        elseif (!preg_match('`[0-9]`',$_POST["password"])){
                                            $error = "<p align=center class='alert alert-danger'>The password must contain at least one numeric character.</p>";
                                            print($error);
                                        }
                            
        
                            
                                        //if the password policy is OK. Its time to update the password
                                        else{
                                            $password = mysqli_real_escape_string($db, $_POST['password']);
                                            $query = "UPDATE $config[db_tableauth] set password = SHA2('$password',512) where email = '$email'";
                                            $sql = mysqli_query($db, $query);
                                            $data = mysqli_fetch_row($sql);

                                            $success ='<p class="alert alert-info">The password has been successfuly updated!</p>';
                                            print($success);
                           
                                        }
                            
                                    }
                                    else{
                                        $error = "<p align=center class='alert alert-danger'>The password and its confirmation do not match! </p>";
                                        print($error);                    
                                    }
                            

                                }
                            }               
                    
                        }
                    }  

                        //PRINTING THE CHANGE PASSWORD FORM PAGE
                        print('
                        <div align=center>
                        <form method ="POST" action ="accountSettings.php?action=changepass">                  
                            <div class="mdl-textfield mdl-js-textfield">
                                <input style="margin-bottom:9px" id="oldpassword" class="mdl-textfield__input" type="password" name="oldpassword">
                                <label class="mdl-textfield__label" for="oldpassword">Old password</label>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield">
                                <input style="margin-bottom:9px" id="password" class="mdl-textfield__input" type="password" name="password">
                                <label class="mdl-textfield__label" for="password">New password</label>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield">
                                <input style="margin-bottom:9px" id="passwordConfirm" class="mdl-textfield__input" type="password" name="passwordConfirm">
                                <label class="mdl-textfield__label" for="passwordConfirm">Confirm new password</label>
                            </div>

                                
                                <button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Change Password</button>
    
                        </form>
                        <br>
                        <a href="accountSettings.php" class="btn btn-link">Go back</a>
                        </div>
                        <br>
                        <br>

    
    
                        ');


                }


                  ////////////////////////////////////////////////////////////////
                  ///////////////DELETE ACCOUNT CONFIRMATION PAGE ///////////////
                  ///////////////////////////////////////////////////////////////

                elseif($_GET['action'] == "delete"){
                    print('
                    
                    <h5 style="color:#d8573b">Are you sure?</h5>
                    <br>
                    <form style="display:inline-block;margin-right:10px;" method ="POST" action ="accountSettings.php?action=delete_yes">                  
                        <button style="background-color:#d8573b;padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">Continue</button>
                    </form>
                    
                    <form style="display:inline-block" method ="POST" action ="accountSettings.php?action=main">                  
                        <button style="background-color:#d8573b;padding-left:15px;padding-right:15px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">Go back</button>
                    </form>
                    
                    
                    ');
                }

                ////// DELETE ACCOUNT: TRIGGER ///////
                elseif($_GET['action'] == "delete_yes"){

                    $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
                    if (!$db) {
                        print('<br><div align=center class="container">ERROR: Database is not available.</div><br>');

                    }

                    else{
                        $delete_user = mysqli_real_escape_string($db, $email);
                        $query = "DELETE FROM $config[db_tableauth] WHERE email='$email'";
                        $commit = mysqli_query($db, $query);
                        
                        header("location: accountSettings.php?action=logout ");
                    }
                }

            }
            else{
                header("location: ../index.php"); 


            }
            ?>
        </div>

    </div>
    <script src="../static/material.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>