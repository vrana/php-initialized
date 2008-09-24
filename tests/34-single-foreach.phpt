--TEST--
Single command in foreach
--FILE--
<?php
foreach (array() as $val)
	if (true) {
		echo $val;
	} else {
		echo $val;
	}

echo $val;
?>
--EXPECTF--
Uninitialized variable $val in %s on line 12
