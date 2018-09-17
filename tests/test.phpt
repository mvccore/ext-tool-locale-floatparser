<?php

	include_once(__DIR__ . '/../src/MvcCore/Ext/Tools/Locales/FloatParser.php');
	
	/**
	 * Non `Intl` parsing with automatic floating point detection 
	 * has better results for unexpected user inputs like:
	 *	'1.2 3'			=> 1.23			(float)
	 *	'1.2 3 and 4'	=> 1.234		(float)
	 *	'-1,234,567.89'	=> -1234567.89	(float) 
	 *  '-1.234.567,89'	=> -1234567.89	(float)
	 *  '-1 234 567.89'	=> -1234567.89	(float)
	 *  '-1 234 567,89'	=> -1234567.89	(float)
	 */
	
	$autoParser = \MvcCore\Ext\Tools\Locales\FloatParser::CreateInstance(
		'en',	// international language code, lowercase 
		'US', 	// international country code, uppercase
		FALSE	// FALSE to prefer automatic floating point detection
	);
	var_dump($autoParser->Parse('-1,234,567.89'));	// -1234567.89	(float)
	var_dump($autoParser->Parse('-1.234.567,89'));	// -1234567.89	(float)
	var_dump($autoParser->Parse('-1 234 567.89'));	// -1234567.89	(float)
	var_dump($autoParser->Parse('-1 234 567,89'));	// -1234567.89	(float)
	var_dump($autoParser->Parse('1.2 3 and 4'));	// 1.234		(float)
	var_dump($autoParser->Parse('1.2 3'));			// 1.23			(float)
	// rest is the same as `Intl` floating point parser bellow:
	var_dump($autoParser->Parse('1.8e308'));		// INF			(float)
	var_dump($autoParser->Parse('1.79e308'));		// 1.79E+308	(float)
	var_dump($autoParser->Parse('21474836470'));	// 21474836470	(float)
	var_dump($autoParser->Parse('123'));			// 123.0		(float)
	var_dump($autoParser->Parse('-3.14'));			// -3.14		(float)
	var_dump($autoParser->Parse('1.2e3'));			// 1200			(float)
	var_dump($autoParser->Parse('7E-10'));			// 7.0E-10		(float)
	var_dump($autoParser->Parse('bob-1.3e3'));		// -1300		(float)
	var_dump($autoParser->Parse('nothing'));		// NULL
	var_dump($autoParser->Parse(TRUE));				// NULL
	var_dump($autoParser->Parse([]));				// NULL
	var_dump($autoParser->Parse(new \stdClass));	// NULL

	/**
	 * `Intl` parser needs to set up language and locale more precisely
	 * to user input expectations, which is more error prone in very 
	 * foreing languages and their locale conventions. 
	 * Mostly all languages use floating point character `.` or `,`, but `Intl` 
	 * library has sometimes not the same values for floating point char in 
	 * specific locale as operation system has for specific locale, there is 
	 * a little mess. That's why sometimes there is better to use non `Intl` 
	 * floating point parsing.
	 */
	$intlParser = \MvcCore\Ext\Tools\Locales\FloatParser::CreateInstance(
		'en',	// international language code, lowercase 
		'US', 	// international country code, uppercase
		TRUE	// TRUE to prefer `Intl` extension parsing
	);
	var_dump($intlParser->Parse('-1,234,567.89'));	// -1234567.89	(float)
	var_dump($intlParser->Parse('-1 234 567,89'));	// -1234567		(float)
	var_dump($intlParser->Parse('-1 234 567.89'));	// -1234567.89	(float)
	var_dump($intlParser->Parse('-1 234 567,89'));	// -1234567		(float)
	var_dump($intlParser->Parse('1.2 3 and 4'));	// 1.2			(float)
	var_dump($intlParser->Parse('1.2 3'));			// 1.2			(float)
	// rest is the same as non `Intl` floating point parser above:
	var_dump($autoParser->Parse('1.8e308'));		// INF			(float)
	var_dump($autoParser->Parse('1.79e308'));		// 1.79E+308	(float)
	var_dump($autoParser->Parse('21474836470'));	// 21474836470	(float)
	var_dump($intlParser->Parse('123'));			// 123.0		(float)
	var_dump($intlParser->Parse('-3.14'));			// -3.14		(float)
	var_dump($intlParser->Parse('1.2e3'));			// 1200			(float)
	var_dump($intlParser->Parse('7E-10'));			// 7.0E-10		(float)
	var_dump($intlParser->Parse('bob-1.3e3'));		// -1300		(float)
	var_dump($intlParser->Parse('nothing'));		// NULL
	var_dump($intlParser->Parse(TRUE));				// NULL
	var_dump($intlParser->Parse([]));				// NULL
	var_dump($intlParser->Parse(new \stdClass));	// NULL
	