<!DOCTYPE html>
<html>
<head>

<?php
    header("Access-Control-Allow-Origin: http://mininet-vm/web/openflowproxy.php");
?>

<link rel="stylesheet" type="text/css" href="css/style.css"/>
<link rel="stylesheet" type="text/css" href="joyride/joyride-2.1.css">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<script type="text/javascript" src="js/nodeinput.js"></script>
<!-- load the d3.js library -->    
<script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<meta charset=utf-8 />
<title>SDN Flow Statistics</title>

</head>


<body>
<div class="container">
  <div class="row">
	<h1 id="start" class="text-center">POX - Network Statistic UI</h1>
	</hr>
  </div> <!-- Row -->
  
  <div class="row">
  	<div id="cy"></div>
  </div> <!-- Row -->
  <div class="row">
	<div id="graph_container" class="centered"> <?php include("graph.php") ?> </div>
	<div id="statgraph"></div>
  </div> <!-- Row -->
 


 <script>
  var oldjson = "";
  function ajaxcmd() {
	  jQuery.ajax({
	                type: "POST",
	                url: 'http://localhost/web/openflowproxy.php',
	                dataType: 'json',
	                data: {functionname: 'get_topo'},
	 
	                success: function (obj, ts) {
	                        if (!('error' in obj)) {
	                        		var result = obj.result;
	                        	    if (oldjson != JSON.stringify(result)) {
		                        	    oldjson = JSON.stringify(result);
		                                makegraph(oldjson);
		                            }
	                        } else {
	                        		//dummy text
	                                jsontext = {"switches":[{"dpid":"00-00-00-00-00-01"},{"dpid":"00-00-00-00-00-02"},{"dpid":"00-00-00-00-00-03"},{"dpid":"00-00-00-00-00-04"},{"dpid":"00-00-00-00-00-05"},{"dpid":"00-00-00-00-00-06"},{"dpid":"00-00-00-00-00-07"}],
	"links":[["00-00-00-00-00-01","00-00-00-00-00-05"],["00-00-00-00-00-02","00-00-00-00-00-03"],["00-00-00-00-00-05","00-00-00-00-00-06"],["00-00-00-00-00-05","00-00-00-00-00-07"],["00-00-00-00-00-02","00-00-00-00-00-04"],["00-00-00-00-00-01","00-00-00-00-00-02"]]}

	                        }
	                }
        });
  }

  ajaxcmd();
  setInterval(ajaxcmd,5000);
  </script>

</div> <!-- Container -->
</body>
</html>
