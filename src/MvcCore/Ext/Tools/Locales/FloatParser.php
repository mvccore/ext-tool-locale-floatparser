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
class FloatParser {

	/**
	 * MvcCore - version:
	 * Comparison by PHP function `version_compare();`.
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0';

	/**
	 * Language international code, lower case, example: `"en" | "de"`.
	 * @var string|NULL
	 */
	protected $lang				= 'en';

	/**
	 * Country/locale code, upper case, example: `"US" | "UK"`.
	 * @var string|NULL
	 */
	protected $locale			= 'US';

	/**
	 * Boolean flag to prefer `Intl` extension parsing if `Intl` extension (bundled in PHP from 5.3).
	 * Default is `FALSE` to prefer parsing by automatic floating point detection.
	 * @var bool
	 */
	protected $preferIntlParsing = FALSE;

	/**
	 * Create new instance of floating point number parser.
	 * @param string $lang Language international code, lower case, example: `"en" | "de"`.
	 * @param string $locale Country/locale code, upper case, example: `"US" | "UK"`.
	 * @param bool $preferIntlParsing	Boolean flag to prefer `Intl` extension parsing if `Intl` extension (bundled in PHP from 5.3).
	 *									Default is `FALSE` to prefer parsing by automatic floating point detection.
	 */
	public static function CreateInstance ($lang = 'en', $locale = 'US', $preferIntlParsing = FALSE) {
		return new static($lang, $locale, $preferIntlParsing);
	}

	/**
	 * Create new instance of floating point number parser.
	 * @param string $lang Language international code, lower case, example: `"en" | "de"`.
	 * @param string $locale Country/locale code, upper case, example: `"US" | "UK"`.
	 * @param bool $preferIntlParsing	Boolean flag to prefer `Intl` extension parsing if `Intl` extension (bundled in PHP from 5.3).
	 *									Default is `TRUE` to prefer parsing by automatic floating point detection.
	 * @return void
	 */
	public function __construct ($lang = 'en', $locale = 'US', $preferIntlParsing = FALSE) {
		$this->lang = $lang;
		$this->locale = $locale;
		$this->preferIntlParsing = $preferIntlParsing;
	}

	/**
	 * Set language international code, lower case, example: `"en" | "de"`.
	 * @param string|NULL $lang
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetLang ($lang) {
		$this->lang = $lang;
		return $this;
	}

	/**
	 * Get language international code, lower case, example: `"en" | "de"`.
	 * @return string|NULL
	 */
	public function GetLang () {
		return $this->lang;
	}

	/**
	 * Set country/locale code, upper case, example: `"US" | "UK"`.
	 * @param string|NULL $locale
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetLocale ($locale) {
		$this->locale = $locale;
		return $this;
	}

	/**
	 * Get country/locale code, upper case, example: `"US" | "UK"`.
	 * @return string|NULL
	 */
	public function GetLocale () {
		return $this->locale;
	}

	/**
	 * Set boolean flag about to prefer `Intl` extension parsing  (bundled in PHP from 5.3).
	 * Default is `FALSE` to prefer parsing by automatic floating point detection.
	 * @param bool $preferIntlParsing 
	 * @return \MvcCore\Ext\Tools\Locales\FloatParser
	 */
	public function SetPreferIntlParsing ($preferIntlParsing = FALSE) {
		$this->preferIntlParsing = $preferIntlParsing;
		return $this;
	}

	/**
	 * Get boolean flag about to prefer `Intl` extension parsing  (bundled in PHP from 5.3).
	 * Default is `FALSE` to prefer parsing by automatic floating point detection.
	 * @return bool
	 */
	public function GetPreferIntlParsing () {
		return $this->preferIntlParsing;
	}

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
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	public function Parse ($rawInput) {
		if (!(is_scalar($rawInput) && !is_bool($rawInput))) 
			return NULL;
		if (is_float($rawInput) || is_int($rawInput))
			return floatval($rawInput);
		$intlExtLoaded = extension_loaded('intl');
		$result = NULL;
		if ($this->preferIntlParsing && $intlExtLoaded) {
			$result = $this->parseByIntl($rawInput);
			if ($result !== NULL) return $result;
			return $this->parseByFloatVal($rawInput);
		} else {
			$result = $this->parseByFloatVal($rawInput);
			if ($result !== NULL) return $result;
			if ($intlExtLoaded) 
				$result = $this->parseByIntl($rawInput);
			return $result;
		}
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int` or `float`.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseByIntl ($rawInput) {
		// set default English int parsing behaviour if not configured
		$langAndLocale = $this->lang && $this->locale
			? $this->lang.'_'.$this->locale
			: 'en_US';
		$intVal = $this->parseIntegerByIntl($rawInput, $langAndLocale);
		if ($intVal !== NULL) 
			return floatval($intVal);
		$floatVal = $this->parseFloatByIntl($rawInput, $langAndLocale);
		if ($floatVal !== NULL) 
			return $floatVal;
		return NULL;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `int`.
	 * @param string $rawInput 
	 * @return int|NULL
	 */
	protected function parseIntegerByIntl ($rawInput, $langAndLocale) {
		$formatter = NULL;
		try {
			$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		try {
			$parsedInt = $formatter->parse($rawInput, \NumberFormatter::TYPE_INT64);
			if (intl_is_failure($formatter->getErrorCode())) 
				return NULL;
		} catch (\IntlException $intlException) {
			return NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawInput);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if (strval($parsedInt) !== $valueFiltered) return NULL;
		return $parsedInt;
	}
	
	/**
	 * Parse user input by `Intl` extension and try to return `float`.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseFloatByIntl ($rawInput, $langAndLocale) {
		// Need to check if this is scientific formatted string. If not, switch to decimal.
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::SCIENTIFIC);
		try {
			$parsedScient = $formatter->parse($rawInput, \NumberFormatter::TYPE_DOUBLE);
			if (intl_is_failure($formatter->getErrorCode())) 
				$parsedScient = NULL;
		} catch (\IntlException $intlException) {
			$parsedScient = NULL;
		}
		$decimalSep  = $formatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
		$groupingSep = $formatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$valueFiltered = str_replace($groupingSep, '', $rawInput);
		$valueFiltered = str_replace($decimalSep, '.', $valueFiltered);
		if ($parsedScient !== NULL && $valueFiltered == strval($parsedScient)) 
			return $parsedScient;
		$formatter = new \NumberFormatter($langAndLocale, \NumberFormatter::DECIMAL);
		try {
			$parsedDecimal = $formatter->parse($rawInput, \NumberFormatter::TYPE_DOUBLE);
			if (intl_is_failure($formatter->getErrorCode())) 
				$parsedDecimal = NULL;
		} catch (\IntlException $intlException) {
			$parsedDecimal = NULL;
		}
		return $parsedDecimal;
	}

	/**
	 * Try to determinate floating point separator if any 
	 * and try to parse user input by `floatval()` PHP function.
	 * @param string $rawInput 
	 * @return float|NULL
	 */
	protected function parseByFloatVal ($rawInput) {
		$result = NULL;
		$rawInput = trim((string) $rawInput);
		$valueToParse = preg_replace("#[^Ee0-9,\.\-]#", '', $rawInput);
		if (strlen($valueToParse) === 0) return NULL;
		$dot = strpos($valueToParse, '.') !== FALSE;
		$comma = strpos($valueToParse, ',') !== FALSE;
		if ($dot && !$comma) {
			$cnt = substr_count($valueToParse, '.');
			if ($cnt == 1) {
				$result = floatval($valueToParse);
			} else {
				$result = floatval(str_replace('.','',$valueToParse));
			}
		} else if (!$dot && $comma) {
			$cnt = substr_count($valueToParse, ',');
			if ($cnt == 1) {
				$result = floatval(str_replace(',','.',$valueToParse));
			} else {
				$result = floatval(str_replace(',','',$valueToParse));
			}
		} else if ($dot && $comma) {
			$dotLastPos = mb_strrpos($valueToParse, '.');
			$commaLastPos = mb_strrpos($valueToParse, ',');
			$dotCount = substr_count($valueToParse, '.');
			$commaCount = substr_count($valueToParse, ',');
			if ($dotLastPos > $commaLastPos && $dotCount == 1) {
				// dot is decimal point separator
				$result = floatval(str_replace(',','',$valueToParse));
			} else if ($commaLastPos > $dotLastPos && $commaCount == 1) {
				// comma is decimal point separator
				$result = floatval(str_replace(['.',','],['','.'],$valueToParse));
			}
		} else if (!$dot && !$comma) {
			$result = floatval($valueToParse);
		}
		return $result;
	}
}
