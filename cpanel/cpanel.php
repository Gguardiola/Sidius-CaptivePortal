<?php

//if there is not GET variable, redirect user to menu 1
if ($_GET['menu'] == NULL) {
  header("Location: cpanel.php?menu=1");

}
#starting session for checking if the access is legal or not
session_start();

if (!isset($_SESSION['config'])) {
  header("Location: ../index.php");
  die();
}

# Include update config from file for changes.
$config = include '../config.php';

# Connect to database by default to display error.
$db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
if (!$db) {
  $error['database'] = "ERROR: Database is not available.";
}

# Get MAC concessions
function get_connected_devices() {
  $devices = exec("sudo iptables -L -n --line-numbers | grep MAC | wc -l");
  return $devices;
}

# Get Free Users from Database - returns array $users
function get_users($config) {
  $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
  if (!$db) {
    }
    $query = "SELECT COUNT(*) FROM $config[db_tableauth] WHERE role='FREE'";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);
    $users['free'] = $data[0];
    $query = "SELECT COUNT(*) FROM $config[db_tableauth] WHERE role='STANDARD'";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);
    $users['premium'] = $data[0];
    $query = "SELECT COUNT(*) FROM $config[db_tableauth] WHERE role='PRO'";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);
    $users['premium1'] = $data[0];
    $query = "SELECT COUNT(*) FROM $config[db_tableauth] WHERE role='administrator'";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);
    $users['admin'] = $data[0];
    $query = "SELECT COUNT(*) FROM $config[db_tableauth]";
    $sql = mysqli_query($db, $query);
    $data = mysqli_fetch_row($sql);
    $users['total'] = $data[0];
    mysqli_close();
    return $users;
}

# Get Network usage (ifstat and vnstat required)
function get_network_usage($config) {
 $network['download'] = exec("ifstat -i $config[external_int] -q 0.1 1 | tail -n1 | tr -s '' ' ' | cut -d ' ' -f2");
 $network['upload'] = exec("ifstat -i $config[external_int] -q 0.1 1 | tail -n1 | tr -s '' ' ' | cut -d ' ' -f3");
 $network['day_upload'] = exec("vnstat -d -i $config[external_int] | tail -n3 | head -n1 | tr -s '' ' ' | cut -d ' ' -f3-4");
 $network['day_download'] = exec("vnstat -d -i $config[external_int] | tail -n3 | head -n1 | tr -s '' ' ' | cut -d ' ' -f6-7");
 return $network;
}

# Get RAM, DISK AND CPU Usage
function get_system_info() {
  $sysinfo['used_ram'] = exec("free -m | tr -s '' ' ' | head -n2 | tail -n1 | cut -d ' ' -f3");
  $sysinfo['total_ram'] = exec("free -m | tr -s '' ' ' | head -n2 | tail -n1 | cut -d ' ' -f2");
  $sysinfo['cpu_usage'] = exec("top -bn2 -d 0.15 | grep '^%Cpu' | tail -n1 | gawk '{print $2+$4+$6}'");
  $sysinfo['total_disk'] = exec("df -h | grep /$ | tr -s '' ' ' | cut -d ' ' -f2");
  $sysinfo['free_disk'] = exec("df -h | grep /$ | tr -s '' ' ' | cut -d ' ' -f3");
  return $sysinfo;
}

                  ////////////////////////////////////////////////////
                  /////////// FIRST PAGE: GENERAL SETTINGS ///////////
                  ////////////////////////////////////////////////////


function print_general_settings($config, $error, $devices, $users, $network, $sysinfo) {
  
                  /////////// DATABASE SETTINGS ///////////  
  
  print('



  <div class="cardContainer mdl-shadow--2dp mdl-cell mdl-cell--8-col">
  <div class="cardTitle">
    <h4 class="cardh4">Database Settings</h4>
  </div>
    <div class="cardBody mdl-cell mdl-cell--8-col">
      <table class="cardContent mdl-data-table mdl-js-data-table ">
      <tbody>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Captive portal name</strong></td>
          <td>'. $config['captivename'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Login form location</strong></td>
          <td>' . $config['loginphp_file'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Database name</strong></td>
          <td>' . $config['db_name'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Database address</strong></td>
          <td>' . $config['db_server'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Database user</strong></td>
          <td>' . $config['db_username'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Database auth table</strong></td>
          <td>' . $config['db_tableauth'] .'</td>
        </tr>
                               
      </tbody>
      </table>  
      <br> 
      <div valign="bottom">
        <hr class="cardContent">
        <p class="cardContent alert-info"><strong>INFO:</strong> If you need to edit the configuration of the database parameters please open config.php, if you need to reset the captive portal please use first_run.php</p>    
        <hr class="cardContent">
      </div>
    </div>
  </div>


  <!-- /////////// FIREWALL SETTINGS /////////// -->


 
  <div class="cardContainer mdl-shadow--2dp mdl-cell mdl-cell--8-col">

    <form method="POST" action="cpanel.php?menu=1&form=1">
    <div class="cardTitle">
      <h4  class="cardh4">Firewall Settings</h4>
    </div>
    <div class="cardBody mdl-cell mdl-cell--8-col">
    <table class="cardContent mdl-data-table mdl-js-data-table ">
    <tbody>
      <tr>
        <td class="mdl-data-table__cell--non-numeric"><strong>Internal Interface</strong></td>
        <td>
          <div class="cardContent mdl-textfield mdl-js-textfield">
            <input id="internal_int" class="mdl-textfield__input" type="text" name="internal_int">
            <label class="mdl-textfield__label" for="internal_int">' . $config['internal_int'] .'</label>
          </div>  
        </td>
      </tr>
      <tr>
        <td class="mdl-data-table__cell--non-numeric"><strong>External Interface</strong></td>
        <td>
          <div class="cardContent mdl-textfield mdl-js-textfield">
            <input id="external_int" class="mdl-textfield__input" type="text" name="external_int">
            <label class="mdl-textfield__label" for="external_int">' . $config['external_int'] .'</label>
          </div>  
        </td>
      </tr>
      <tr>
        <td class="mdl-data-table__cell--non-numeric"><strong>Subnet Range</strong></td>
        <td>
          <div class="cardContent mdl-textfield mdl-js-textfield">
            <input id="external_subnet" class="mdl-textfield__input" type="text" name="external_subnet">
            <label class="mdl-textfield__label" for="external_subnet">' . $config['external_subnet'] .'</label>
          </div>  
        </td>        
      </tr>
                             
    </tbody>
    </table>     
      <br>
      ');
      if(isset($error['form1'])){
        print('<p class="cardContent alert-danger">' . $error['form1'] . '</p>');


      }
      print('
      <div>
      <hr class="cardContent">
      <div align=center>
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Apply Changes</button>
      </div>
      </form>
      <hr class="cardContent">
      </div>
    </div>
  </div>




  <!-- /////////// CONCESSION SETTINGS /////////// -->






  <div  class="cardContainer mdl-shadow--2dp mdl-cell mdl-cell--8-col">
    <div class="cardTitle">
      <h4 class="cardh4">Concession Settings</h4>
    </div>
      <div class="cardBody mdl-cell mdl-cell--8-col">
      <form method="POST" action="cpanel.php?menu=1&form=3">
      <br>
      <div align=center>
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">REMOVE ALL CONCESSIONS</button>
      </div>
      </form>
      <br>
      <form method="POST" action="cpanel.php?menu=1&form=2">
      <table class="cardContent mdl-data-table mdl-js-data-table ">
      <tbody>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Free Concession Time</strong></td>
          <td>
            <div class="cardContent mdl-textfield mdl-js-textfield">
              <input id="free_time" class="mdl-textfield__input" type="text" name="free_time">
              <label class="mdl-textfield__label" for="free_time">' . $config['free_time'] .'</label>
            </div>  
          </td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Standard Concession Time</strong></td>
          <td>
            <div class="cardContent mdl-textfield mdl-js-textfield">
              <input id="premium_time" class="mdl-textfield__input" type="text" name="premium_time">
              <label class="mdl-textfield__label" for="premium_time">' . $config['premium_time'] .'</label>
            </div>  
          </td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Pro Concession Time</strong></td>
          <td>
            <div class="cardContent mdl-textfield mdl-js-textfield">
              <input id="premium1_time" class="mdl-textfield__input" type="text" name="premium1_time">
              <label class="mdl-textfield__label" for="premium1_time">' . $config['premium1_time'] .'</label>
            </div>  
          </td>
        </tr>        
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Admin Concession Time</strong></td>
          <td>
            <div class="cardContent mdl-textfield mdl-js-textfield">
              <input id="admin_time" class="mdl-textfield__input" type="text" name="admin_time">
              <label class="mdl-textfield__label" for="admin_time">' . $config['admin_time'] .'</label>
            </div>  
          </td>        
        </tr>
                              
      </tbody>
      </table>

      <br>
      ');
      if(isset($error['form2'])){
        print('<p class="cardContent alert-danger">' . $error['form2'] . '</p>');


      }
      print('    
      <br> 
      <div>
        <hr class="cardContent">
        <div align=center>
          <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Apply Changes</button>
        </div>    
        </form>
        
        <hr class="cardContent">
        </div>
      </div>
    </div>





    <!-- /////////// SYSTEM STATUS /////////// -->






  <div style="width:1550px;margin-right:45px;" class=" mdl-shadow--2dp mdl-cell mdl-cell--12-col">        
    <div class="cardTitle">
        <h4 class="cardh4">System Status</h4>
    </div>
    <div style="display:inline-block;vertical-align:top;" class="cardBody mdl-cell mdl-cell--8-col">
      <table class="cardContent mdl-data-table mdl-js-data-table ">
      <tbody>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Total Users</strong></td>
          <td>' . $users['total'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Free Users</strong></td>
          <td>' . $users['free'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Standard Users</strong></td>
          <td>' . $users['premium'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Pro Users</strong></td>
          <td>' . $users['premium1'] .'</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Admin Users</strong></td>
          <td>' . $users['admin'] .'</td>
        </tr>

                              
      </tbody>
      </table>      
    </div>
    

    <div style="display:inline-block" class="cardBody mdl-cell mdl-cell--8-col">
      <table class="cardContent mdl-data-table mdl-js-data-table ">
      <tbody>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Active Concessions</strong></td>
          <td>' . $devices . '</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Network Usage (now)</strong></td>
          <td>' . $network['download'] . "KB/s | " . $network['upload'] . 'KB/s</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Data Transfered (1d)</strong></td>
          <td>' . $network['day_download'] . " | " . $network['day_upload'] . '</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>RAM Usage</strong></td>
          <td>' . $sysinfo['used_ram'] . "MB | " . $sysinfo['total_ram'] . 'MB</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>CPU Usage</strong></td>
          <td>' . $sysinfo['cpu_usage'] . "% | 100.0%" . '</td>
        </tr>
        <tr>
          <td class="mdl-data-table__cell--non-numeric"><strong>Disk Usage</strong></td>
          <td>' . $sysinfo['free_disk'] . "B | " . $sysinfo['total_disk'] . 'B</td>
        </tr>
            

                              
      </tbody>
      </table>  
      </div>      
      <div style="margin-left:55px;margin-top:60px;display:inline-block;vertical-align:top;">
        <img style="max-width: 100%;max-height: 100%" src="../login/images/logo.png">	
      </div>
      ');
      if(isset($error['database'])){
        print('<p class="cardContent alert-danger">' . $error['database'] . '</p>');


      }
      print(' 

    </div>
  </div>
');
}


                  ////////////////////////////////////////////////////
                  ///////////SECOND PAGE: DATABASE MANAGEMENT ////////
                  ////////////////////////////////////////////////////



function print_db($config, $data, $error) {
  # Print first sector
  print('
  <div style="width:1550px;margin-right:45px;" class=" mdl-shadow--2dp mdl-cell mdl-cell--12-col">        
    <div class="cardTitle">
        <h4 class="cardh4">Database Management</h4>
    </div>
    <div style="margin:30px;" class="mdl-cell mdl-cell--8-col">
      <form method="POST" action="cpanel.php?menu=2&form=1">
      <div style="display:inline-block;margin-right:20px;">
        <div style="width:150px;" class="select">
          <select name="type" id="slct">

            <option value="email">EMAIL</option>
            <option value="order_ID">ORDER_ID</option>
            <option value="role">ROLE</option>
          </select>
        </div>
      </div>
      <div style="display:inline-block;margin-right:20px;">
      <div style="width:150px" class="select">
        <select name="order" id="slct">
          <option value="ASC">ASC</option>
          <option value="DESC">DESC</option>
        </select>
      </div>
      </div>
        <div style="vertical-align:center;" class="mdl-textfield mdl-js-textfield">
          <input id="search" class="mdl-textfield__input" type="text" name="search">
          <label class="mdl-textfield__label" for="search">Search...</label>
        </div>  

        <button style="margin-left:30px;" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">SEARCH</button>
      </form>     
   </div>
    <div style="display:inline-block;margin:30px;" class="mdl-cell mdl-cell--8-col">
      <table style="margin-left:7px;width:100%;" class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
        <thead>
          <tr>
            <th>ID</th>
            <th></th>
            <th>Email</th>
            <th></th>
            <th></th>

            <th >Role</th>
            <th></th>
            <th >Last Login</th>
            
            <th>Creation Date</th>
            <th>Payment Status</th>   
          </tr>
        </thead>
      </table>
      
      <div style="width:100%;height:448px;overflow-y: scroll;overflow-x:hidden" class="mdl-cell mdl-cell--8-col">
        <table  style="width:100%; " class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">

          <tbody >

            ');
            while ($row = mysqli_fetch_array($data, MYSQLI_ASSOC)) {
              print("<tr > ");
              print("<td class='mdl-data-table__cell--non-numeric'>" . $row['order_ID'] . "</td><td class='mdl-data-table__cell--non-numeric'>" . $row['email'] . "</td><td class='mdl-data-table__cell--non-numeric'>" . $row['role'] . "</td><td class='mdl-data-table__cell--non-numeric'>" . $row['last_login'] . "</td>" . "<td class='mdl-data-table__cell--non-numeric'>" . $row['creation_date'] . "</td><td class='mdl-data-table__cell--non-numeric'>" . $row['payment_status'] . "</td>");
              print("</tr>");
            }
        print('
          </tbody>
        </table>    
     </div>
     
    </div>




    <!-- /////////// DATABASE MANAGEMENT RIGHT PANEL /////////// -->





    <div style="display:inline-block;margin:30px;width:390px;height:500px;vertical-align:top;padding:20px;" class="mdl-cell mdl-shadow--2dp mdl-cell--8-col">
    <form style="margin-left:10px" method="POST" action="cpanel.php?menu=2&form=4">
      <hr>
      <div align=center>
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">EXPORT DATABASE</button>
      </div> 
      <hr>

    </form>
        <form style="margin-left:10px" method="POST" action="cpanel.php?menu=2&form=2">
          <h6 style="margin-bottom:-10px;" class="cardh4">Type the ID of the user that you want to delete:</h6>
          <div style="vertical-align:center;" class="mdl-textfield mdl-js-textfield">
          <input id="delete" class="mdl-textfield__input" type="text" name="delete">
          <label class="mdl-textfield__label" for="delete">Example: 1234567890-S</label>
        </div>  
        <hr>
        ');
        if(isset($error)){
          print('<p align=center style="margin-left:60px;width:200px" class=" alert-info">' . $error . '</p>');
  
  
        }
        print('   
        <div align=center>
          <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">DELETE</button>
        </div>  
        <hr>

        </form>
 
        </div>
    </div>
  </div>

	 ');

}

                  ////////////////////////////////////
                  ///////////THIRD PAGE: LOGS ////////   
                  ////////////////////////////////////


function print_logs($config, $data, $error) {

  print('
  <div style="width:1300px;margin-right:45px;" class=" mdl-shadow--2dp mdl-cell mdl-cell--12-col">        
    <div class="cardTitle">
        <h4 class="cardh4">Logs</h4>
    </div>

    <div style="display:inline-block;margin:30px;" class="mdl-cell mdl-cell--12-col">

      <div style="width:95%;height:448px;overflow-y: scroll;overflow-x:scroll" class="mdl-cell mdl-cell--8-col">
        <table  style="width:100%;" class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">

          <tbody >

            ');
            $logfile = fopen("/var/log/captiveportal.log", "r") or die("Unable to open file!");

            while(!feof($logfile)) {
              $line = fgets($logfile);
              print("<tr> ");

              print("<td class='mdl-data-table__cell--non-numeric'>" . $line . "</td>");
              print("</tr>");

            }

            fclose($logfile);
          print('
          </tbody>
        </table>    
     </div>
     <div style="width:100%" class="mdl-cell mdl-cell--8-col">
     <form method="POST" action="cpanel.php?menu=3">
       <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Update</button>
     </form>     
     </div>
     
    </div>
  </div>

	 ');

}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Control Panel | <?php print($config['captivename']); ?></title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Control Panel | <?php print($config['captivename']); ?>">


    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.cyan-light_blue.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cpanel.css">
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
    <div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
      <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
          
          

        </div>
      </header>
      <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
          <div style="display:inline-block"><img src="images/wrench.png" class="demo-avatar">
          <p style="margin-left:20px;display:inline-block">Administrator</p>
          <form method ="POST" action ="cpanel.php?menu=logout">                  
              <div style="margin-top:20px" align=center>
                <button style="padding-left:40px;padding-right:40px" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Log out</button>
              </div>
              </form>    
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
          <a class="mdl-navigation__link" href="cpanel.php?menu=1"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">dashboard</i>General Settings</a>
          <a class="mdl-navigation__link" href="cpanel.php?menu=2"><i class="mdl-color-text--blue-grey-400 material-icons">dns</i>Database management</a>
          <a class="mdl-navigation__link" href="cpanel.php?menu=3"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">library_books</i>Logs</a>

          <div class="mdl-layout-spacer"></div>
          <a class="mdl-navigation__link" href="../index.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">exit_to_app</i><span>Login page</span></a>
        </nav>
      </div>
      <body>
      <main class="mdl-layout__content mdl-color--grey-100">
      <div class="mdl-grid">
      
<?php



/////////// LOGOUT BUTTON ///////////

if($_GET['menu'] == "logout"){
  session_destroy();
  unset($_SESSION['config']);
  //INCLUDING THE IPTABLES CONCESSION REMOVER
  $logout = true;
  include "../cpanel/iptablesUserHandler.php";
  header("Location: ../index.php");
  die();

}

/////////// GENERAL SETTINGS: HANDLER ///////////

if ($_GET['menu'] == 1) {
  $MAC = get_connected_devices();
  $users = get_users($config);
  $network = get_network_usage($config);
  $sysinfo = get_system_info();
	# If Not form selected
	if ($_GET['form'] == NULL) {
		print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
	 }

	# If POST its from first form
	elseif ($_GET['form'] == 1) {
		# First try
		if($_POST['internal_int'] == NULL and $_POST['external_int'] == NULL and $_POST['external_subnet'] == NULL) {
			$error['form1'] = "ERROR: Please fill all the fields before applying.";
			print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
		}
		# If one of the three parameters is missing go back.
		elseif($_POST['internal_int'] == "" or $_POST['external_int'] == "" or $_POST['external_subnet'] == "") {
			$error['form1'] = "ERROR: Please fill all the fields before applying.";
			print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
		}
    # We assume data is good.
		else {
			# If the three parameters aren't empty, go post config.
			if ($_POST['internal_int'] != "" or $_POST['external_int'] != "" or $_POST['external_subnet'] != "") {
				$config['external_int'] = "$_POST[external_int]";
				$config['internal_int'] = "$_POST[internal_int]";
				$config['external_subnet'] = "$_POST[external_subnet]";
				file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
        print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
			}
		}
	}

	# If POST its from second form
	elseif ($_GET['form'] == 2) {
    if($_POST['free_time'] == NULL and $_POST['premium_time'] == NULL and $_POST['premium1_time'] == NULL and $_POST['admin_time'] == NULL) {
			$error['form2'] =  "ERROR: Please fill all the fields before applying.";
			print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
	   }
     # If one of the three parameters is missing go back.
    elseif($_POST['free_time'] == "" or $_POST['premium_time'] == "" or $_POST['premium1_time'] == "" or $_POST['admin_time'] == "") {
      $error['form2'] = "ERROR: Please fill all the fields before applying.";
      print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
    }
    elseif ($_POST['free_time'] != "" or $_POST['premium_time'] != "" or $_POST['premium1_time'] != "" or $_POST['admin_time'] != "") {
      $config['free_time'] = "$_POST[free_time]";
      $config['premium_time'] = "$_POST[premium_time]";
      $config['premium1_time'] = "$_POST[premium1_time]";      
      $config['admin_time'] = "$_POST[admin_time]";
      file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
      print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
      }
    }

  
  //REMOVE ALL CONCESSIONS BUTTON
  //this button will include the iptables.sh script commands that its gonna rewrite the iptables, erasing the current user concessions
  elseif ($_GET['form'] == 3) {

    include "removeConcessions.php";

    header("location: cpanel.php?menu=logout ");
    }
  }

/////////// DATABASE MANAGEMENT: HANDLER ///////////

if ($_GET['menu'] == 2) {
  if ($_GET['form'] == NULL) {
    print_db($config, $data, $error);
   }
  elseif ($_GET['form'] == 1) {
    if (!isset($_POST['type']) and !isset($_POST['search']) and !isset($_POST['order'])) {
      print_db($config, $data, $error);

    }
    elseif (isset($_POST['type']) and isset($_POST['search']) and isset($_POST['order'])) {
      # Remove Special Characters to Query
      $type = mysqli_real_escape_string($db, $_POST['type']);
      $search = mysqli_real_escape_string($db, $_POST['search']);
      $order = mysqli_real_escape_string($db, $_POST['order']);
      # Execute Query
      $query = "SELECT order_ID,email,role,creation_date,last_login,payment_status FROM $config[db_tableauth] WHERE $type LIKE '$search%' ORDER BY $type $order";
      $data = mysqli_query($db, $query);
      print_db($config,$data, $error);
      return($data);
    }
  }
  elseif ($_GET['form'] == 2) {
    if ($_POST['delete'] != "") {
      $delete = mysqli_real_escape_string($db, $_POST['delete']);
      $query = "DELETE FROM $config[db_tableauth] WHERE order_ID='$delete'";
      $error = mysqli_query($db, $query);
      $error = "Deleted user with id: " . $delete;
      print_db($config, $data, $error);
      return($error);
    }
    else {
      print_db($config, $data, $error);
    }
  }
  elseif ($_GET['form'] == 4) {
    $filename = "backup-" . date("d-m-Y") . ".sql.gz";
    $mime = "application/x-gzip";
    header("Content-Type: " . $mime);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $export = "mysqldump -u $config[db_username] --password=$config[db_password] $config[db_name] | gzip --best";
    passthru($export);
    }
}

/////////// LOG: HANDLER ///////////
if($_GET['menu'] == 3) {

  print_logs($config, $data, $error);

}
?>  
        </div>
      </main>
    <script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
  </body>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>  
</html>


