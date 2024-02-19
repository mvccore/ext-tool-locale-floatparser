<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Tools\Locales;

/**
 * Responsibility:	try to parse floating point number from raw user input string
 *					by locale conventions by `Intl` extension or by automatic
 *					floating point detection (which looks more successful).
 *					If there is not possible to get `float` value, return `NULL`.
 */
interface IFloatParser {

	/**
	 * Create new instance of floating point number parser.
	 * @param string $lang Language international code, lower case, example: `"en" | "de"`.
	 * @param string $locale Country/locale code, upper case, example: `"US" | "UK"`.
	 * @param bool $preferIntlParsing	Boolean flag to prefer `Intl` extension parsing if `Intl` extension (bundled in PHP from 5.3).
	 *									Default is `TRUE` to prefer parsing by automatic floating point detection.
	 */
	public static function CreateInstance ($lang = 'en', $locale = 'US', $preferIntlParsing = TRUE);

	/**
	 * Set language international code, lower case, example: `"en" | "de"`.
	 * @param string|NULL $lang
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetLang ($lang);

	/**
	 * Get language international code, lower case, example: `"en" | "de"`.
	 * @return string|NULL
	 */
	public function GetLang ();

	/**
	 * Set country/locale code, upper case, example: `"US" | "UK"`.
	 * @param string|NULL $locale
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetLocale ($locale);

	/**
	 * Get country/locale code, upper case, example: `"US" | "UK"`.
	 * @return string|NULL
	 */
	public function GetLocale ();

	/**
	 * Set boolean flag about to prefer `Intl` extension parsing  (bundled in PHP from 5.3).
	 * Default is `TRUE` to prefer parsing by automatic floating point detection.
	 * @param bool $preferIntlParsing 
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetPreferIntlParsing ($preferIntlParsing = TRUE);

	/**
	 * Get boolean flag about to prefer `Intl` extension parsing  (bundled in PHP from 5.3).
	 * Default is `TRUE` to prefer parsing by automatic floating point detection.
	 * @return bool
	 */
	public function GetPreferIntlParsing ();

	/**
	 * Try to parse floating point number from raw user input string
	 * by locale conventions by `Intl` extension or by automatic
	 * floating point detection (which looks more successful).
	 * If there is not possible to get `float` value, return `NULL`.
	 * 
	 * If `Intl` extension installed and if `Intl` extension parsing preferred, 
	 * try to parse by `Intl` extension integer first, than floating point number,
	 * but always return floating point number type.
	 * 
	 * If not preferred or not installed, try to determinate floating point in 
	 * user input string automatically and use PHP `floatval()` to parse the result.
	 * If parsing by floatval returns `NULL` and `Intl` extension is installed
	 * but not preferred, try to parse user input by `Intl` extension after it.
	 * 
	 * This function do not throw any exception outside. 
	 * All possible exceptions are caught inside the class.
	 * 
	 * @param  int|float|string $rawInput 
	 * @return int|float|NULL
	 */
	public function Parse ($rawInput);

}
