php-file-parser
===============

PHP 5 command-line file parsing application
Author: Andrew Samuels

USAGE:
	In the main folder:
	$	php myparser.php <file> <command> <option>

List of <command>s:
	find-by-id
	find-all
	find-by-category

List of <options>s:
	<id>
	<category>

Or give myparser.php execute permissions
	$	chmod +x myparser.php

Find by Id:
	$	./myparser.php <file> find-by-id <id>

Find All:
	$	./myparser.php <file> find-all <id>

Find by Category:
	$	./myparser.php <file> find-by-category <category>

Note: Categories with spaces must be put in quotes. (ex. "category 1")
