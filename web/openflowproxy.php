<?

$result;
$method;

if (!isset($_POST['functionname'])) {
	$result['error'] = 'No function name';
} else {
	$method = $_POST['functionname'];
}

if (!isset($result['error'])) {

	$url='http://mininet-vm:8000/OF/';
	$postData;

	if (!isset($_POST['s'])) {
		$postData = '{"method":"get_switches","id":1}';
	} else {
		$postData = '{"method":"get_flow_stats","params":{"dpid":"' . $_POST['s'] . '"},"id":1}';
	}

	$ch = curl_init($url);

	curl_setopt_array($ch, array(
    		CURLOPT_POST => TRUE,
   		CURLOPT_RETURNTRANSFER => TRUE,
    		CURLOPT_HTTPHEADER => array(
        	'Content-Type: application/json'
    	),
    	CURLOPT_POSTFIELDS => $postData
	));

	$response = curl_exec($ch);

	if ($response == FALSE) {
		$result['error'] = 'cURL failed';
	} else {
		$result = $response;
	}


	echo $result;
}
