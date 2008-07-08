<?php
include "../php-initialized.inc.php";

foreach (glob("*.phpt") as $filename) {
	preg_match("~^--TEST--\n(.*)\n--FILE--\n(.*)\n--EXPECTF--\n(.*)~s", file_get_contents($filename), $match);
	ob_start();
	check_variables($filename);
	if (preg_match('(^' . str_replace("%s", ".*", preg_quote($match[3])) . '$)', ob_get_clean())) {
		//~ echo "passed $filename ($match[1])\n";
	} else {
		echo "failed $filename ($match[1])\n";
	}
}
