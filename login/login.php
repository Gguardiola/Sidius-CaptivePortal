<!DOCTYPE html>

<html lang="es">
<head>
	<title><?php print($config['captivename']); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="login/images/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="login/login.css"/>
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">

				<div class="login100-pic">

					<img src="login/images/logo.png">
				</div>

				<form class="login100-form" method="POST" action="index.php">

					<span class="login100-form-title">

						<b>WI-FI Login</b>
					</span>

					<div class="wrap-input100">
						<input class="input100" type="text" name="email" placeholder="Email">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</form>
					<form class="login100-form" method="POST" action="register.php">
						<button class="login100-form-btn-2">
							Create new account (WIP)
						</button>
					</form>
					<div class="error_msg">
						<b><?php if(isset($error)) {
												print($error); } ?></b>
					</div>
					</div>
			</div>
		</div>
	</div>
</body>
</html>
