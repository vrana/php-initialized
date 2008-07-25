--TEST--
Halt compiler
--FILE--
<?php
__halt_compiler();
echo $a;
?>
--EXPECTF--
