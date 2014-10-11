<?php

	//Connect to database now
	
    $username = "root"; 
    $password = "";   
    $host = "localhost";
    $database="coms";
    
    $link = mysql_connect($host, $username, $password);
	if(!$link) {
		die('Could not connect: ' . mysql_error());
	}
	if (!mysql_select_db($database, $link)) {
		die('Could not select database: ' . mysql_error());
	}
	
	//Build initial SQL query
    $portlistsql = "SELECT  DISTINCT `Port` FROM  `teststuff` WHERE ";

	//Process $_GET stuff now
	//$getstring is used for the graph
	$getstring = "?";
	
	if(isset($_GET["source"]) && ($_GET["source"] != null)) {
		$source = $_GET["source"];
		$getstring .= "source=".$source;
		$portlistsql .= "`Source` = '".$source."'";
	}
	if(isset($_GET["dest"]) && ($_GET["dest"] != null)) {
		$dest = $_GET["dest"];
		$getstring .= "&dest=".$dest;
		$portlistsql .= " AND `Destination` = '".$dest."'";
	}
	if(isset($_GET["port"]) && ($_GET["port"] != null)) {
		$port = $_GET["port"];
		$getstring .= "&port=".$port;
	}
	if(isset($_GET["tcpudp"]) && ($_GET["tcpudp"] != null)) {
		$tcpudp = $_GET["tcpudp"];
		$getstring .= "&tcpudp=".$tcpudp;
	}
	//Finish our query up now with a semicolon
	$portlistsql .= ";";
	
	//Run the query and grab the results
	///*
    $result = mysql_query($portlistsql);
    
    if ( ! $result ) {
        echo mysql_error();
		echo "<br />";
		echo $portlistsql;
        die;
    }
	
	$portlist = array();
	
	for ($x = 0; $x < mysql_num_rows($result); $x++) {
        $portlist[$x] = mysql_result($result, $x);
    }
	//*/
	
	//$portlist = array(1,2,3,4);
	
	mysql_close($link);
?>

<meta charset="utf-8">
<style> /* set the CSS */

body { font: 12px Arial;}

path { 
    stroke: steelblue;
    stroke-width: 2;
    fill: none;
}

.axis path,
.axis line {
    fill: none;
    stroke: grey;
    stroke-width: 1;
    shape-rendering: crispEdges;
}

</style>
<body>
<!-- Add in some drop-down boxes to restrict based on port and crap-->
<form action="test.php" method="get">
	Port: <select name="port">
	
	<?php
		foreach($portlist as $port) {
			?>

                <option> <?php echo $port; ?> </option>>

			<?php
		}
	?>
	
	</select>   TCP/UDP:
	<select name="tcpudp">
		<option>TCP</option>
		<option>UDP</option>
	</select>
	<input type="hidden" name="source" value="<?php echo $source; ?>" />
	<input type="hidden" name="dest"value="<?php echo $dest; ?>" />
	<input type="submit" />
</form>

<!-- load the d3.js library -->    
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>

// Set dimensions and padding of graph.
var margin = {top: 30, right: 20, bottom: 30, left: 50},
    width = 1280 - margin.left - margin.right,
    height = 720 - margin.top - margin.bottom;

// Parse the timestamp to silly d3 format or something
var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;

// Set the ranges
var x = d3.time.scale().range([0, width]);
var y = d3.scale.linear().range([height, 0]);

// Define the axes
var xAxis = d3.svg.axis().scale(x)
    .orient("bottom").ticks(10);

var yAxis = d3.svg.axis().scale(y)
    .orient("left").ticks(10);

// Define the line
var valueline = d3.svg.line()
    .x(function(d) { return x(d.Timestamp); })
    .y(function(d) { return y(d.Value); });
    
// Adds the svg canvas
var svg = d3.select("body")
    .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
    .append("g")
        .attr("transform", 
              "translate(" + margin.left + "," + margin.top + ")");

// Get the data
data = [
{Timestamp:"30-Apr-12",Value:53.98},
{Timestamp:"27-Apr-12",Value:67.00},
{Timestamp:"26-Apr-12",Value:89.70},
{Timestamp:"25-Apr-12",Value:99.00},
{Timestamp:"24-Apr-12",Value:130.28},
{Timestamp:"23-Apr-12",Value:166.70},
{Timestamp:"20-Apr-12",Value:234.98},
{Timestamp:"19-Apr-12",Value:345.44},
{Timestamp:"18-Apr-12",Value:443.34},
{Timestamp:"17-Apr-12",Value:543.70},
{Timestamp:"16-Apr-12",Value:580.13},
{Timestamp:"13-Apr-12",Value:605.23},
{Timestamp:"12-Apr-12",Value:622.77},
{Timestamp:"11-Apr-12",Value:626.20},
{Timestamp:"10-Apr-12",Value:628.44},
{Timestamp:"9-Apr-12",Value:636.23},
{Timestamp:"5-Apr-12",Value:633.68},
{Timestamp:"4-Apr-12",Value:624.31},
{Timestamp:"3-Apr-12",Value:629.32},
{Timestamp:"2-Apr-12",Value:618.63},
];

//d3.csv("data.csv", function(error, data) {
//<? //echo $gestring; ?>
d3.json("getData.php", function(error, data) {
   data.forEach(function(d) {
        d.Timestamp = parseDate(d.Timestamp);
        d.Value = +d.Value;
    });

    // Scale the range of the data
    x.domain(d3.extent(data, function(d) { return d.Timestamp; }));
    y.domain([0, d3.max(data, function(d) { return d.Value; })]);

    // Add the valueline path.
    svg.append("path")
        .attr("class", "line")
        .attr("d", valueline(data));

    // Add the X Axis
    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    // Add the Y Axis
    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis);
});
//});

</script>
</body>