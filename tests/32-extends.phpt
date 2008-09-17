--TEST--
Method with extends
--FILE--
<?php
class A {
	static function f(&$x) {
		$x = 5;
	}
}
class B extends A {
}
B::f($a);
echo $a;
?>
--EXPECTF--
