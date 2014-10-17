<!DOCTYPE html>
<html>
<head>

<?php
    header("Access-Control-Allow-Origin: http://mininet-vm/web/openflowproxy.php");
?>

<link rel="stylesheet" type="text/css" href="css/style.css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<script type="text/javascript" src="js/nodeinput.js"></script>
<!-- load the d3.js library -->    
<script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>

<meta charset=utf-8 />
<title>Query Node</title>

</head>


<body>

  <h1>POX - Network Statistic UI</h1>

  <div id="graph_container"> <?php include("graph.php") ?> </div>
  <br/><br/><br/>		
  <div id="cy"></div>

  <script>
  setInterval(function() {
	  jQuery.ajax({
	                type: "POST",
	                url: 'http://localhost/web/openflowproxy.php',
	                dataType: 'json',
	                data: {functionname: 'get_topo'},
	 
	                success: function (obj, ts) {
	                        if (!('error' in obj)) {
	                                var result = obj.result;
	                                makegraph(JSON.stringify(result));
	                        } else {
	                        		//dummy text
	                                jsontext = {"switches":[{"dpid":"00-00-00-00-00-01"},{"dpid":"00-00-00-00-00-02"},{"dpid":"00-00-00-00-00-03"},{"dpid":"00-00-00-00-00-04"},{"dpid":"00-00-00-00-00-05"},{"dpid":"00-00-00-00-00-06"},{"dpid":"00-00-00-00-00-07"}],
	"links":[["00-00-00-00-00-01","00-00-00-00-00-05"],["00-00-00-00-00-02","00-00-00-00-00-03"],["00-00-00-00-00-05","00-00-00-00-00-06"],["00-00-00-00-00-05","00-00-00-00-00-07"],["00-00-00-00-00-02","00-00-00-00-00-04"],["00-00-00-00-00-01","00-00-00-00-00-02"]]}

	                        }
	                }
        });
  }, 5000); //5 seconds
  </script>

</body>
</html>
