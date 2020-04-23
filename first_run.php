<?php
$config = include "config.php";

# First Page (SQL Setup)
function print_page_db_setup($error) {
	print('
	<html lang="es">
		<head>
			<title>CaptivePortal Setup 1/4</title>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="icon" type="image/png" href="login/images/favicon.ico"/>
			<link rel="stylesheet" type="text/css" href="first_run.css"/>
		</head>
		<body>
			<div class="limiter">
				<div class="container-login100">
					<div class="wrap-login100">
						<form class="login100-form" method="POST" action="first_run.php?page=1">
							<span class="login100-form-title">
								<b>Captive Portal<br>Initial Configuration 1/4</b><br><b>SQL Setup</b>
							</span>

							<div class="wrap-input100">
								<span class="margin"><b>Name of the Database:</b></span>
								<input class="input100" type="text" name="db_name" placeholder="Example: captiveportal">
								<span class="focus-input100"></span>
							</div>

							<div class="wrap-input100">
								<span class="margin"><b>MySQL/MariaDB IP:</b></span>
								<input class="input100" type="text" name="db_server" placeholder="Example: 127.0.0.1">
								<span class="focus-input100"></span>
							</div>

							<div class="wrap-input100">
								<span class="margin"><b>Database access username:</b></span>
								<input class="input100" type="text" name="db_username" placeholder="Example: captive">
								<span class="focus-input100"></span>
							</div>

							<div class="wrap-input100">
								<span class="margin"><b>Database access password:</b></span>
								<input class="input100" type="password" name="db_password" placeholder="Example: foobar">
								<span class="focus-input100"></span>
							</div>

							<div class="wrap-input100">
								<span class="margin"><b>Name of the table in the SQL where users will be login into:</b></span>
								<input class="input100" type="text" name="db_tableauth" placeholder="Example: login">
								<span class="focus-input100"></span>
							</div>

							<div class="container-login100-form-btn">
							</form>
							<button class="login100-form-btn-2">
								Create Database
							</button>
							<div class="error_msg"><b>' .
							$error . '</b>
							</div>
							</div>
					</div>
				</div>
			</div>
		</body>
		</html>');
}

# Second Page (User Setup)
function print_page_login_setup($error) {
	print('
	<html lang="es">
		<head>
			<title>CaptivePortal Setup 2/4</title>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="icon" type="image/png" href="login/favicon.ico"/>
			<link rel="stylesheet" type="text/css" href="first_run.css"/>
		</head>
		<body>
			<div class="limiter">
				<div class="container-login100">
					<div class="wrap-login100">
						<form class="login100-form" method="POST" action="first_run.php?page=2">
							<span class="login100-form-title">
								<b>Captive Portal</b><br><b>Initial Configuration 2/4</b><br><b>Admin Setup</b>
							</span>
							<div class="wrap-input100">
								<span class="margin"><b>Admin Username:</b></span>
								<input class="input100" type="text" name="admin_username" placeholder="Example: admin">
								<span class="focus-input100"></span>
							</div>
							<div class="wrap-input100">
								<span class="margin"><b>Admin Password:</b></span>
								<input class="input100" type="password" name="admin_password" placeholder="Example: foobar">
								<span class="focus-input100"></span>
							</div>
							<div class="container-login100-form-btn">
							</form>
							<button class="login100-form-btn-2">
								Create Admin Account
							</button>
							<div class="error_msg"><b>' .
							$error . '</b>
							</div>
							</div>
					</div>
				</div>
			</div>
		</body>
		</html>');
}

# Third Page (Firewall Setup)
function print_page_firewall_setup($error) {
	print('
	<html lang="es">
		<head>
			<title>CaptivePortal Setup 3/4</title>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="icon" type="image/png" href="login/favicon.ico"/>
			<link rel="stylesheet" type="text/css" href="first_run.css"/>
		</head>
		<body>
			<div class="limiter">
				<div class="container-login100">
					<div class="wrap-login100">
						<form class="login100-form" method="POST" action="first_run.php?page=3">
							<span class="login100-form-title">
								<b>Captive Portal</b><br><b>Initial Configuration 3/4</b><br><b>Admin Setup</b>
							</span>
							<div class="wrap-input100">
								<span class="margin"><b>WIFI Internal Interface Name:</b></span>
								<input class="input100" type="text" name="internal_int" placeholder="Example: enp0s3">
								<span class="focus-input100"></span>
							</div>
							<div class="wrap-input100">
								<span class="margin"><b>External Interface Name:</b></span>
								<input class="input100" type="text" name="external_int" placeholder="Example: eth0">
								<span class="focus-input100"></span>
							</div>
							<div class="wrap-input100">
								<span class="margin"><b>Internal Subnet Range:</b></span>
								<input class="input100" type="text" name="external_subnet" placeholder="Example: 10.110.0.0/16">
								<span class="focus-input100"></span>
							</div>
							<div class="container-login100-form-btn">
							</form>
							<button class="login100-form-btn-2">
								Apply network configuration
							</button>
							<div class="error_msg"><b>' .
							$error . '</b>
							</div>
							</div>
					</div>
				</div>
			</div>
		</body>
		</html>');
}

if (!isset($_GET['page'])) {
	print_page_db_setup($error);
}

# First Step (Database)
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
			$query .= "CREATE TABLE $_POST[db_tableauth] (id serial, email varchar(70), password char(128), role varchar(25), creation_date datetime, last_login datetime);";
    	$sql = mysqli_multi_query($db, $query);
			# Everything is done at this point. Go Phase 2.
			header("Location: first_run.php?page=2");
		}
	}
}

# Second Step (Login)
elseif ($_GET['page'] == "2") {
	# First login
	if (!isset($_POST['admin_username']) or !isset($_POST['admin_password'])) {
		print_page_login_setup($error);
	}
	# If username or password is empty
	elseif ($_POST['admin_username'] == "" or $_POST['admin_password'] == "") {
		$error = "Username or Password is missing. Please try again.";
		print_page_login_setup($error);
	}
	else {
		# Data should be good
		$db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
		$query = "INSERT INTO $config[db_tableauth] (email,password,role,creation_date,last_login) VALUES ('$_POST[admin_username]',SHA2('$_POST[admin_password]',512),'administrator',NOW(),NOW())";
		$sql = mysqli_query($db, $query);
		# Everything is done at this point. Go Phase 3.
			header("Location: first_run.php?page=3");
	}
}

# Third Page (FirewalL)
elseif ($_GET['page'] == "3") {
		# First login
		if (!isset($_POST['internal_int']) or !isset($_POST['external_int'])) {
			print_page_firewall_setup($error);
		}
		# If parameters are empty.
		elseif ($_POST['internal_int'] == "" or $_POST['external_int'] == "") {
			$error = "Some parameters are missing. Please try again.";
			print_page_firewall_setup($error);
		}
		else {
			# Data should be good
			$config['internal_int'] = "$_POST[internal_int]";
			$config['external_int'] = "$_POST[external_int]";
			$config['external_subnet'] = "$_POST[external_subnet]";
			file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
			# Everything is done at this point. Print Status
			die("<font size='24'><center> You can now login </center></font>");

		}
	}
