--TEST--
Internal function parameter passed by value
--FILE--
<?php
preg_match('~~', $s);
?>
--EXPECTF--
Unitialized variable $s in %s on line 5
