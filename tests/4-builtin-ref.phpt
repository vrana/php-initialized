--TEST--
Internal function parameter passed by reference
--FILE--
<?php
preg_match('~~', '', $match);
echo $match[0];
?>
--EXPECTF--
