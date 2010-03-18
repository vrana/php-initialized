--TEST--
include with dirname(__FILE__)
--FILE--
<?php
include dirname(__FILE__) . "/12-include.inc.php";
echo $a;
?>
--EXPECTF--
