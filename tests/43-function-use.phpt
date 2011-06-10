--TEST--
Anonymous function with use
--FILE--
<?php
$a = 3;
$b = 3;
function () use ($a) {
	echo "$a\n";
	echo "$b\n";
};
?>
--EXPECTF--
Uninitialized variable $b in %s on line 9
