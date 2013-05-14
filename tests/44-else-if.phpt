--TEST--
else followed by control structure
--FILE--
<?php
if (false) {
} else if (true) {
}
$a = 1;
echo $a;
?>
--EXPECTF--
