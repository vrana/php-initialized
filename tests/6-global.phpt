--TEST--
Initialization of a global variable inside a function
--FILE--
<?php
function f() {
	global $x;
	$x = 5;
}
f();
echo $x;
?>
--EXPECTF--
