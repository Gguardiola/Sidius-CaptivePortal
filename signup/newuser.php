<?php

$config = include "../config.php";

if(isset($_POST["email"])){
    if($_POST["email"] == NULL or $_POST["password"] == NULL or $_POST["passwordConfirm"] == NULL or  $_POST['captcha'] == NULL){
        $error = '<p align=center class="alert alert-danger">Please, fill all the fields!</p>';
        include "register.php";
    }
    else{

        if($_POST["password"] == $_POST["passwordConfirm"]){
            session_start();

       
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
            elseif($_SESSION['captcha'] != $_POST['captcha']){
                $error = "<p align=center class='alert alert-danger'>The captcha was incorrect. Try again!</p>";
                include "register.php";        

            }
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

                    if($data == NULL){

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
elseif(isset($_POST["free"])){
    $todo_ok = "free";
    include "register.php";
}
elseif(isset($_POST["standard"])){
    print($_POST['username']);
    print($_POST['password']);
    print("standard");
    
}
elseif(isset($_POST["pro"])){
    print($_POST['username']);
    print($_POST['password']);
    print("pro");
    
}
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
        $query = "INSERT into $config[db_tableauth] values(NULL,'$email', SHA2('$password',512),'free',now(),TIMESTAMP('9999-00-00',  '00:00:00'))";
        $sql = mysqli_query($db, $query);
        $data = mysqli_fetch_row($sql);    
        header("location:http://google.es/");
    }

    

}
elseif(!isset($_POST['contract']) and isset($_POST["username"]) and isset($_POST["password"])){
$todo_ok = "free";
$error = "<p align=center class='alert alert-danger'>You need to accept the privacy policy and terms of service to create the account. </p>";
include "register.php";

}
else{
    header("location:register.php");

}

?>

