<?php
# Starting session from iptables to get config saved even if we self-post
session_start();

if (!isset($_SESSION['config'])) {
  header("Location: index.php");
  die();
}

# Include update config from file for changes.
$config = include 'config.php';

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

# Menu 1 Page
function print_general_settings($config, $error, $devices, $users, $network, $sysinfo) {
  print('
  <body>
	<div class="exterior_contenido">
		<div class="interior_contenido">
			<div class="seccion1">
				<div class="contenido">

					<div class="contenidotop">
						<p class="negritacentrado24">Database Settings</p>
					</div>
						<p class="blanconegritaconf">Portal Cautive Name: <span class="noinput100">' . $config['captivename'] .'</p>
						<p class="blanconegritaconf">Login Form php: <span class="noinput100">' . $config['loginphp_file'] .'</p>
						<p class="blanconegritaconf">Database Name: <span class="noinput100">' . $config['db_name'] .'</p>
						<p class="blanconegritaconf">Database IP: <span class="noinput100">' . $config['db_server'] .'</p>
						<p class="blanconegritaconf">Database Login: <span class="noinput100">' . $config['db_username'] .'</p>
						<p class="blanconegritaconf">Database Password: <span class="noinput100">' . $config['db_password'] .'</p>
						<div class="contenidotop"></div>
						<p class="blanconegritaconf">Database Auth Table: <span class="noinput100">' . $config['db_tableauth'] .'</p>
						<div class="contenidotop"></div>
						<p class="verde">INFO: If you need to edit the configuration of the database parameters please open config.php, if you need to reset the captive portal please use first_run.php</p>
				</div>
			</div>
			<div class="seccion1">
			  <form method="POST" action="cpanel.php?menu=1&form=1">
			  	<div class="contenido">
			  	<div class="contenidotop">
				  	<p class="negritacentrado24">Firewall Settings</p>
			  	</div>
			 		<p class="blanconegritaconf">Internal Interface: <input class="input100" type="text" name="internal_int" placeholder=" ' . $config['internal_int'] .'"></p>
					<p class="blanconegritaconf">External Interface: <input class="input100" type="text" name="external_int" placeholder=" ' . $config['external_int'] .'"></p>
					<p class="blanconegritaconf">Subnet Range: <input class="input100" type="text" name="external_subnet" placeholder=" ' . $config['external_subnet'] .'"></p>
					<p class="rojo">' . $error['form1'] . '</p>
					<button class="login100-form-btn">
            <span class="textoboton">Apply Changes</span>
          </button>
		  	</form>
			  <form method="POST" action="cpanel.php?menu=1&form=2">
          <br><br>
          <div class="contenidotop"></div>
            <div class="contenidotop">
              <p class="negritacentrado24">Concession Settings</p>
            </div>
  	  	  	<p class="blanconegritaconf">Free Concession Time: <input class="input100" type="text" name="free_time" placeholder=" ' . $config['free_time'] .'"></p>
  	  	  	<p class="blanconegritaconf">Premium Concession Time: <input class="input100" type="text" name="premium_time" placeholder=" ' . $config['premium_time'] .'"></p>
  	  	  	<p class="blanconegritaconf">Admin Concession Time: <input class="input100" type="text" name="admin_time" placeholder=" ' . $config['admin_time'] .'"></p>
            <p class="rojo">' . $error['form2'] . '</p>
            <button class="login100-form-btn">
            <span class="textoboton">Apply Changes</span>
            </button>

  	  		</div>
          </form>
  		</div>

			<div class="seccion1">
        <div class="contenido">
        <div class="contenidotop">
          <p class="negritacentrado24">Concession Settings</p>
        </div>
			  <form method="POST" action="cpanel.php?menu=1&form=3">
          <button class="login100-form-btn-big">
            <span class="textoboton">DELETE ALL CLIENT CONCESSIONS</span>
            </button>
        </form>

        <br><div class="contenidotop"></div>
        <div class="contenidotop">
          <p class="negritacentrado24">System Status</p>
        </div><br>
        <p class="espaciado">Total Users: <span class="noinput100">' . $users['total'] .'</p>
        <p class="espaciado">Free Users: <span class="noinput100">' . $users['free'] .'</p>
        <p class="espaciado">Premium Users: <span class="noinput100">' . $users['premium'] .'</p>
        <p class="espaciado">Premium+ Users: <span class="noinput100">' . $users['premium1'] .'</p>
        <p class="espaciado">Admin Users: <span class="noinput100">' . $users['admin'] .'</p>
        <br>
        <p class="espaciado">Active Concessions: <span class="noinput100">' . $devices . '</p>
        <p class="espaciado">Network Usage (now): <span class="noinput100">' . $network['download'] . "KB/s | " . $network['upload'] . 'KB/s</p>
        <p class="espaciado">Data Transfered (1d): <span class="noinput100">' . $network['day_download'] . " | " . $network['day_upload'] . '</p>
        <p class="espaciado">RAM Usage: <span class="noinput100">' . $sysinfo['used_ram'] . "MB | " . $sysinfo['total_ram'] . 'MB</p>
        <p class="espaciado">CPU Usage: <span class="noinput100">' . $sysinfo['cpu_usage'] . "% | 100.0%" . '</p>
        <p class="espaciado">Disk Usage: <span class="noinput100">' . $sysinfo['free_disk'] . "B | " . $sysinfo['total_disk'] . 'B</p>

        <p class="rojo">' . $error['database'] . '</p>
			</div>
		    </div>
	    </div>
  </body>');
}

# Menu 2 Page
function print_db($config, $data, $error) {
  # Print first sector
  print('
    <body>
	  <div class="exterior_contenido">
		<div class="interior_contenido">
			<div class="seccion1-db">
				<div class="contenido-db">
					<div class="contenidotop">
            <p class="negritacentrado24-db"></p>
						<p class="negritacentrado24-db">Database</p>
          </div>
          <form method="POST" action="cpanel.php?menu=2&form=1">
            <div class="search">
            <select name="type">
              <option value="email">EMAIL</option>
              <option value="id">ID</option>
              <option value="role">ROLE</option>
            </select>
            <input class="input100-db" type="text" name="search" ></input>
            <select name="order" class="margenselect">
            <option value="ASC">ASC</option>
            <option value="DESC">DESC</option>
            </select>
            <button class="login100-form-btn-db-search">Search</button>
            </div>
         </form>
          <div class="db">
          <table>
            <th class="titol">ID<th class="titol2">Email<th class="titol3">Role<th class="titol4">Last Login<th class="titol5">Creation Date<th class="titol6">Payment Status
'
  );
  while ($row = mysqli_fetch_array($data, MYSQLI_ASSOC)) {
      print("<tr class='fila'>");
      print("<td>" . $row['order_ID'] . "</td><td class='noalign'>" . $row['email'] . "</td><td>" . $row['role'] . "</td><td>" . $row['last_login'] . "</td>" . "<td>" . $row['creation_date'] . "</td><td>" . $row['payment_status'] . "</td>");
      print("</tr>");
}

  print('
    </table>
    </div>
      <form method="POST" action="cpanel.php?menu=2&form=4">
        <button class="login100-form-btn-db">EXPORT DATABASE</button>
          <div class="color">
      </form>
        	 <form method="POST" action="cpanel.php?menu=2&form=2">
            <span class="blanconegrita-db">DELETE id: </span><input class="input100-db-2" type="text" name="delete"></input>
            <button class="login100-form-btn-db-s">DELETE</button>
           </form>
            <span class="verde-db">' . $error .'</span>
          </div>
    </div>
	  </div>
   </div>
   </div>
   </div>
   </body>');

}
?>

<!-- Top Menu STATIC !-->
<html lang="es">
<head>
	<title><?php print($config['captivename']); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="login/images/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="cpanel.css"/>
  <div class="exterior_superior">
    <div class="interior_superior">
      <a class="seccion_superior" href="cpanel.php?menu=1">
        <span class="negrita">GENERAL SETTINGS</span>
      </a>
      <a class="seccion_superior" href="cpanel.php?menu=2">
        <span class="negrita">DATABASE</span>
      </a>
      <a class="seccion_superior" href="cpanel.php?menu=3">
        <span class="negrita">LOGS</span>
      </a>
    </div>
  </div>
</head>

<?php
# If Not Page - First Page
if ($_GET['menu'] == NULL) {
  header("Location: cpanel.php?menu=1");

}

# General Settings
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
			$error['form1'] = "";
			print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
		}
		# If one of the three parameters is missing go back.
		elseif($_POST['internal_int'] == "" or $_POST['external_int'] == "" or $_POST['external_subnet'] == "") {
			$error['form1'] = "ERROR: Please fill the three fields before applying.";
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
    if($_POST['free_time'] == NULL and $_POST['premium_time'] == NULL and $_POST['admin_time'] == NULL) {
			$error['form2'] = "";
			print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
	   }
     # If one of the three parameters is missing go back.
    elseif($_POST['free_time'] == "" or $_POST['premium_time'] == "" or $_POST['admin_time'] == "") {
      $error['form2'] = "ERROR: Please fill the three fields before applying.";
      print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
    }
    elseif ($_POST['free_time'] != "" or $_POST['premium_time'] != "" or $_POST['admin_time'] != "") {
      $config['free_time'] = "$_POST[free_time]";
      $config['premium_time'] = "$_POST[premium_time]";
      $config['admin_time'] = "$_POST[admin_time]";
      file_put_contents('config.php',' <?php return ' . var_export($config, true) . ';');
      print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
      }
    }

  # Delete all client concessions
  elseif ($_GET['form'] == 3) {
    # Execute Query to grab all concessions
    $numero = exec("sudo iptables -L -n --line-numbers | grep MAC | wc -l");
    foreach (range(1,$numero) as $i){
      exec("sudo iptables -D FORWARD 3");
      exec("sudo iptables -t nat -D PREROUTING 2");
    }
    print_general_settings($config, $error, $MAC, $users, $network, $sysinfo);
    }
  }

# Database
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
      $error = "Deleted id " . $delete;
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
?>
