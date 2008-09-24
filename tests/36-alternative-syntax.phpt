--TEST--
Alternative syntax
--FILE--
<?php
if (false):
	$var = 1;
	echo $var;
endif;
echo $var;
?>
--EXPECTF--
Uninitialized variable $var in %s on line 9
