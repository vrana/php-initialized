--TEST--
Method call
--FILE--
<?php
class A {
	static function f(&$x) {
		$x = 5;
	}
	function g() {
		$this->f($a);
		self::f($b);
		echo $a, $b
	}
}
A::f($c);
echo $c;
?>
--EXPECTF--
