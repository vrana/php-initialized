--TEST--
Exceptions
--FILE--
<?php
try {
	throw new Exception("Exception");
} catch (Exception $e) {
	echo $e->getMessage() . "\n";
}
?>
--EXPECTF--
