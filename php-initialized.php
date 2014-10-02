#!/usr/bin/env php
<?php
include dirname(__FILE__) . "/php-initialized.inc.php";

$lines = array();
if (isset($argv[1]) && preg_match('/^\d+-\d+$/', $argv[1])) {
	list($min, $max) = explode('-', $argv[1]);
	if ($min != $max) {
		$lines = array($min, $max);
	}
	array_shift($argv);
}

if (!isset($argv[1]) || !glob($argv[1])) {
	echo "Purpose: Checks if PHP code uses only initialized variables\n";
	echo "Usage: php php-initialized.php [line-line] filename.php ...\n";
	exit(1);
}

for ($i=1; $i < count($argv); $i++) {
	foreach (glob($argv[$i]) as $filename) {
		ob_start(function ($s) {
			return preg_replace_callback('/.* on line (\d+)\n(\S+:\d+:.*\n)?/', function (array $match) {
				global $lines;
				list($all, $line) = $match;
				if (!$lines) {
					return $all;
				}
				list($min, $max) = $lines;
				if ($line >= $min && $line <= $max) {
					return $all;
				}
			}, $s);
		});
		check_variables($filename);
		ob_end_flush();
	}
}
