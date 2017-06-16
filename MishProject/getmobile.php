<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=yes, minimum-scale=1.0">
		<title>And your phone number is...</title>
		<style>
			body {
				font: 17px sans-serif;
				color: #444;
				margin: 20px;
			}
			
		</style>
	</head>
	<body>
<?php



$header = apache_request_headers(); 
var_dump($header);
if(isset($header['x-up-calling-line-id']) && !empty($header['x-up-calling-line-id'])){ 
	echo $phone_number = $header['x-up-calling-line-id']; 
}else{ 
	echo $error_phone_number = "Phone number missing...!"; 
}

/*$headers = apache_request_headers();


foreach ($headers as $header => $value) {
	if ($header == 'x-up-calling-line-id') {
		$phone_number = $value;
		break;
	}
}

if (isset($phone_number)) {
	echo "<p>Your phone number is:</p><h1>$value</h1><p>Did you know that this number is sent to <strong>every</strong> website you visit (when not using Wi-Fi)?</p>";
} else {
	echo '<p>No phone number found. Make sure Wi-Fi is turned off and <a href="./">try again</a>.</p>';
}
*/
?>
		
	</body>
</html>
