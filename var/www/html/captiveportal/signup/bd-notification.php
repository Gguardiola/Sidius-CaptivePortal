<?php 

$config = include "../config.php";



                  ///////////////////////////////////////////////////////////
                  /////////////// PAYMENT GATEWAY VERFICATION  //////////////
                  ///////////////////////////////////////////////////////////




//if the user doesn't come from the same url of the payment gateway that its in config.php the user will be redirected to the failed page
if(strpos($_SERVER['HTTP_REFERER'], $config['payment_gateway']) !== false){
  //API import
  include "redsys-api/apiRedsys.php";
  $myObj = new RedsysAPI;

  //getting the payment gateway verification info
  $version = $_GET['Ds_SignatureVersion'];
  $params = $_GET['Ds_MerchantParameters'];
  $sgnatureRecived = $_GET['Ds_Signature'];

  //decoding the verification info to get the returning user params
  $decodec = $myObj->decodeMerchantParameters($params);

  //getting the order code
  $order_decoded = $myObj->getParameter('Ds_Order');



  $db = mysqli_connect($config['db_server'], $config['db_username'], $config['db_password'], $config['db_name']);
  # In case of error connecting send client back to login.
  if (!$db) {
    #### WIP: error_log("ERROR: Database is not available!", 3, $config['error_log']); ####
    print("ERROR: Database is not available.");
  }
  # Database is up, querying...

  else {
          //checking if exist an user with the same order_ID
          $query = "SELECT * from $config[db_tableauth] where order_ID = '$order_decoded'";
          $sql = mysqli_query($db, $query);
          $data = mysqli_fetch_row($sql);    

          //if exist an user with the same order_ID, the verification is complete. Updating the payment status to PAID
          //NOW THE USER WILL BE REDIRECTED TO THE PAYMENT VERIFICATION DONE AND READY TO LOG IN
          if($data != NULL){

            //starting the verification session to prevent illegal access to the payment verification pages (payment-ok and payment-nook).
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
  header("location:payment-nook.php");
}



?>