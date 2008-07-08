<?php
function check_variables($filename, $initialized = array(), $function = "", $tokens = null, $i = 0) {
	static $function_globals = array();
	static $globals = array('$php_errormsg', '$_SERVER', '$_GET', '$_POST', '$_COOKIE', '$_FILES', '$_ENV', '$_REQUEST', '$_SESSION'); // not $GLOBALS
	if (!isset($tokens)) {
		$tokens = array();
		foreach (token_get_all(file_get_contents($filename)) as $token) {
			if (!in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
				$tokens[] = $token;
			}
		}
	}
	for (; $i < count($tokens); $i++) {
		$token = $tokens[$i];
		//~ echo (is_array($token) ? token_name($token[0]) . "\t" . trim($token[1]) : "\t$token") . "\n";
		//~ continue;
		
		// variables
		if ($token[0] === T_VARIABLE && $token[1] !== '$GLOBALS') {
			$variable = $token[1];
			if (isset($function_globals[$function][$variable])) {
				if (!$function_globals[$function][$variable]) {
					$function_globals[$function][$variable] = ($tokens[$i+1] === '=' ? true : "$filename on line $token[2]");
				}
			} elseif ($tokens[$i+1] === '=') {
				$initialized[$variable] = true;
				$i++;
			} elseif (!isset($initialized[$variable]) && !in_array($variable, $globals)) {
				echo "Unitialized variable $token[1] in $filename on line $token[2]\n";
			}
		} elseif ($token[0] === T_LIST) {
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$initialized[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i] !== ')');
		
		// foreach
		} elseif ($token[0] === T_AS) {
			$locals = array();
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$locals[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i+1] !== '{');
			$i = check_variables($filename, $initialized + $locals, $function, $tokens, $i+2);
		
		} elseif ($token[0] === T_GLOBAL) {
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$function_globals[$function][$tokens[$i][1]] = false; //! usage outside of a function
				}
			} while ($tokens[$i] !== ';');
		
		// functions
		} elseif ($token[0] === T_FUNCTION) {
			$token = $tokens[++$i];
			$locals = array();
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$locals[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i+1] !== '{');
			$i = check_variables($filename, $locals, $token[1], $tokens, $i+2);
		} elseif ($token[0] === T_STRING && $tokens[$i+1] === '(') {
			if (function_exists($token[1])) {
				$reflection = new ReflectionFunction($token[1]);
				$parameters = $reflection->getParameters();
				$param = 0;
				$depth = 0;
				do { //! check inner functions call, allow assignment
					$i++;
					if ($tokens[$i] === '(') {
						$depth++;
					} elseif ($tokens[$i] === ')') {
						$depth--;
					} elseif ($depth == 1) {
						if ($tokens[$i] === ',') {
							$param++;
						} elseif ($tokens[$i][0] === T_VARIABLE && !isset($initialized[$tokens[$i][1]]) && !in_array($tokens[$i][1], $globals)) {
							if (!$parameters[$param]->isPassedByReference()) {
								echo "Unitialized parameter " . $tokens[$i][1] . " in $filename on line " . $tokens[$i][2] . "\n";
							} else {
								$initialized[$tokens[$i][1]] = true;
							}
						}
					}
				} while ($depth > 0);
			} elseif (is_array($function_globals[$token[1]])) {
				foreach ($function_globals[$token[1]] as $variable => $info) {
					if ($info === true) {
						$initialized[$variable] = true;
					} elseif (is_string($info) && !isset($initialized[$variable])) {
						echo "Unitialized global $variable in $info\n";
					}
				}
			}
		
		// includes
		} elseif (in_array($token[0], array(T_INCLUDE, T_REQUIRE, T_INCLUDE_ONCE, T_REQUIRE_ONCE), true)) { //! respect once
			if ($tokens[$i+1][0] === T_CONSTANT_ENCAPSED_STRING && $tokens[$i+2] === ';') {
				$initialized += check_variables(stripslashes(substr($tokens[$i+1][1], 1, -1)), $initialized, $function);
			}
		
		// blocks
		} elseif ($token === '{') {
			$i = check_variables($filename, $initialized, $function, $tokens, $i+1);
		} elseif ($token === '}') {
			return $i;
		}
		
	}
	return $initialized;
}
