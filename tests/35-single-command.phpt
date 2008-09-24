--TEST--
Variable initialization in a single command
--FILE--
<?php
if (false)
	$auth = true;

echo $auth;
?>
--EXPECTF--
Uninitialized variable $auth in %s on line 8
