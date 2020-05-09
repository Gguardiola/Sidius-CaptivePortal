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

            include "tpv.php";
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

