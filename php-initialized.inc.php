<?php
/** Recursive function checking the variables initialization
* @param string $filename name of the processed file
* @param array [$initialized] initialized variables in keys
* @param string [$function] inside a function definition
* @param string [$class] inside a class definition
* @param array [$tokens] result of token_get_all() without whitespace, computed from $filename if null
* @param int [$i] position in $tokens
* @return mixed $initialized in the end of code, $i in the end of block
* @link http://code.google.com/p/php-initialized/
* @author Jakub Vrana, http://php.vrana.cz
* @copyright 2008 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
*/
function check_variables_ex($filename, $initialized = array(), $function = "", $class = "", $tokens = null, $i = 0) {
	static $function_globals, $function_parameters, $function_calls, $extends;
	static $globals = array('$php_errormsg', '$_SERVER', '$_GET', '$_POST', '$_COOKIE', '$_FILES', '$_ENV', '$_REQUEST', '$_SESSION'); // not $GLOBALS
	if (func_num_args() < 2) {
		$function_globals = array();
		$function_parameters = array();
		$function_calls = array();
		$extends = array();
	}
	if (!isset($tokens)) {
		$tokens = array();
		foreach (token_get_all(file_get_contents($filename)) as $token) {
			if (!in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
				$tokens[] = $token;
			}
		}
	}
	$in_list = false;
	for (; $i < count($tokens); $i++) {
		$token = $tokens[$i];
		//~ echo (is_array($token) ? token_name($token[0]) . "\t" . trim($token[1]) : "\t$token") . "\n";
		//~ continue;
		
		// variables
		if ($token[0] === T_VARIABLE && $token[1] !== '$GLOBALS') {
			$variable = $token[1];
			if ($tokens[$i-1][0] === T_DOUBLE_COLON) {
				// ignore static properties
			} elseif (isset($function_globals[$function][$variable])) {
				if (!$function_globals[$function][$variable]) {
					$function_globals[$function][$variable] = ($in_list || $tokens[$i+1] === '=' ? true : "$filename on line $token[2]");
				}
			} elseif ($in_list || $tokens[$i+1] === '=' || $function_calls[count($function_calls) - 1][0]) {
				$initialized[$variable] = true;
			} elseif (!isset($initialized[$variable]) && !in_array($variable, $globals)) {
				if (isset($function_parameters[$function][$variable])) {
					$function_parameters[$function][$variable] = false;
				} else {
					echo "Uninitialized variable $token[1] in $filename on line $token[2]\n";
					$initialized[$variable] = true;
				}
			}
		} elseif ($token[0] === T_LIST || $token[0] === T_UNSET) {
			$in_list = true;
		} elseif ($in_list && $token === ')') {
			$in_list = false;
		
		// foreach
		} elseif ($token[0] === T_AS) {
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$initialized[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i+1] !== ')');
		
		// catch
		} elseif ($token[0] === T_CATCH) {
			$locals = array();
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$locals[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i+1] !== '{');
			array_pop($function_calls);
			$i = check_variables_ex($filename, $initialized + $locals, $function, $class, $tokens, $i+2);
		
		// global
		} elseif ($token[0] === T_GLOBAL && $function) {
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$function_globals[$function][$tokens[$i][1]] = false;
				}
			} while ($tokens[$i] !== ';');
		
		// static
		} elseif ($token[0] === T_STATIC && $tokens[$i+1][0] !== T_FUNCTION && $tokens[$i+2][0] !== T_FUNCTION) {
			do {
				$i++;
				if ($function && $tokens[$i][0] === T_VARIABLE) {
					$initialized[$tokens[$i][1]] = true;
				}
			} while ($tokens[$i] !== ';');
		
		// function definition
		} elseif ($token[0] === T_FUNCTION && !in_array(T_ABSTRACT, array($tokens[$i-1][0], $tokens[$i-2][0], $tokens[$i-3][0]), true)) {
			$locals = ($class && $tokens[$i-1][0] !== T_STATIC && $tokens[$i-2][0] !== T_STATIC ? array('$this' => true) : array());
			$i++;
			if ($tokens[$i] === '&') {
				$i++;
			}
			$name = ($class ? "$class::" : "") . $tokens[$i][1];
			$function_parameters[$name] = array();
			do {
				$i++;
				if ($tokens[$i][0] === T_VARIABLE) {
					$function_parameters[$name][$tokens[$i][1]] = ($tokens[$i-1] === '&');
					if ($tokens[$i-1] !== '&') {
						$locals[$tokens[$i][1]] = true;
					}
				}
			} while ($tokens[$i+1] !== '{');
			$i = check_variables_ex($filename, $locals, $name, ($function ? "" : $class), $tokens, $i+2);
		
		// function call
		} elseif ($token[0] === T_STRING && $tokens[$i+1] === '(') {
			$name = $token[1];
			$class_name = "";
			if (($tokens[$i-1][0] === T_DOUBLE_COLON && $tokens[$i-2][1] === 'self') || ($tokens[$i-1][0] === T_OBJECT_OPERATOR && $tokens[$i-2][1] === '$this')) {
				$class_name = $class;
			} elseif ($tokens[$i-1][0] === T_DOUBLE_COLON && $tokens[$i-2][0] === T_STRING) {
				$class_name = $tokens[$i-2][1];
			}
			$i++;
			if ($class_name ? method_exists($class_name, $name) : function_exists($name)) {
				$reflection = ($class_name ? new ReflectionMethod($class_name, $name) : new ReflectionFunction($name));
				$parameters = array();
				foreach ($reflection->getParameters() as $parameter) {
					$parameters[] = ($parameter->isPassedByReference() ? '$' . $parameter->getName() : '');
				}
				$function_calls[] = $parameters;
			} else {
				if ($class_name) {
					while ($class_name && !$function_parameters["$class_name::$name"]) {
						$class_name = $extends[$class_name];
					}
					$name = "$class_name::$name";
				}
				$function_calls[] = array_values((array) $function_parameters[$name]);
				if (!$function && is_array($function_globals[$name])) {
					foreach ($function_globals[$name] as $variable => $info) {
						if ($info === true) {
							$initialized[$variable] = true;
						} elseif (is_string($info) && !isset($initialized[$variable])) {
							echo "Uninitialized global $variable in $info\n: called in $filename on line $token[2]\n";
						}
					}
				}
			}
		
		// class
		} elseif ($token[0] === T_CLASS) {
			$i++;
			$token = $tokens[$i];
			while ($tokens[$i+1] !== '{') {
				if ($tokens[$i][0] === T_EXTENDS) {
					$extends[$tokens[$i-1][1]] = $tokens[$i+1][1];
				}
				$i++;
			}
			$i = check_variables_ex($filename, array(), $function, $token[1], $tokens, $i+2);
		} elseif ($token[0] === T_VAR || (in_array($token[0], array(T_PUBLIC, T_PRIVATE, T_PROTECTED), true) && $tokens[$i+1][0] === T_VARIABLE)) {
			do {
				$i++;
			} while ($tokens[$i] !== ';');
		
		// include
		} elseif (in_array($token[0], array(T_INCLUDE, T_REQUIRE, T_INCLUDE_ONCE, T_REQUIRE_ONCE), true)) {
			if ($tokens[$i+1][0] === T_CONSTANT_ENCAPSED_STRING && $tokens[$i+2] === ';') {
				$initialized += check_variables_ex(stripslashes(substr($tokens[$i+1][1], 1, -1)), $initialized, $function, $class);
			}
		
		// interface
		} elseif ($token[0] === T_INTERFACE) {
			while ($tokens[$i+1] !== '}') {
				$i++;
			}
		
		// halt_compiler
		} elseif ($token[0] === T_HALT_COMPILER) {
			return $initialized;
		
		// blocks
		} elseif ($token === '(') {
			$function_calls[] = array();
		} elseif ($token === ')') {
			array_pop($function_calls);
		} elseif ($token === ',' && $function_calls) {
			if ($function_calls[count($function_calls) - 1][0] !== '$...') {
				array_shift($function_calls[count($function_calls) - 1]);
			}
		} elseif ($token === '{' || $token[0] === T_CURLY_OPEN || $token[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
			$i = check_variables_ex($filename, $initialized, $function, $class, $tokens, $i+1);
		} elseif ($token === '}') {
			return $i;
		}
		
	}
	return $initialized;
}

/** Print usage of uninitialized variables
* @param string $filename name of the processed file
* @return array properly initialized variables
*/
function check_variables($filename) {
	$old_error = error_reporting(E_ALL & ~E_NOTICE);
	$return = check_variables_ex($filename);
	error_reporting($old_error);
	return $return;
}
