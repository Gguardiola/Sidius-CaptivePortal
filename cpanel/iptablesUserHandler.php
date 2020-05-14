<?php

/////////////////////////////////////////////////////////////////////////
///// ACCOUNT SETTINGS USER LOGOUT BUTTON (USER CONCESSION REMOVER) /////
////////////////////////////////////////////////////////////////////////


if(isset($logout)){
    //UPDATING THE IPTABLES LOG
    exec("sudo iptables -L -v --line-numbers > /var/log/iptablesRules.log");
    exec("sudo iptables -t nat -L -v --line-numbers >> /var/log/iptablesRules.log");

    $mac = exec("arp -n | grep '$_SERVER[REMOTE_ADDR] ' | tr -s '' ' ' | cut -f3 -d ' ' ");
    $mac = strtoupper($mac);
    if ($file = fopen("/var/log/iptablesRules.log", "r")) {
        //REMOVING THE FORWARD RULE NUMBER
        while(!feof($file)) {
            $line = fgets($file);

            if(strpos($line, $mac)){

                $line = explode(" ",$line);
                $userFORWARDnum = $line[0];
                print($userFORWARDnum);
                exec("sudo iptables -D FORWARD $userFORWARDnum");
                break;

            }
        }
        
        //REMOVING THE PREROUTING RULE NUMBER
        while(!feof($file)) {
            $line = fgets($file);

            if(strpos($line, $mac)){

                $line = explode(" ",$line);
                $userPREROUTINGnum = $line[0];
                print($userPREROUTINGnum);
                exec("sudo iptables -t nat -D PREROUTING $userPREROUTINGnum");
                break;
   

            }
        }
        
        fclose($file);
    }
}else{
    print("Access denied!");
    die();

}
?>