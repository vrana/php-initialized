--TEST--
Usage of $this
--FILE--
<?php
class A {
	var $x;
	function b() {
		$this->x = 5;
	}
}
?>
--EXPECTF--
