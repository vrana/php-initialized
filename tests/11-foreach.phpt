--TEST--
Variables initialized in foreach "as"
--FILE--
<?php
foreach (array() as $key => $val) {
	
}
?>
--EXPECTF--
