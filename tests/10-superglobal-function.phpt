--TEST--
Access to superglobal variable inside a function
--FILE--
<?php
function f() {
	echo $_GET["x"];
}
?>
--EXPECTF--
