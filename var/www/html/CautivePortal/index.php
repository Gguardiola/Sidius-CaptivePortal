<?php
# Parse config file and load html file.
# Starting session so if we miss email/password we go back to loginphp_file with error.
$config = include "config.php";

# Login Successful
if (isset($_POST['email']) and isset($_POST['password']) and $_POST['email'] != "" and $_POST['password'] != "") {
  $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);

  # In case of error connecting send client back to login.
  if (!$db) {
    #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
    $error = "ERROR: Database is not available.";
  }
  # Database is up, querying...
  else {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $query = "SELECT email,password,role FROM $config[db_tableauth] WHERE lower(email)='$email' AND password=SHA2('$password',512)";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);

    # if data[0] doesn't exist because returns NULL then login failed.
    if (!$data[0]) {
      $error = "ERROR: Invalid email or password.";
    }

    # if data[0] exists its because login successful and db found role.
    if ($data[0]) {
      # Update into entry last_login value
      $query = "UPDATE $config[db_tableauth] SET last_login=NOW() WHERE email='$data[0]' AND password='$data[1]'";
      $sql = mysqli_query($db, $query);
      # Everything is done at this point. Go Firewall.
      $roleuser = $data[2];
      include "iptables.php";
    }
  }
}

# First login attempt don't show error
elseif (!isset($_POST['email']) or !isset($_POST['password'])) {
}

# If there is missing data.
elseif ($_POST['email'] == "" or $_POST['password'] == "") {
  $error = "ERROR: Email or password is missing.<br> Try again.";
}

# Redirect to login only if we didn't succesfully logged.
if (!isset($data)) {
    include $config['loginphp_file'];
}
?>
