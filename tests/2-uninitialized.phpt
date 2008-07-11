--TEST--
Usage of uninitialized variable
--FILE--
<?php
echo $x;
?>
--EXPECTF--
Uninitialized variable $x in %s on line 5
