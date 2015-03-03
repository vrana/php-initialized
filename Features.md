## Supported Features ##

  * Simple assignment
  * [list](http://www.php.net/manual/en/function.list.php) and [unset](http://www.php.net/manual/en/function.unset.php) construction
  * Work with [superglobal variables](http://www.php.net/manual/en/language.variables.superglobals.php)
  * Variables initialization inside the [foreach](http://www.php.net/manual/en/control-structures.foreach.php) loop
  * Function and method parameters
  * Classes and their methods including inheritance
  * Included files
  * Internal function parameters [passed by reference](http://www.php.net/manual/en/functions.arguments.php#functions.arguments.by-reference)
  * Access to global variables through [global](http://www.php.net/manual/en/language.variables.scope.php#language.variables.scope.global) (except functions called from functions)
  * [static](http://www.php.net/manual/en/language.oop5.static.php) properties
  * Abstract methods, interfaces, exceptions, [\_\_halt\_compiler](http://www.php.net/manual/en/function.halt-compiler.php)
  * [Alternative syntax for control structures](http://www.php.net/manual/en/control-structures.alternative-syntax.php)
  * Variables initialization inside the short-circuit operators
  * Access to constant global variables through [$GLOBALS](http://www.php.net/manual/en/reserved.variables.globals.php)
  * Usage of undefined constants like ` $row[id] `

## Unsupported Features ##

  * The visibility scope is a block and not a function (good practice but many false positives)
  * The code is not executed thus [variable variables](http://www.php.net/manual/en/language.variables.variable.php), [variable functions](http://www.php.net/manual/en/functions.variable-functions.php), inclusion of inconstant files, [eval](http://www.php.net/manual/en/function.eval.php) neither ` ${"a"} ` construction is not supported
  * Function has to be defined before usage
  * Only variables are checked, not object properties or array keys
  * ` do { $j = 1; } while ($j) ` and ` for (; ; $j) { $j = 1; } ` are false positives