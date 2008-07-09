--TEST--
Usage of $this inside a static method
--FILE--
<?php
class A {
	static function f() {
		echo $this->a;
	}
	function g() {
		echo $this->a;
	}
}
?>
--EXPECTF--
Unitialized variable $this in %s on line 7
