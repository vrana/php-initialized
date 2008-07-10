--TEST--
Optional parameters passed by reference
--FILE--
<?php
sscanf("1a", "%d%s", $d, $s);
?>
--EXPECTF--
