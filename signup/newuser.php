<?php

//Include the config file
$config = include "../config.php";

                //////////////////////////////////////////////////////////////////////
                /////////// FIRST PAGE: EMAIL, PASSWORD AND CAPTCHA CHECKER //////////
                //////////////////////////////////////////////////////////////////////

//if email POST exists it means that the user submited the first form
if(isset($_POST["email"])){

    //if one of these fields is empty, it will return and error to the register.php
    if($_POST["email"] == NULL or $_POST["password"] == NULL or $_POST["passwordConfirm"] == NULL or  $_POST['captcha'] == NULL){
        $error = '<p align=center class="alert alert-danger">Please, fill all the fields!</p>';
        include "register.php";
    }
    else{
        //password and password confirmation checker
        if($_POST["password"] == $_POST["passwordConfirm"]){
            //starting the session for the captcha checker
            session_start();
            
            /////////////// PASSWORD POLICY ///////////////

            
            if(strlen($_POST["password"]) < 6){
                $error = "<p align=center class='alert alert-danger'>The password must be at least 6 characters long.</p>";
                include "register.php";
            }
            elseif(strlen($_POST["password"]) > 16){
                $error = "<p align=center class='alert alert-danger'>The password can't be more than 16 characters long.</p>";
                include "register.php";
            }
            elseif (!preg_match('`[a-z]`',$_POST["password"])){
                $error = "<p align=center class='alert alert-danger'>The password must contain at least one lowercase letter.</p>";
                include "register.php";
            }
            elseif (!preg_match('`[A-Z]`',$_POST["password"])){
                $error = "<p align=center class='alert alert-danger'>The password must contain at least one uppercase letter.</p>";
                include "register.php";
            }
            elseif (!preg_match('`[0-9]`',$_POST["password"])){
                $error = "<p align=center class='alert alert-danger'>The password must contain at least one numeric character.</p>";
                include "register.php";
            }

            //comparing the captcha with the captcha sended by the user
            elseif($_SESSION['captcha'] != $_POST['captcha']){
                $error = "<p align=center class='alert alert-danger'>The captcha was incorrect. Try again!</p>";
                include "register.php";        

            }

            //if the captcha and the password policy is OK. Then is time to check if the user email is already in the DB
            else{
                unset($_SESSION["captcha"]);
                $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
                if (!$db) {
                    #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
                    $error = "<p align=center class='alert alert-danger'>ERROR: Database is not available.</p>";
                  }
                  # Database is up, querying...
                  else {                
                    $email = mysqli_real_escape_string($db, $_POST['email']);
                    $query = "SELECT * FROM $config[db_tableauth] WHERE email ='$email'";
                    $sql = mysqli_query($db, $query);
                    $data = mysqli_fetch_row($sql);

                    //if the query returns NULL it means that the user email is not in the DB
                    if($data == NULL){
                        //the $newuser_data array gets the email and password and finally sended to the next sign up page
                        $newuser_data = [];
                        array_push($newuser_data,$_POST["email"],$_POST["password"]);
                        include "register.php";
                    }
                    else{
                        $error = "<p align=center class='alert alert-danger'>A user with this email address already exists!</p>";
                        include "register.php";

                    }
                  }

            }

        }
        else{
            $error = "<p align=center class='alert alert-danger'>The password and its confirmation do not match! </p>";
            include "register.php";                    
        }

        

    }
}


                  ///////////////////////////////////////////////////////////////////
                  /////////////// SECOND PAGE: PLAN SELECTION CHECKER ///////////////
                  ///////////////////////////////////////////////////////////////////

//if the user selects one of the plans, it will be stored in the $plan variable and sended to register.php
elseif(isset($_POST["free"])){
    $plan = "FREE";
    include "register.php";
}
elseif(isset($_POST["standard"])){
    $plan = "STANDARD";
    include "register.php";
}
elseif(isset($_POST["pro"])){
    $plan = "PRO";
    include "register.php";
}

                  ////////////////////////////////////////////////////////////////////////////
                  /////////// THIRD PAGE: PRIVACY POLICY AND TOS AGREEMENT CHECKER ///////////
                  ////////////////////////////////////////////////////////////////////////////


//if the POST contract value is "agree" it means that the user checked the privacy policy and TOS confirmation                  
elseif($_POST['contract'] == "agree"){
    $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
    # In case of error connecting send client back to login.
    if (!$db) {
      #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
      print("ERROR: Database is not available.");
    }
    # Database is up, querying...
    
    else {
        $email = mysqli_real_escape_string($db, $_POST['username']);
        $password = mysqli_real_escape_string($db, $_POST['password']);
        $plan = $_POST["plan_concept"];
        $order = time()."-F";


        //if the value of $plan is FREE, it will directly insert the user data in the DB ready to login.
        if($plan == "FREE"){
            $query = "INSERT into $config[db_tableauth] values('$order','$email', SHA2('$password',512),'FREE',now(),TIMESTAMP('9999-00-00',  '00:00:00'),'FREE')";
            $sql = mysqli_query($db, $query);
            $data = mysqli_fetch_row($sql);    

            header("location:../index.php");

        }

        //if the plan is not FREE, it will be redirected to the payment gateway page (tpv.php)
        else{
            print('
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
                    <meta name="apple-mobile-web-app-title" content="Redirecting to the payment gateway...">
                
                    <link rel="shortcut icon" href="images/favicon.png">
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
                        <span class="mdl-layout-title">'.$config['captivename'].'</span>
                        <div class="mdl-layout-spacer"></div>
                        </div>
                    </header>
                    <div class="demo-ribbon"></div>
                    <main class="demo-main mdl-layout__content">
                        <div align=center class="demo-container mdl-grid">
                        <div class="mdl-cell mdl-cell--2-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
                        <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--8-col">                          
               
            ');
                                            include "tpv.php";



            print('


                </div>
                <script src="../static/material.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
            </body>
            </html>

            
            
            
            
            ');
        }
    }

    

}

//if the POST contract doesn't exists, it will not continue
elseif(!isset($_POST['contract']) and isset($_POST["username"]) and isset($_POST["password"])){
$plan = $_POST['plan_concept'];
$error = "<p align=center class='alert alert-danger'>You need to accept the Privacy Policy and Terms of Service to create the account. </p>";
include "register.php";

}

//if someone tries to ilegally enter to some specific page of the sign up handler, it will be redirected to the login page
else{
    header("location:../index.php");

}

?>

