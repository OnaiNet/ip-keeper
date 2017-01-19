<?php
require_once('ip_addresses.php');

$request_type = (sizeof($_POST) > 0) ? 'POST' : 'GET';

$ip = $_SERVER['REMOTE_ADDR'];
$name = $_REQUEST['name'];

function handle_get($name) {
	if (array_key_exists($ip_addresses[$name])) {
		echo $ip_addresses[$name]['ip'];
	} else {
		http_response_code(404);
		printf('IP address [%s] not found', $name);
	}
}

function handle_post($name) {
	global $ip_addresses;
}

?>
