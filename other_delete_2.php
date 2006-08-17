<?php

include "include_config.php";

        if (isset($_GET['mac'])) {

        $link = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect");
        mysql_select_db("$mysql_database") or die("Could not select database");

        $query = "DELETE FROM other WHERE other_id = '" . $_GET['mac'] . "'";
        $result = mysql_query($query)  or die("Query failed at insert stage. groups");

        header("Location: ./list.php?view=other_delete");
        } else {
        header("Location: ./list.php?view=other_delete");
        }

?>
