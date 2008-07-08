<?php
function check_variables($filename, $initialized = array(), $function = "", $tokens = null, $i = 0) {
	static $function_globals = array(), $function_parameters = array(), $function_calls = array();
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
			} elseif ($tokens[$i+1] === '=' || $function_calls[count($function_calls) - 1][0]) {
				$initialized[$variable] = true;
			} elseif (!isset($initialized[$variable]) && !in_array($variable, $globals)) {
				if (isset($function_parameters[$function][$variable])) {
					$function_parameters[$function][$variable] = false;
				} else {
					echo "Unitialized variable $token[1] in $filename on line $token[2]\n";
				}
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
			} while ($tokens[$i+1] !== '{'); //! allow single commands
			array_pop($function_calls);
			$i = check_variables($filename, $initialized + $locals, $function, $tokens, $i+2);
		
		} elseif ($token[0] === T_GLOBAL && $function) {
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$function_globals[$function][$tokens[$i][1]] = false;
				}
			} while ($tokens[$i] !== ';');
		
		// functions
		} elseif ($token[0] === T_FUNCTION) {
			$i++;
			$token = $tokens[$i];
			$locals = array();
			$parameters = array();
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$parameters[$tokens[$i][1]] = ($tokens[$i-1] === '&');
					if ($tokens[$i-1] !== '&') {
						$locals[$tokens[$i][1]] = true;
					}
				}
			} while ($tokens[$i+1] !== '{');
			$function_parameters[$token[1]] = $parameters;
			$i = check_variables($filename, $locals, $token[1], $tokens, $i+2);
		} elseif ($token[0] === T_STRING && $tokens[$i+1] === '(') {
			$i++;
			if (function_exists($token[1])) {
				$reflection = new ReflectionFunction($token[1]);
				$parameters = array();
				foreach ($reflection->getParameters() as $parameter) {
					$parameters[] = $parameter->isPassedByReference();
				}
				$function_calls[] = $parameters;
			} else {
				$function_calls[] = array_values($function_parameters[$token[1]]);
				if (is_array($function_globals[$token[1]])) {
					foreach ($function_globals[$token[1]] as $variable => $info) {
						if ($info === true) {
							$initialized[$variable] = true;
						} elseif (is_string($info) && !isset($initialized[$variable])) {
							echo "Unitialized global $variable in $info\n: called in $filename on line $token[2]\n";
						}
					}
				}
			}
		
		// includes
		} elseif (in_array($token[0], array(T_INCLUDE, T_REQUIRE, T_INCLUDE_ONCE, T_REQUIRE_ONCE), true)) {
			if ($tokens[$i+1][0] === T_CONSTANT_ENCAPSED_STRING && $tokens[$i+2] === ';') {
				$initialized += check_variables(stripslashes(substr($tokens[$i+1][1], 1, -1)), $initialized, $function);
			}
		
		// blocks
		} elseif ($token === '(') {
			$function_calls[] = array();
		} elseif ($token === ')') {
			array_pop($function_calls);
		} elseif ($token === ',') {
			array_shift($function_calls[count($function_calls) - 1]);
		} elseif ($token === '{') {
			$i = check_variables($filename, $initialized, $function, $tokens, $i+1);
		} elseif ($token === '}') {
			return $i;
		}
		
	}
	return $initialized;
}
