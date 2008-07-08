--TEST--
Usage of an initialized global variable inside a function
--FILE--
<?php
function f() {
	global $x;
	echo $x;
}
$x = 5;
f();
?>
--EXPECTF--
