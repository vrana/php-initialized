--TEST--
Call of two nested functions
--FILE--
<?php
preg_match('~~', preg_replace('~~', '', $s, -1, $count), $match);
?>
--EXPECTF--
Unitialized variable $s in %s on line 5
