--TEST--
Function doesn't see global variables
--FILE--
<?php
function f() {
	echo $a;
}
$a = 5;
f();
?>
--EXPECTF--
Uninitialized variable $a in %s on line 6
