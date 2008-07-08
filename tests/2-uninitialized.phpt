--TEST--
Usage of uninitialized variable
--FILE--
<?php
echo $x;
?>
--EXPECTF--
Unitialized variable $x in %s on line 5
