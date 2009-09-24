--TEST--
Usage of $GLOBALS
--FILE--
<?php
function f() {
	global $b;
	$GLOBALS["a"];
	$GLOBALS["b"];
}
$b = 5;
f();
?>
--EXPECTF--
Uninitialized global a in %s on line 7
: called in %s on line 11
