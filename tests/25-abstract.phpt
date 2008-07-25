--TEST--
Abstract variables
--FILE--
<?php
abstract class A {
	abstract function f();
}
$a = new A;
?>
--EXPECTF--
