<?php
$page = "other";
include "include_config.php";
include "include_functions.php";

mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
mysql_select_db($mysql_database) or die("Could not select database");

if(isset($_REQUEST["view"]) AND isset($_REQUEST["category"])){


    //Other-System
    if($_REQUEST["view"]=="other_system" AND $_REQUEST["category"]=="summary" OR
       $_REQUEST["view"]=="printer" AND $_REQUEST["category"]=="summary"){

        $sql  = "UPDATE other SET other_network_name = '" . $_REQUEST['other_network_name'] . "',";
        $sql .= " other_ip_address = '" . $_REQUEST['other_ip_address'] . "',";
        $sql .= " other_mac_address = '" . $_REQUEST['other_mac_address'] . "',";
        $sql .= " other_p_port_name = '" . $_REQUEST['other_p_port_name'] . "',";
        $sql .= " other_description = '" . $_REQUEST['other_description'] . "',";
        $sql .= " other_serial = '" . $_REQUEST['other_serial'] . "',";
        $sql .= " other_manufacturer = '" . $_REQUEST['other_manufacturer'] . "',";
        $sql .= " other_model='" . $_REQUEST['other_model'] . "',";
        $sql .= " other_type='" . $_REQUEST['other_type'] . "',";
        $sql .= " other_location='" . $_REQUEST['other_location'] . "',";
        $sql .= " other_date_purchased='" . $_REQUEST['other_date_purchased'] . "',";
        $sql .= " other_value='" . $_REQUEST['other_value'] . "',";
        $sql .= " other_linked_pc='" . $_REQUEST['other_linked_pc'] . "' ";
        $sql .= " WHERE other_id='" . $_REQUEST['other'] . "'";

        $url="./system.php?other=".$_REQUEST["other"]."&view=".$_REQUEST["view"]." ";

    }elseif($_REQUEST["view"]=="monitor" AND $_REQUEST["category"]=="summary"){

        $sql  = "UPDATE monitor SET monitor_date_purchased = '" . $_REQUEST['monitor_date_purchased'];
        $sql .= "', monitor_purchase_order_number = '" . $_REQUEST['monitor_purchase_order_number'];
        $sql .= "', monitor_value = '" . $_REQUEST['monitor_value'];
        $sql .= "', monitor_description = '" . $_REQUEST['monitor_description'];
        $sql .= "' WHERE monitor_id = '" . $_REQUEST['monitor'] . "' ";

        $url="./system.php?monitor=".$_REQUEST["monitor"]."&view=".$_REQUEST["view"]." ";

    }else{
        die(__("FATAL: There is now method for this view/summary defined"));
    }

    //Executing the query
    $result=mysql_query($sql);
    if(!$result) { echo "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>";
                   echo "<pre>";
                   print_r($_REQUEST);
                   die();
                 };

    //Redirect
    header("Location: ".$url);

}else{
    die(__("FATAL: Not enought variables to proceed: view and category needed"));
}

die(print_r($_REQUEST));

?>
