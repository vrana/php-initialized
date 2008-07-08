--TEST--
Access to superglobal variable
--FILE--
<?php
echo $_GET["x"];
?>
--EXPECTF--
