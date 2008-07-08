--TEST--
Initialization of variable inside an included file and usage in another file
--FILE--
<?php
include "./12-include.inc.php";
include "./13-include2.inc.php";
?>
--EXPECTF--
