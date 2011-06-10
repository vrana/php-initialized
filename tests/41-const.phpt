--TEST--
Class constants
--FILE--
<?php
const x = 1; // PHP 5.3
echo x;
class Constants {
	const y = 1;
	function f() {
		echo self::x;
		echo self::y;
	}
}
echo y;
echo Constants::y;
?>
--EXPECTF--
Uninitialized constant Constants::x in %s on line 10
Uninitialized constant y in %s on line 14
