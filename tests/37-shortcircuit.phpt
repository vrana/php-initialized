--TEST--
Initialization after shortcircuit operator
--FILE--
<?php
(($a = 1) || ($b = 2)) xor ($c = 3);
true ? $d = 4 : $e = 5;
echo $a, $b, $c, $d, $e;
?>
--EXPECTF--
Uninitialized variable $b in %s on line 7
Uninitialized variable $d in %s on line 7
Uninitialized variable $e in %s on line 7
