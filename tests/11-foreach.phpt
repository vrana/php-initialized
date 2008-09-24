--TEST--
Variables initialized in foreach "as"
--FILE--
<?php
foreach (array() as $key => $val) {
	echo $val;
}
echo $val;
?>
--EXPECTF--
Uninitialized variable $val in %s on line 8
