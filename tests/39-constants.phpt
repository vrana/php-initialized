--TEST--
Constants
--FILE--
<?php
define("DEFINED", 1);
echo PHP_VERSION;
echo DEFINED;
echo UNDEFINED;
$a = array();
echo "$a[b]\n";
?>
--EXPECTF--
Uninitialized constant UNDEFINED in %s on line 8
