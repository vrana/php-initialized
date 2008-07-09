<?php
include dirname(__FILE__) . "/php-initialized.inc.php";

if ($argc < 2 || !glob($argv[1])) {
	echo "Purpose: Checks if PHP code uses only initialized variables\n";
	echo "Usage: php php-initialized.php filename.php ...\n";
	exit(1);
}

for ($i=1; $i < $argc; $i++) {
	foreach (glob($argv[1]) as $filename) {
		check_variables($filename);
	}
}
