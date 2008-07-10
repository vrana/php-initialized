--TEST--
Static variables and properties
--FILE--
<?php
class A {
	static $x;
	function f() {
		static $x;
		echo $x;
	}
}
?>
--EXPECTF--
