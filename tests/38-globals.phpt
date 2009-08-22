--TEST--
Usage of $GLOBALS
--FILE--
<?php
function f() {
	$GLOBALS["a"];
}
f();
?>
--EXPECTF--
Uninitialized global a in %s on line 6
: called in %s on line 8
