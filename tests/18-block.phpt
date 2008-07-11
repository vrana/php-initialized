--TEST--
It is not recommended to use a variable outside of a block
--FILE--
<?php
if (true) {
	$a = 5;
}
echo $a;
?>
--EXPECTF--
Uninitialized variable $a in %s on line 8
