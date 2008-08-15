<?php
include "../php-initialized.inc.php";

function xhtml_open_tags($s) {
	$return = array();
	preg_match_all('~<([^>]+)~', $s, $matches);
	foreach ($matches[1] as $val) {
		if ($val{0} == "/") {
			array_pop($return);
		} elseif (substr($val, -1) != "/") {
			$return[] = $val;
		}
	}
	return $return;
}

if ($_GET["coverage"]) {
	xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
}

foreach (glob("*.phpt") as $filename) {
	preg_match("~^--TEST--\n(.*)\n--FILE--\n(.*)\n--EXPECTF--\n(.*)~s", file_get_contents($filename), $match);
	ob_start();
	check_variables($filename);
	if (!preg_match('(^' . str_replace("%s", ".*", preg_quote($match[3])) . '$)', ob_get_clean())) {
		echo "failed $filename ($match[1])\n";
	} else {
		//~ echo "passed $filename ($match[1])\n";
	}
}

if ($_GET["coverage"]) {
	$coverage = xdebug_get_code_coverage();
	$coverage = $coverage[realpath("../php-initialized.inc.php")];
	$file = explode("<br />", highlight_file("../php-initialized.inc.php", true));
	unset($prev_color);
	$s = "";
	for ($l=0; $l <= count($file); $l++) {
		$line = $file[$l];
		$color = "#C0FFC0"; // tested
		switch ($coverage[$l+1]) {
			case -1: $color = "#FFC0C0"; break; // untested
			case -2: $color = "Silver"; break; // dead code
			case null: $color = ""; break; // not executable
		}
		if (!isset($prev_color)) {
			$prev_color = $color;
		}
		if ($prev_color != $color || !isset($line)) {
			echo "<div" . ($prev_color ? " style='background-color: $prev_color;'" : "") . ">" . $s;
			$open_tags = xhtml_open_tags($s);
			foreach (array_reverse($open_tags) as $tag) {
				echo "</" . preg_replace('~ .*~', '', $tag) . ">";
			}
			echo "</div>\n";
			$s = ($open_tags ? "<" . implode("><", $open_tags) . ">" : "");
			$prev_color = $color;
		}
		$s .= "$line<br />\n";
	}
}
