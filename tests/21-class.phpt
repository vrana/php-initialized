--TEST--
Variables inside a class
--FILE--
<?php
class A {
	var $a;
	public $b;
	private $c;
	protected $d;
	
	function f() {
		echo $a;
		echo $this->a;
	}
}
?>
--EXPECTF--
Unitialized variable $a in %s on line 12
