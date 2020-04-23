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
        <div>
        <h2>Sign Up</h2>
        <p>Create your account. It only takes a minute.<p>        
        <?php
           if(!isset($newuser_data) and !isset($todo_ok)){
            print('
            <p>Page 1/3 - User information</p>
            <form method ="POST" action ="newuser.php">
                <input type="email" name="email" placeholder="example@example.com"><br>
                <input type="password" name="password" placeholder="Password"><br>
                <input type="password" name="passwordConfirm" placeholder="Confirm password"><br>
                <input type="submit" value="Sign Up">
            </form>





            ');
            
                if(isset($error)){
                    print($error);
                
                }
            }


            if(isset($newuser_data) and !isset($todo_ok)){
                print('
                <p>Page 2/3 - Select your plan</p>
                <div style="margin-right:20px;border: 3px solid black;width:300px;height:300px;display:inline-block">
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
                        <input type="submit" name="free" value="SELECT PLAN">
                    </form>
                    </div>
                </div>

                <div style="margin-right:20px;border: 3px solid black;width:300px;height:300px;display:inline-block">
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
                            <input type="submit" name="standard" value="SELECT PLAN">
                        </form>
                    </div>
                </div>
                
                <div style="margin-right:20px;border: 3px solid black;width:300px;height:300px;display:inline-block;vertical-align:top">
                    <div align=center>
                    <h3>PRO</h3>

                    <p>Choose this plan if you only want to access the internet for important or business stuff and you want more speed.<p>
                    <br>
                    <h2>2,99€<h2>
                    <br>
                    <form method="POST" action="newuser.php">
                        <input type="hidden" name="username" value="'.$newuser_data[0].'">
                        <input type="hidden" name="password" value="'.$newuser_data[1].'">
                        <input type="submit" name="pro" value="SELECT PLAN">

                    </form>
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
                    <p>You selected the <strong>FREE</strong> plan. You can change this in the future through the login page.</p>
                    <form method="POST" action"newuser.php">
                    <input type="checkbox" name="contract">
                    <label>I confirm that i have read, consent and agree with the <a href="/privacy.php">Privacy Policy</a> and <a href="/terms.html">Terms of Service</a>.</label>

                    </form>



                    ');
                }


            }
                    
                
        ?>
        </div>
    </section>
</body>
</html>