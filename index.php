<?php
require_once('ip_addresses.php');

$request_type = (sizeof($_POST) > 0) ? 'POST' : 'GET';

$ip = $_SERVER['REMOTE_ADDR'];
$name = $_REQUEST['name'];

if (array_key_exists($ip_addresses[$ip])) {


} else {

}

?>
