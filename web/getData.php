<?php
	$testing = 0;

    $username = "root"; 
    $password = "";   
    $host = "localhost";
    $database="poxdb";
    
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);
	
	//Base query to manipulate
	$query = "SELECT  `datetime` as XVal, ";
	
	if(isset($_GET["unit"]) && ($_GET["unit"] != null)) {
		if($_GET["unit"] == "Bytes") {
			$query .= "`byte_count`";
		} else {
			$query .= "`packet_count`";
		}
	} else {
		//Default to packet_count
		$query .= "`packet_count`";
	}
	
	$query .= " as YVal FROM  `stats`";
	
	if(isset($_GET["source"]) && ($_GET["source"] != null)) {
		$source = $_GET["source"];
		if($source != "All") {
			$query .= " WHERE `dl_src` = '".$source."'";
		} else {
			//Use WHERE 1 so that subsequent ANDs work fine
			$query .= " WHERE 1";
		}
	} else {
		$query .= " WHERE 1";
	}
	if(isset($_GET["dest"]) && ($_GET["dest"] != null)) {
		$dest = $_GET["dest"];
		if($dest != "All") {
			$query .= " AND `dl_dst` = '".$dest."'";
		}
	}
	if(isset($_GET["port"]) && ($_GET["port"] != null)) {
		$port = $_GET["port"];
		if($port != "All") {
			$query .= " AND `tp_dst` = ".$port;
		}
	}
	if(isset($_GET["protocol"]) && ($_GET["protocol"] != null)) {
		$tcpudp = $_GET["protocol"];
		if($tcpudp == "TCP") {
			$query .= " AND `nw_proto` = 6";
		}
		if($tcpudp == "UDP") {
			$query .= " AND `nw_proto` = 17";
		}
		if($tcpudp == "ICMP") {
			$query .= " AND `nw_proto` = 1";
		}
	}
	// change that WHERE to be AND, this is just for quick testing without a source or dest or any other $_GET stuff
	$query .= " AND `datetime` BETWEEN (DATE_SUB(NOW(),INTERVAL 5 MINUTE)) AND NOW();";
	
    $result = mysql_query($query);
    
    if ( ! $result ) {
        echo mysql_error();
        die;
    }
    
    $data = array();
    
    for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $data[] = mysql_fetch_assoc($result);
    }
    
	if($testing) {
		echo $query."<br /><br />";
	}
	
	//Spit out our nice JSON encoded results for our graph to use now
	echo json_encode($data);     
     
    mysql_close($server);
?>