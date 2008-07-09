--TEST--
Parameters passed by reference can be initialized inside a function
--FILE--
<?php
function f(&$x, &$y) {
	echo $x;
	$y = 5;
}
f($a, $b);
echo $a, $b;
?>
--EXPECTF--
Unitialized variable $a in %s on line 9
