<?php
# Parse config file and fetch database auth.
# We continue with the database open from index.php

# Speed Cap IS UNSTABLE but works for limiting bandwith.
# If you change those values FLUSH CONCESSIONS

# Medium Speed
#~150KB/s
$free = 5;

# High Speed
#~650kbps - 1MB/s
$premium = 10;

# High Speed x2
#~1.0MB/s - 2MB/s
$premium1 = 20;

# Ultra High Speed
$admin = 100;

//iptables concession logger
function loggerCONCESSION($status,$MAC){
  

  $now = date("Y-m-d H:i:s");


  $logline = $status . " | " . $now . " | " . "MAC: " . $MAC;

  $myfile = file_put_contents('/var/log/captiveportal.log', $logline.PHP_EOL , FILE_APPEND | LOCK_EX);

}
# Block Direct Access to this website.
if(!$config) {
  die("<font size='10'>Direct access to this website is not allowed.</font>");
}

# Fetch MAC of IP Address.
$MAC = exec("arp -n | grep '$_SERVER[REMOTE_ADDR] ' | tr -s '' ' ' | cut -f3 -d ' ' ");

function get_date($config) {
  $date['start'] = exec("date -u +%Y-%m-%dT%H:%M:%S");
  $date['stop_free'] = exec("date -u +%Y-%m-%dT%H:%M:%S -d '+$config[free_time]'");
  $date['stop_premium'] = exec("date -u +%Y-%m-%dT%H:%M:%S -d '+$config[premium_time]'");
  $date['stop_premium1'] = exec("date -u +%Y-%m-%dT%H:%M:%S -d '+$config[premium1_time]'");
  $date['stop_admin'] = exec("date -u +%Y-%m-%dT%H:%M:%S -d '+$config[admin_time]'");
  return $date;
}

$date = get_date($config);
# Free Users rules to apply.
if ($roleuser == "FREE") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_free] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $free/sec -m time --datestart $date[start] --datestop $date[stop_free] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");

  $status = "CREATED FREE CONCESSION SUCCESSFULY";
  loggerCONCESSION($status,$MAC);
  session_set_cookie_params(1800);
  session_start();
  $_SESSION['user'] = [$email,$roleuser];
}
# Premium Users rule to apply.
elseif ($roleuser == "STANDARD") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_premium] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $premium/sec -m time --datestart $date[start] --datestop $date[stop_premium] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");

  $status = "CREATED STANDARD CONCESSION SUCCESSFULY";
  loggerCONCESSION($status,$MAC);
  session_set_cookie_params(14400);
  session_start();
  $_SESSION['user'] = [$email,$roleuser];
}
# Premium Users 1 rule to apply.
elseif ($roleuser == "PRO") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_premium1] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $premium1/sec -m time --datestart $date[start] --datestop $date[stop_premium1] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");

  $status = "CREATED PRO CONCESSION SUCCESSFULY";
  loggerCONCESSION($status,$MAC);
  session_set_cookie_params(28800);
  session_start();
  $_SESSION['user'] = [$email,$roleuser];
}

# Admin Users rule to apply.
elseif ($roleuser == "administrator") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_admin] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $admin/sec -m time --datestart $date[start] --datestop $date[stop_admin] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  # Everything is done at this point, Go Control Panel.
  # Starting session to not reset variable config on cpanel self-post
  # Session duration
  session_set_cookie_params(86400);
  session_start();

  $status = "CREATED ADMIN CONCESSION SUCCESSFULY";
  loggerCONCESSION($status,$MAC);
  
  $_SESSION['config'] = $config;
  header("location:../index.php");
}
?>
