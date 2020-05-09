<?php

# Parse config file and load html file.
# Starting session so if we miss email/password we go back to loginphp_file with error.
$config = include "config.php";


//this function will write into the log file if the login attempt was successful or not. also will store the browser and SO client info (user agent)
function loggerAUTH($status,$mac,$userinfo){
  
  $userinfo = strtolower($userinfo);
  $now = date("Y-m-d H:i:s");

  while(true){
   if(strpos($userinfo, 'linux')){
    $so = "Linux";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 10')){
    $so = "Windows 10";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 6.3')){
    $so = "Windows 8.1";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 6.2')){
    $so = "Windows 8";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 6.1')){
    $so = "Windows 7";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 6.0')){
    $so = "Windows Vista";
    break;
   }
   elseif(strpos($userinfo, 'windows nt 5.1') or strpos($userinfo, 'windows xp')){
    $so = "Windows XP";
    break;
   }
   elseif(strpos($userinfo, 'iphone')){
    $so = "iPhone";
    break;
   }
   elseif(strpos($userinfo, 'ipad')){
    $so = "iPad";
    break;
   }
   elseif(strpos($userinfo, 'android')){
    $so = "Android";
    break;
   }
   elseif(strpos($userinfo, 'blackberry')){
    $so = "Blackberry";
    break;
   }
   elseif(strpos($userinfo, 'macintosh|mac os x')){
    $so = "Mac OS X";
    break;
   }
   elseif(strpos($userinfo, 'mac_powerpc')){
    $so = "Mac OS 9";
    break;
   }
   elseif(strpos($userinfo, 'webos')){
    $so = "WebOS";
    break;
   }
   else{
    $so = "Unknown";
    break;  

   }

  }

  while(true){
    if(strpos($userinfo, 'firefox')){
     $browser = "Firefox";
     break;
    }
    elseif(strpos($userinfo, 'msie')){
      $browser = "Internet Explorer";
      break;
    }
    elseif(strpos($userinfo, 'safari')){
      $browser = "Safari";
      break;
    }
    elseif(strpos($userinfo, 'chrome')){
      $browser = "Chrome";
      break;
    }
    elseif(strpos($userinfo, 'edge')){
      $browser = "Edge";
      break;
    }
    elseif(strpos($userinfo, 'opera')){
      $browser = "Opera";
      break;
    }
    elseif(strpos($userinfo, 'mobile')){
      $browser = "Mobile Browser";
      break;
    }
    else{
      $browser = "Unknown";
      break;
    }

 
   }

  $logline = $status . " | " . $now . " | " . "MAC: " . $mac . " | " . "BROWSER: " . $browser . " | " . "SO: " . $so;

  $myfile = file_put_contents('/var/log/captiveportal.log', $logline.PHP_EOL , FILE_APPEND | LOCK_EX);

}







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
    $query = "SELECT email,password,role,payment_status,creation_date FROM $config[db_tableauth] WHERE lower(email)='$email' AND password=SHA2('$password',512)";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);

    # if data[0] doesn't exist because returns NULL then login failed.
    if (!$data[0]) {
      $error = "<p align=center class='alert alert-danger'> Invalid email or password.</p>";

      $status = "LOGIN FAILED";
      $mac = exec("arp -n | grep '$_SERVER[REMOTE_ADDR] ' | tr -s '' ' ' | cut -f3 -d ' ' ");
      $userinfo = $_POST['userinfo'];
      loggerAUTH($status,$mac,$userinfo);

    }
    # if data[0] exists its because login successful and db found role.
    if ($data[0]) {
      if($data[3] == "UNPAID"){
        $error = "<p align=center class='alert alert-danger'> The payment status of your ".$data[2]." account is UNPAID. Please change it to FREE or pay your plan clicking the button below. YOU HAVE 7 DAYS COUNTING FROM ".$data[4]." TO RENEW!. If you think this is a problem contact with the captive portal administrator <strong>".$config['admin_mail']."</strong></p>";
        include $config['loginphp_file'];
      }
      else{
        # Update into entry last_login value
        $query = "UPDATE $config[db_tableauth] SET last_login=NOW() WHERE email='$data[0]' AND password='$data[1]'";
        $sql = mysqli_query($db, $query);
        # Everything is done at this point. Go Firewall.
        $roleuser = $data[2];


        $status = "LOGIN SUCCESS";
        $mac = exec("arp -n | grep '$_SERVER[REMOTE_ADDR] ' | tr -s '' ' ' | cut -f3 -d ' ' ");
        $userinfo = $_POST['userinfo'];
        loggerAUTH($status,$mac,$userinfo);


        include "cpanel/iptables.php";
      }
    }
  }
}

# First login attempt don't show error
elseif (!isset($_POST['email']) or !isset($_POST['password'])) {
}

# If there is missing data.
elseif ($_POST['email'] == "" or $_POST['password'] == "") {
  $error = "<p align=center class='alert alert-danger'>Email or password is missing.<br> Try again.</p>";
}

# Redirect to login only if we didn't succesfully logged.
if (!isset($data)) {
    include $config['loginphp_file'];
}
?>
