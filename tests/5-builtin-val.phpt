--TEST--
Internal function parameter passed by value
--FILE--
<?php
preg_match('~~', $s);
?>
--EXPECTF--
Unitialized parameter $s in %s on line 5
