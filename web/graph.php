<?php
/* USAGE:
   This should ALWAYS have $_GET["source"] and $_GET["dest"] set to the 
   source and destination mac addresses of the link.  Port, protocol and unit
   are optional, and will default to All, Any and Packets respectively.
*/

	//Connect to database now
    $username = "root"; 
    $password = "";   
    $host = "localhost";
    $database="poxdb";
    
    $link = mysql_connect($host, $username, $password);
	if(!$link) {
		die('Could not connect: ' . mysql_error());
	}
	if (!mysql_select_db($database, $link)) {
		die('Could not select database: ' . mysql_error());
	}
	
	//Build initial SQL query
	$sourcelistsql = "SELECT DISTINCT `dl_src` FROM `stats` ORDER BY `dl_src` ASC;";
	$destlistsql = "SELECT DISTINCT `dl_dst` FROM `stats` ORDER BY `dl_dst` ASC;";
    $portlistsql = "SELECT DISTINCT `tp_src` FROM  `stats`";

	//Process $_GET stuff now
	//$getstring is used for the graph
	$getstring = "?";
	
	if(isset($_GET["source"]) && ($_GET["source"] != null)) {
		$source = $_GET["source"];
		$getstring .= "source=".$source;
		if($source != "All") {
			$portlistsql .= " WHERE `dl_src` = '".$source."'";
		} else {
			$portlistsql .= " WHERE 1 ";
		}
	} else {
		$portlistsql .= " WHERE 1 ";
	}
	if(isset($_GET["dest"]) && ($_GET["dest"] != null)) {
		$dest = $_GET["dest"];
		$getstring .= "&dest=".$dest;
		if($dest != "All") {
			$portlistsql .= " AND `dl_dst` = '".$dest."'";
		}
	}
	if(isset($_GET["port"]) && ($_GET["port"] != null)) {
		if($_GET["port"] != "All") {
			$getstring .= "&port=".$_GET["port"];
		}
	}
	if(isset($_GET["protocol"]) && ($_GET["protocol"] != null)) {
		$protocol = $_GET["protocol"];
		if($protocol != "Any") {
			$getstring .= "&protocol=".$protocol;
		}
	}
	if(isset($_GET["unit"]) && ($_GET["unit"] != null)) {
		$getstring .= "&unit=".$_GET["unit"];
	}
	//Finish our query up now with a semicolon
	$portlistsql .= "ORDER BY tp_src ASC";
	
	//Run the queries and store the results in an array
    $result = mysql_query($sourcelistsql); 
    if ( ! $result ) {
        echo mysql_error();
		echo "<br />Sourcelistsql: ";
		echo $sourcelistsql;
        die;
    }
	$sourcelist = array();
	for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $sourcelist[$x] = mysql_result($result, $x);
    }
	
	//Destination
	$result = mysql_query($destlistsql); 
    if ( ! $result ) {
        echo mysql_error();
		echo "<br />Destlistsql: ";
		echo $destlistsql;
        die;
    }
	$destlist = array();
	for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $destlist[$x] = mysql_result($result, $x);
    }
	
	//Port
	$result = mysql_query($portlistsql); 
    if ( ! $result ) {
        echo mysql_error();
		echo "<br />Portlistsql: ";
		echo $portlistsql;
        die;
    }
	$portlist = array();
	for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $portlist[$x] = mysql_result($result, $x);
    }
	
	mysql_close($link);
?>

<!-- Add in some drop-down boxes to restrict based on port and crap-->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">

	<!-- These two drop-downs are filled in by nodeinput.js -->
	Source:
	<select id="source" name="source">
		<option>All</option>
		<?php
			foreach($sourcelist as $sourcemac) {
				if($sourcemac != null) {
					if($sourcemac == $_GET["source"]) {
						echo "<option selected>";
					} else {
						echo "<option>";
					}
					echo $sourcemac."</option>";
				}
			}
		?>	
	</select>
	
	Destination:
	<select id="dest" name="dest">
		<option>All</option>
		<?php
			foreach($destlist as $destmac) {
				if($destmac != null) {
					if($destmac == $_GET["dest"]) {
						echo "<option selected>";
					} else {
						echo "<option>";
					}
					echo $destmac."</option>";
				}
			}
		?>	
	</select>

	<!-- Might need to turn this into a quick bit of AJAX to update when a source and dest have been chosen... -->
	Port: 
	<select name="port">
		<option>All</option>
	<?php
		foreach($portlist as $port) {
			if($port != null) {
				if($port == $_GET["port"]) {
					echo "<option selected>";
				} else {
					echo "<option>";
				}
				echo $port."</option>";
			}
		}
	?>	
	</select> 
	
	Protocol:
	<select name="protocol">
<?php	
	$protocols = array("Any", "TCP", "UDP", "ICMP");
	$to_echo = "";
	if(isset($_GET["protocol"]) && ($_GET["protocol"] != null)) {
		for($i=0; $i<count($protocols); $i++) {
			$to_echo .= "<option "
			//This bit adds selected if GET[protocol] matches that index
			.($_GET["protocol"] == $protocols[$i] ? "selected" : "")
			. ">".$protocols[$i]."</option>";
		}
	} else {
		for($i=0; $i<count($protocols); $i++) {
			$to_echo .= "<option>".$protocols[$i]."</option>";
		}
	}
	echo $to_echo;
?>
	</select>
	
	Unit:
	<select name="unit">
	<?php
		if(isset($_GET["unit"]) && ($_GET["unit"] != null)) {
			if($_GET["unit"] == "Packets") {
				echo "<option selected>Packets</option><option>Bytes</option>";
			} else {
				echo "<option>Packets</option><option selected>Bytes</option>";
			}
		} else {
			echo "<option>Packets</option><option>Bytes</option>";
		}
	?>
	</select>
	
	<input type="submit" />
</form>

<script type="text/javascript">
var margin = {top: 20, right: 20, bottom: 70, left: 120},
    width = 1280 - margin.left - margin.right,
    height = 480 - margin.top - margin.bottom;
 
// Parse the date / time
var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;
 
var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);
 
var y = d3.scale.linear().range([height, 0]);
 
var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom")
    .tickFormat(d3.time.format("%M:%S"));
 
var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .ticks(10);
 
var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", 
          "translate(" + margin.left + "," + margin.top + ")");
 
d3.json("getData.php<?php echo $getstring; ?>", function(error, data) {
 
    data.forEach(function(d) {
        d.XVal = parseDate(d.XVal);
        d.YVal = +d.YVal;
    });
	
  x.domain(data.map(function(d) { return d.XVal; }));
  y.domain([0, d3.max(data, function(d) { return d.YVal; })]);
 
  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis)
    .selectAll("text")
      .style("text-anchor", "end")
      .attr("dx", "-.8em")
      .attr("dy", "-.55em")
      .attr("transform", "rotate(-90)" );
 
  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
	<?php	  
	if(isset($_GET["unit"]) && ($_GET["unit"] != null)) {
		echo ".text(\"# ".$_GET["unit"]."\");";
	}
	?>
 
  svg.selectAll("bar")
      .data(data)
    .enter().append("rect")
      .style("fill", "steelblue")
      .attr("x", function(d) { return x(d.XVal); })
      .attr("width", x.rangeBand())
      .attr("y", function(d) { return y(d.YVal); })
      .attr("height", function(d) { return height - y(d.YVal); });
 
});
</script>
