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
Unitialized variable $a in %s on line 6
