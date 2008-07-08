<?php
include dirname(__FILE__) . "/php-initialized.inc.php";
if ($argc != 2 || !is_file($argv[1])) {
	echo "Purpose: Checks if PHP code uses only initialized variables\n";
	echo "Usage: php php-initialized.php filename.php\n";
}
check_variables($argv[1]);
echo "Done.\n";
