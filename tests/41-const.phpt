--TEST--
Class constants
--FILE--
<?php
const X = 1; // PHP 5.3
echo X;
class Constants {
	const Y = 1;
	function f() {
		echo self::X;
		echo self::Y;
	}
}
echo Y;
echo Constants::Y;
?>
--EXPECTF--
Uninitialized constant Constants::X in %s on line 10
Uninitialized constant Y in %s on line 14
