<?php
    $username = "root"; 
    $password = "";   
    $host = "localhost";
    $database="coms";
    
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);
	
	//$sql = $_GET["q"];
	
    $sql = "SELECT  `Timestamp`, `Value` FROM  `teststuff`";
    $query = mysql_query($sql);
    
    if ( ! $query ) {
        echo mysql_error();
        die;
    }
    
    $data = array();
    
    for ($x = 0; $x < mysql_num_rows($query); $x++) {
        $data[] = mysql_fetch_assoc($query);
    }
    
    echo json_encode($data);     
     
    mysql_close($server);
?>