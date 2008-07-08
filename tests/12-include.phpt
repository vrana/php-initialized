--TEST--
Initialization of variable inside an included file
--FILE--
<?php
include "./12-include.inc.php";
echo $a;
?>
--EXPECTF--
