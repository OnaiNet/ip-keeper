<?php
/**
 * SERVER: Quick and dirty utility to take/store/notify of IP addresses
 *
 * Simply run this from any server with PHP
 * 
 * REQUIREMENTS:
 *  - user of script must have write access to "ip_addresses.php" in same folder
 *
 * @author Kevin Gwynn <kevin.gwynn@gmail.com>
 * @since 2017-01-19
 */
require_once('ip_addresses.php');
global $ip_addresses;  // Yes, this is nasty. Just a simple script.

if (!is_array($ip_addresses)) {
	$ip_addresses = array();
}

// Handle inputs
$ip = preg_replace('/[^0-9\.]/', '', substr(trim(isset($_REQUEST['ip']) ? $_REQUEST['ip'] : $_SERVER['REMOTE_ADDR']), 0, 15));
$name = preg_replace('/[^\w\-_\.]/', '', substr(trim($_REQUEST['name']), 0, 32));
$mail_to = substr(trim($_REQUEST['notify']), 0, 128);

if (empty($name)) {
	handle_error(400, "Missing parameter: name");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	handle_post($name, $ip, $mail_to);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
	handle_get($name);
} else {
	handle_error(400, "Bad request");
}

exit();

/**
 * Handles a GET request; return output about requested IP by name
 */
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
		handle_error(404, "IP address [$name] not found");
	}
}

/**
 * Handle POST request; submit and store IP address and timestamp
 */
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

/**
 * Handle error
 */
function handle_error($response_code, $message) {
	http_response_code($response_code);
	header("Content-Type: text/plain");
	echo $message;
	exit();
}

/**
 * Notify user of a change or registration of the IP by name
 */
function notify($header, $details, $mail_to) {
	if (empty($mail_to)) return;

	$subject = 'ip-keeper: ' . $header;
	$body = get_details($details);
	mail($mail_to, $subject, $body);
}

/**
 * Get details about IP registration
 */
function get_details($details) {
	return json_encode($details);
}

/**
 * Write out/save the IP address table
 */
function write_out() {
	global $ip_addresses;

	file_put_contents('ip_addresses.php', "<?php \$ip_addresses = " . var_export($ip_addresses, true) . "; ?>");
}

?>
