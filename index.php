<?php
require_once('ip_addresses.php');
global $ip_addresses;

if (!is_array($ip_addresses)) {
	$ip_addresses = array();
}

$ip = preg_replace('/[^0-9\.]/', '', trim(isset($_REQUEST['ip']) ? $_REQUEST['ip'] : $_SERVER['REMOTE_ADDR']));
$name = preg_replace('/[^\w\-_\.]/', '', trim($_REQUEST['name']));
$mail_to = trim($_REQUEST['notify']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	handle_post($name, $ip, $mail_to);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
	handle_get($name);
} else {
	http_response_code(400);
	echo "Bad request";
}

exit();

function handle_get($name) {
	global $ip_addresses;

	if (array_key_exists($name, $ip_addresses)) {
		if (isset($_REQUEST['simple'])) {
			header('Content-Type: text/plain');
			echo $ip_addresses[$name]['ip'];
		} else {
			header('Content-Type: application/json');
			echo get_details($ip_addresses[$name]);
		}
	} else {
		http_response_code(404);
		printf('IP address [%s] not found', $name);
	}
}

function handle_post($name, $ip, $notify_email_address) {
	global $ip_addresses;
	$timestamp = time();
	$datetime = date('Y-m-d H:i:s', $timestamp);
	
	if (!array_key_exists($name, $ip_addresses)) {
		$ip_addresses[$name] = array(
			'name' => $name,
			'ip' => $ip,
			'timestamp' => $timestamp,
			'datetime' => $datetime
		);

		notify('[' . $name . '] Registered [' . $ip . ']', $ip_addresses[$name], $notify_email_address);
	} else {
		$old_ip = $ip_addresses[$name]['ip'];
		
		$ip_addresses[$name]['ip'] = $ip;
		$ip_addresses[$name]['timestamp'] = $timestamp;
		$ip_addresses[$name]['datetime'] = $datetime;
		
		if ($old_ip != $ip) {
			$ip_addresses[$name]['previous'] = $old_ip;
			notify('[' . $name . '] Changed from [' . $old_ip .'] to [' . $ip . ']', $ip_addresses[$name], $notify_email_address);
		}
	}

	if (isset($_REQUEST['simple'])) {
		header('Content-Type: text/plain');
		echo $ip_addresses[$name]['ip'];
	} else {
		header('Content-Type: application/json');
		echo get_details($ip_addresses[$name]);
	}

	write_out();
}

function notify($header, $details, $mail_to) {
	$subject = 'ip-keeper: ' . $header;
	$body = get_details($details);
	mail($mail_to, $subject, $body);
}

function get_details($details) {
	return json_encode($details);
}

function write_out() {
	global $ip_addresses;

	file_put_contents('ip_addresses.php', "<?php \$ip_addresses = " . var_export($ip_addresses, true) . "; ?>");
}

?>
