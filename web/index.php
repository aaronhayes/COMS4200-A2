<!DOCTYPE html>
<html>
<head>

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

</body>
</html>
