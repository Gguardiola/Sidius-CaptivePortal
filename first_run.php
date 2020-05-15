<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Captive portal - First Run</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="First Run">


    <link rel="stylesheet" href="static/googlefonts.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="static/material.indigo-pink.min.css" />
	<link rel="stylesheet" href="static/styles.css">
	<link rel="stylesheet" href="static/first_run.css">
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
          <span class="mdl-layout-title">Captive portal - First Run</span>
          <div class="mdl-layout-spacer"></div>
        </div>
      </header>
      <div class="demo-ribbon"></div>
      <main class="demo-main mdl-layout__content">
        <div align=center class="demo-container mdl-grid">

          <div class="mdl-cell mdl-cell--2-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
          <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--8-col">
            <br>
            <br>

            <section class="mdl-cell mdl-cell--8-col">
              <div>
              <h1>Captive Portal - First run</h1>
                  
  
			  <?php
					$config = include "config.php";

					

                  //////////////////////////////////////////////
                  ////////// FIRST PAGE: DATABASE SETUP ////////
                  //////////////////////////////////////////////

					function print_page_db_setup($error) {
						print('
						<h5 style="opacity:0.6">Fill all the fields to have the portal ready to be used.<h5>
						<h5>Initial Configuration 1/5 - Database Setup</h5>
						');
						if(isset($error)){
						  print('
						  <br>    
						  <br>  
						  <p class=" alert-danger">' . $error. '</p>');
				  
				  
						}
						print(' 
						<br>    
						<br>  

						<div >
							<form method="POST" action="first_run.php?page=1">
								<h6>Database name</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="db_name" class="mdl-textfield__input" type="text" name="db_name">
									<label class="mdl-textfield__label" for="db_name">Example: captiveportal</label>
								</div>
								<h6>Database address</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="db_server" class="mdl-textfield__input" type="text" name="db_server">
									<label class="mdl-textfield__label" for="db_server">Example: 127.0.0.1</label>
								</div>
								<h6>Database username</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="db_username" class="mdl-textfield__input" type="text" name="db_username">
									<label class="mdl-textfield__label" for="db_username">Example: root</label>
								</div>
								<h6>Database password</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="db_password" class="mdl-textfield__input" type="password" name="db_password">
									<label class="mdl-textfield__label" for="db_password">Example: trustno1</label>
								</div>
								<h6>Login table name</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="db_tableauth" class="mdl-textfield__input" type="text" name="db_tableauth">
									<label class="mdl-textfield__label" for="db_tableauth">Example: login</label>
								</div>								
								
								<br>									
								<button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">CONTINUE</button>
							</form>


							</div>
						</div>						
						');
					}


					
                  ////////////////////////////////////////////////////
                  ////////// SECOND PAGE: ADMIN ACCOUNT SETUP ////////
                  ////////////////////////////////////////////////////
					
					function print_page_login_setup($error) {
						print('
						<h5 style="opacity:0.6">Fill all the fields to have the portal ready to be used.<h5>
						<h5>Initial Configuration 2/5 - Admin account setup</h5>
						');
						if(isset($error)){
						  print('
						  <br>    
						  <br>  
						  <p class=" alert-danger">' . $error. '</p>');
				  
				  
						}
						print(' 
						<br>    
						<br>  

						<div >
							<form method="POST" action="first_run.php?page=2">
								<h6>Admin Username</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="admin_username" class="mdl-textfield__input" type="text" name="admin_username">
									<label class="mdl-textfield__label" for="admin_username">Example: admin</label>
								</div>
								<h6>Admin password</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="admin_password" class="mdl-textfield__input" type="password" name="admin_password">
									<label class="mdl-textfield__label" for="admin_password">Example: trustno1</label>
								</div>
								<h6>Admin email</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="admin_mail" class="mdl-textfield__input" type="email" name="admin_mail">
									<label class="mdl-textfield__label" for="admin_mail">Example: example@example.com</label>
								</div>
								
								<br>									
								<button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">CONTINUE</button>
							</form>


							</div>
						</div>						
						'); 
					}

                  ////////////////////////////////////////////////////
                  ////////// THIRD PAGE: FIREWALL SETUP //////////////
                  ////////////////////////////////////////////////////					
					
					function print_page_firewall_setup($error) {
						print('
						<h5 style="opacity:0.6">Fill all the fields to have the portal ready to be used.<h5>
						<h5>Initial Configuration 3/5 - Firewall setup</h5>
						');
						if(isset($error)){
						  print('
						  <br>    
						  <br>  
						  <p class=" alert-danger">' . $error. '</p>');
				  
				  
						}
						print(' 
						<br>    
						<br>  

						<div >
							<form method="POST" action="first_run.php?page=3">
								<h6>Internal interface</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="internal_int" class="mdl-textfield__input" type="text" name="internal_int">
									<label class="mdl-textfield__label" for="internal_int">Example: enp0s8</label>
								</div>
								<h6>Internal interface IP address</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="internal_ip" class="mdl-textfield__input" type="text" name="internal_ip">
									<label class="mdl-textfield__label" for="internal_ip">Example: 192.168.1.1</label>
								</div>	
								<h6>WiFi External interface</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="external_int" class="mdl-textfield__input" type="text" name="external_int">
									<label class="mdl-textfield__label" for="external_int">Example: enp0s3</label>
								</div>	
								<h6>External subnet</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="external_subnet" class="mdl-textfield__input" type="text" name="external_subnet">
									<label class="mdl-textfield__label" for="external_subnet">Example: 10.110.0.0/16</label>
								</div>
								<h6>External default gateway</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="external_gateway" class="mdl-textfield__input" type="text" name="external_gateway">
									<label class="mdl-textfield__label" for="external_gateway">Example: 10.110.0.1</label>
								</div>
								');
								$handle = fopen("setupTemplates/usingDNSfamilyFriendly", "r");
								$response = fgets($handle);
								fclose($handle);

								if($response == "yes"){
									print('<input type="hidden" value="208.67.222.123" name="dnsforwarder1">');
									print('<input type="hidden" value="208.67.220.123" name="dnsforwarder2">');
								}
								else{

									print('	
									<h6>Primary DNS forwarder</h6>
									<div class="mdl-textfield mdl-js-textfield">
										<input style="margin-bottom:9px" id="dnsforwarder1" class="mdl-textfield__input" type="text" value="8.8.8.8" name="dnsforwarder1">
										<label class="mdl-textfield__label" for="dnsforwarder1">Example: 8.8.8.8</label>
									</div>
									<h6>Secondary DNS forwarder</h6>
									<div class="mdl-textfield mdl-js-textfield">
										<input style="margin-bottom:9px" id="dnsforwarder2" class="mdl-textfield__input" type="text" value="8.8.8.8" name="dnsforwarder2">
										<label class="mdl-textfield__label" for="dnsforwarder2">Example: 8.8.4.4</label>
									</div> ');					
								}

								print('
									<br>									
									<button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">CONTINUE</button>
									</form>


										</div>
									</div>						
								');
						
					}

                  //////////////////////////////////////////////////////////
                  ////////// FOURTH PAGE: CUSTOMIZATION SETUP //////////////
                  //////////////////////////////////////////////////////////					
									

					function print_page_customize_setup($error) {
						print('
						<h5 style="opacity:0.6">Fill all the fields to have the portal ready to be used.<h5>
						<h5>Initial Configuration 4/5 - Customization setup</h5>
						');
						if(isset($error)){
						  print('
						  <br>    
						  <br>  
						  <p class=" alert-danger">' . $error. '</p>');
				  
				  
						}
						print(' 
						<br>    
						<br>  

						<div >
							<form method="POST" action="first_run.php?page=4">
								<h6>Captive portal name</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="captivename" class="mdl-textfield__input" type="text" name="captivename">
									<label class="mdl-textfield__label" for="captivename">Example: El Prat Airport WiFi</label>
								</div>
								<h6>Login file location</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="loginphp_file" class="mdl-textfield__input" type="text" value="login/login.php" name="loginphp_file">
									<label class="mdl-textfield__label" for="loginphp_file" >Example: login/login.php</label>
								</div>
								<h6>Captive portal domain name</h6>
								<div class="mdl-textfield mdl-js-textfield">
									<input style="margin-bottom:9px" id="domain" class="mdl-textfield__input" type="text"  name="domain">
									<label class="mdl-textfield__label" for="domain" >Example: elPratAirport.webredirect.org</label>
								</div>								
								<br>									
								<button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">CONTINUE</button>
							</form>


							</div>
						</div>						
						');
						
					}


                  ///////////////////////////////////////////////////////////////
                  ////////// FIFTH PAGE:CAPTIVE PORTAL LOGO SETUP //////////////
                  //////////////////////////////////////////////////////////////					
									


					function print_page_image_setup($error) {
						print('
						<h5 style="opacity:0.6">Fill all the fields to have the portal ready to be used.<h5>
						<h5>Initial Configuration 5/5 - Captive portal logo</h5>
						');
						if(isset($error)){
						  print('
						  <br>    
						  <br>  
						  <p class=" alert-danger">' . $error. '</p>');
				  
				  
						}
						print(' 
						<br>    
						<br>  

						<div align=center>
							<form  action="first_run.php?page=5" method="post" enctype="multipart/form-data">
							
							<br>
							<div style="text-align: center;text-justify: inter-word;">
								<ul>
									<li style="list-style-type:circle">The logo image must be named as <strong>logo.png</strong></li>
									<li style="list-style-type:circle">The recommended dimensons are <strong>350x210</strong></li>
								<ul>
								
							</div>
							<br>
							<br>
							<h6>Select the captive portal logo</h6>
							<input style="margin-left:100px" type="file" name="logo" id="logo">
							<br>
							<br>
							<br>									
							<button style="padding-left:46px;padding-right:46px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" name="submit_logo" type="submit">Finish</button>
							</form>

							</div>
						</div>						
						');
						
					}										

					if (!isset($_GET['page'])) {
						print_page_db_setup($error);
					}

				  ////////// FIRST PAGE: DATABASE SETUP HANDER //////////////
				  
					if ($_GET['page'] == "1") {
						# If variable $_POST[....] are empty go back, go try again.
						if ($_POST['db_name'] == NULL and $_POST['db_tableauth'] == NULL and $_POST['db_username'] == NULL and $_POST['db_server'] == NULL) {
							print_page_db_setup($error);
						}
						# If there are missing parameters.
						elseif ($_POST['db_name'] == "" or $_POST['db_tableauth'] == "" or $_POST['db_username'] == "" or $_POST['db_server'] == "") {
							$error = "There are missing fields in the form. Please check them and try again.";
							print_page_db_setup($error);
						}

						# If variables are right go check database.
						elseif (isset($_POST['db_name']) and (isset($_POST['db_tableauth'])) and (isset($_POST['db_server'])) and (isset($_POST['db_username']))) {
							$db = mysqli_connect($_POST['db_server'], $_POST['db_username'], $_POST['db_password']);
							# Error connecting to database, print it and go back to configuration.
							if (!$db) {
								$error = mysqli_connect_error();
								print_page_db_setup($error);
						}
							# We have connection to database, so update config.php
							else {
								# Sending new config to the config.php
								$config['db_name'] = "$_POST[db_name]";
								$config['db_server'] = "$_POST[db_server]";
								$config['db_tableauth'] = "$_POST[db_tableauth]";
								$config['db_username'] = "$_POST[db_username]";
								$config['db_password'] = "$_POST[db_password]";
								file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
								# Now, make database and tables.
								$query = "CREATE DATABASE $_POST[db_name];";
								$query .= "USE $_POST[db_name];";
								$query .= "CREATE TABLE $_POST[db_tableauth] (order_ID varchar(13) not null, email varchar(70), password char(128), role varchar(25), creation_date datetime, last_login datetime, payment_status varchar(25));";
							$sql = mysqli_multi_query($db, $query);
								# Everything is done at this point. Go Phase 2.
								header("Location: first_run.php?page=2");
							}
						}
					}

				  ////////// SECOND PAGE: ADMIN ACCOUNT SETUP HANDER //////////////


					elseif ($_GET['page'] == "2") {
						# First login
						if (!isset($_POST['admin_username']) or !isset($_POST['admin_password']) or !isset($_POST['admin_mail'])) {
							print_page_login_setup($error);
						}
						# If username or password is empty
						elseif ($_POST['admin_username'] == "" or $_POST['admin_password'] == "" or $_POST['admin_mail'] == "") {
							$error = "Username or Password is missing. Please try again.";
							print_page_login_setup($error);
						}
						else {
							# Data should be good
							$db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
							$query = "INSERT INTO $config[db_tableauth] (order_ID,email,password,role,creation_date,last_login,payment_status) VALUES ('ADMIN','$_POST[admin_username]',SHA2('$_POST[admin_password]',512),'administrator',NOW(),NOW(),'ADMIN')";
							$sql = mysqli_query($db, $query);
							$config['admin_mail'] = "$_POST[admin_mail]";
							file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
							# Everything is done at this point. Go Phase 3.
								header("Location: first_run.php?page=3");
						}
					}

				  ////////// THIRD PAGE: FIREWALL SETUP HANDER //////////////


					elseif ($_GET['page'] == "3") {
							# First login

							if (!isset($_POST['internal_int'])or !isset($_POST['external_subnet']) or !isset($_POST['external_int'])or !isset($_POST['internal_ip'])or !isset($_POST['external_gateway'])or !isset($_POST['dnsforwarder1'])or !isset($_POST['dnsforwarder2'])) {
								print_page_firewall_setup($error);
							}
							# If parameters are empty.
							elseif ($_POST['internal_int'] == "" or $_POST['external_subnet'] == "" or $_POST['external_int'] == "" or $_POST['internal_ip'] == "" or $_POST['external_gateway'] == "" or $_POST['dnsforwarder1'] == "" or $_POST['dnsforwarder2'] == "") {
								$error = "Some parameters are missing. Please try again.";
								print_page_firewall_setup($error);
							}
							else {
								# Data should be good
								$config['internal_int'] = "$_POST[internal_int]";
								$config['internal_ip'] = "$_POST[internal_ip]";
								$config['external_int'] = "$_POST[external_int]";
								$config['external_gateway'] = "$_POST[external_gateway]";
								$config['external_subnet'] = "$_POST[external_subnet]";
								$config['dnsforwarder1'] = "$_POST[dnsforwarder1]";
								$config['dnsforwarder2'] = "$_POST[dnsforwarder2]";
								file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
								# Everything is done at this point. Go Phase 3.
								header("Location: first_run.php?page=4");
							}
						}


				  ////////// FOURTH PAGE: CUSTOMIZATION SETUP HANDER //////////////


						elseif ($_GET['page'] == "4") {


							if (!isset($_POST['loginphp_file']) or !isset($_POST['captivename']) or !isset($_POST['domain'])) {
								print_page_customize_setup($error);
							}
							
							elseif ($_POST['loginphp_file'] == "" or $_POST['captivename'] == "" or $_POST['domain'] =="") {
								$error = "Some parameters are missing. Please try again.";
								print_page_customize_setup($error);
							}
							else {
								# Data should be good
								$config['loginphp_file'] = "$_POST[loginphp_file]";
								$config['captivename'] = "$_POST[captivename]";
								$config['domain'] = "$_POST[domain]";
								file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
								header("Location: first_run.php?page=5");

							}
						}

						
				  ////////// FIFTH PAGE: CAPTIVE PORTAL LOGO SETUP HANDER //////////////



						elseif ($_GET['page'] == "5") {

							if (!isset($_FILES['logo'])) {
								print_page_image_setup($error);
							}

							if(isset($_FILES['logo'])){
				
								$target_dir = "login/images/";
								$target_file = $target_dir . basename($_FILES["logo"]["name"]);
								$uploadOk = 1;
								$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
								if($_FILES['logo']['name'] != "logo.png"){
									$error = "The logo image must be named as <strong>logo.png</strong>";
									print_page_image_setup($error);

								}
								
								elseif(isset($_POST["submit_logo"])) {
									$path = $_SERVER['DOCUMENT_ROOT'].'/login/images/logo.png';
									var_dump($path);
									unlink($path);
									$check = getimagesize($_FILES["logo"]["tmp_name"]);
									move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
									header("location: first_run.php?page=finished");

								}
								
							}	
						}	
						
						
				  //////////  SUCCESSFUL PAGE //////////////


						elseif($_GET['page'] == "finished"){
							print('
							<img style="max-width: 100%;max-height: 100%;" src="signup/images/tickpayment.png"><br>
							<h2>SETUP DONE!</h2><br>
							<p>Now you can log in, surf the internet and monitorize the captive portal throught the cpanel!</p>
							<a href="index.php" class="btn btn-link">Go to login page</a>								
						
						
						
						');		


						}
			?>

			</div>
        	</div>

    </div>
    <script src="static/material.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>




