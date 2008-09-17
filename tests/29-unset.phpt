--TEST--
Initialization by unset()
--FILE--
<?php
unset($a);
var_dump($a);
?>
--EXPECTF--
