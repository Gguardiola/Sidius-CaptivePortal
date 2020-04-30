<?php 

$config = include "../config.php";
if(strpos($_SERVER['HTTP_REFERER'], $config['payment_gateway']) !== false){

  include "redsys-api/apiRedsys.php";
  $myObj = new RedsysAPI;

  $version = $_GET['Ds_SignatureVersion'];
  $params = $_GET['Ds_MerchantParameters'];
  $sgnatureRecived = $_GET['Ds_Signature'];

  $decodec = $myObj->decodeMerchantParameters($params);

  $order_decoded = $myObj->getParameter('Ds_Order');



  $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
  # In case of error connecting send client back to login.
  if (!$db) {
    #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
    print("ERROR: Database is not available.");
  }
  # Database is up, querying...

  else {

          $query = "SELECT * from $config[db_tableauth] where order_ID = '$order_decoded'";
          $sql = mysqli_query($db, $query);
          $data = mysqli_fetch_row($sql);    

          if($data != NULL){
            session_start();
            $_SESSION['validation'] = true;
            $query = "UPDATE $config[db_tableauth] SET payment_status = 'PAID' where order_ID = '$order_decoded'";
            $sql = mysqli_query($db, $query);
            $data = mysqli_fetch_row($sql);  

            header("location:payment-ok.php");
          }
          else{
            header("location:payment-nook.php");

          }



      }

}
else{
  var_dump($_SERVER['HTTP_REFERER']);
  var_dump($config['payment_gateway']);


}



?>