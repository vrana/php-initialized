--TEST--
Constants
--FILE--
<?php
define("defined", 1);
echo PHP_VERSION;
echo defined;
echo undefined;
$a = array();
echo "$a[b]\n";
?>
--EXPECTF--
Uninitialized constant undefined in %s on line 8
