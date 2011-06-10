--TEST--
Namespaces and global use
--FILE--
<?php
namespace A\B;
use A\B;
?>
--EXPECTF--
