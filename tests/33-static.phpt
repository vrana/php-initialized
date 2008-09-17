--TEST--
Static class variables
--FILE--
<?php
class A {
	static $x = 5;
	function f() {
		echo self::$x;
	}
}
echo A::$x;
?>
--EXPECTF--
