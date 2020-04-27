<?php
# Parse config file and fetch database auth.
# We continue with the database open from index.php

# Speed Cap IS UNSTABLE but works for limiting bandwith.
# If you change those values FLUSH CONCESSIONS

# Medium Speed
$free = 4;

# High Speed
$premium = 10;

# High Speed x2
$premium1 = 20;

# Ultra High Speed
$admin = 100;

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
  $date['stop_admin'] = exec("date -u +%Y-%m-%dT%H:%M:%S -d '+$config[admin_time]'");
  return $date;
}

$date = get_date($config);
# Free Users rules to apply.
if ($roleuser == "free") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_free] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $free/sec -m time --datestart $date[start] --datestop $date[stop_free] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");
}
# Premium Users rule to apply.
elseif ($roleuser == "premium") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_premium] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $premium/sec -m time --datestart $date[start] --datestop $date[stop_premium] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");
}
# Premium Users 1 rule to apply.
elseif ($roleuser == "premium") {
  exec("sudo iptables -t nat -I PREROUTING 2 -i $config[internal_int] -m mac --mac-source $MAC -m time --datestart $date[start] --datestop $date[stop_premium] -j ACCEPT");
  exec("sudo iptables -A FORWARD -m mac --mac-source $MAC -m limit --limit $premium1/sec -m time --datestart $date[start] --datestop $date[stop_premium] ! -d $config[external_subnet] -i $config[internal_int] -o $config[external_int] -j ACCEPT");
  header("Location: https://google.es/");
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
  $_SESSION['config'] = $config;
  include "cpanel.php";
}
?>
