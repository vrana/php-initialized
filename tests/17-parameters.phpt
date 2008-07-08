--TEST--
Function sees own parameters
--FILE--
<?php
function f($a) {
	echo $a;
}
f(5);
?>
--EXPECTF--
