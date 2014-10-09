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
		$postData = array(
			'method' => $method,
			'id' => 1
		);
	} else {
                $postData = array(
                        'method' => $method,
			'params' => '{"dpid":"' . $_POST['s'] . '"}',
			'id' => 1
                );
	}

	$ch = curl_init($url);

	curl_setopt_array($ch, array(
    		CURLOPT_POST => TRUE,
   		CURLOPT_RETURNTRANSFER => TRUE,
    		CURLOPT_HTTPHEADER => array(
        	'Content-Type: application/json'
    	),
    	CURLOPT_POSTFIELDS => json_encode($postData)
	));

	$response = curl_exec($ch);

	if ($response == FALSE) {
		$result['error'] = 'cURL failed';
	} else {
		$result = $response;
	}


	echo $result;
}
