<!DOCTYPE html>
<html>
<head>

<?php
    header("Access-Control-Allow-Origin: http://mininet-vm/web/openflowproxy.php");
?>

<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="joyride/joyride-2.1.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<script type="text/javascript" src="js/nodeinput.js"></script>

<!-- load the d3.js library -->    
<script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/2.2.0/js/bootstrap.min.js"></script>


<meta charset=utf-8 />
<title>SDN Dataflow Monitor</title>

</head>


<body>
<div class="container">
  <div class="row">
	<h1 id="start" class="text-center">SDN Dataflow Monitor</h1>
	<hr>
  </div> <!-- Row -->
  
  <div class="row">
  	<div id="cy"></div>
	<hr>
  </div> <!-- Row -->
  <div class="row">
	<div id="graph_container" class="centered"> <?php include("graph.php") ?> </div>
	<hr>
  </div> <!-- Row -->

  <div class="row">
	<div id="footer" class="text-center"><h4>COMS4200 Assignment 2 2014 - Group I</h4> <p>Aaron Hayes, Christopher Cronin, Christopher Vanek, Shalvin Deo, Teangi Shoesmith</p></div>
</div> <!-- container --> 
      <!-- Tip Content -->
    <ol id="joyRideTipContent">
      <li data-id="cy" data-button="Next" data-options="tipLocation:left;tipAnimation:fade">
        <h2>Network Graph</h2>
        <p>This graph shows the current network topology of active hosts (squares), switches (triangles) and links (edges).</p>
      </li>
      <li data-id="source" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Source Node</h2>
        <p>Select the address of the desired source node.</p>
      </li>
      <li data-id="dest" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Destination Node</h2>
        <p>Select the address of the desired destination node.</p>
      </li>
      <li data-id="port" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Port</h2>
        <p>Select the desired transport layer source port.</p>
      </li>
      <li data-id="protocol" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Protocol</h2>
        <p>Select the desired transport protocol.</p>
      </li>
      <li data-id="unit" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Unit</h2>
        <p>Select the type of unit for data to be displayed in. </p>
      </li>
      <li data-id="qid" data-button="Next" data-options="tipLocation:right;tipAnimation:fade">
        <h2>Query</h2>
        <p>Press this button to generate the graph for the desired data. </p>
      </li>
      <li data-id="graph" data-button="Next" data-options="tipLocation:top;tipAnimation:fade">
        <h2>Statistical Results</h2>
        <p>This is a data versus time graph, depicting the traffic flow through all flows connecting the source and destination nodes.</p>
      </li>
    </ol>


<script type="text/javascript" src="joyride/jquery.cookie.js"></script>
<script type="text/javascript" src="joyride/modernizr.mq.js"></script>
<script type="text/javascript" src="joyride/jquery.joyride-2.1.js"></script>

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



    <script>
      $(window).load(function() {
	if(document.URL == "http://mininet-vm/web/index.php" || document.URL == "http://localhost/web/index.php"){
        $('#joyRideTipContent').joyride({
          autoStart : true,
          postStepCallback : function (index, tip) {
          if (index == 2) {
            $(this).joyride('set_li', false, 1);
          }
        },
        modal:true,
        expose: true
        });
      }
      });
    </script>

</body>
</html>
