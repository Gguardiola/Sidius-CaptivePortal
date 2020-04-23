<!DOCTYPE html>

<html lang="es">
<head>
	<title><?php print($config['captivename']); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="login/images/favicon.ico"/>
</head>
<body>
    <section>




<?php

if(isset($_POST["email"]) and $_GET["plan"] == NULL){
    if($_POST["email"] == NULL or $_POST["password"] == NULL or $_POST["passwordConfirm"] == NULL){
        $error = '<p style="background-color: red;color:white">Please, fill all the fields!</p>';
        include "register.php";
    }

    else{

        if($_POST["password"] == $_POST["passwordConfirm"]){
            if(strlen($_POST["password"]) < 6){
                $error = "La clave debe tener al menos 6 caracteres";
                include "register.php";
            }
            elseif(strlen($_POST["password"]) > 16){
                $error = "La clave no puede tener más de 16 caracteres";
                include "register.php";
            }
            elseif (!preg_match('`[a-z]`',$_POST["password"])){
                $error = "La clave debe tener al menos una letra minúscula";
                include "register.php";
            }
            elseif (!preg_match('`[A-Z]`',$_POST["password"])){
                $error = "La clave debe tener al menos una letra mayúscula";
                include "register.php";
            }
            elseif (!preg_match('`[0-9]`',$_POST["password"])){
                $error = "La clave debe tener al menos un caracter numérico";
                include "register.php";
            }
            else{
                $newuser_data = [];
                array_push($newuser_data,$_POST["email"],$_POST["password"]);
                include "register.php";
            }

        }
        else{
            $error = '<p style="background-color: red;color:white">The password and its confirmation do not match! </p>';
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
else{
    header("location:register.php");

}

?>

