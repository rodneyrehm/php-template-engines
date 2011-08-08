<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

	/**
	 * A generic container for the template functions created for
	 * the autoloader purporses only.
	 */
	class Opt_Function
	{
		/**
		 * The cycle iterator for cycle() function.
		 * @static
		 * @var int
		 */
		static private $_cycleIterator = 0;

		/**
		 * The method allows to create aggregate functions that operate
		 * on container. It calls the specified function for all the
		 * container elements provided in the first function call
		 * argument.
		 *
		 * @static
		 * @param Callback $callback A valid function callback
		 * @param Array $args The list of function arguments.
		 * @return Container Processed container
		 */
		static public function processContainer($callback, $args)
		{			
			$result = array();
			foreach($args[0] as $idx => $value)
			{
				$args[0] = $value;
				$result[$idx] = call_user_func_array($callback, $args);
			}
			
			return $result;
		} // end processContainer();

		/**
		 * Returns true, if the specified value is a valid OPT container.
		 *
		 * @static
		 * @param Mixed $value The value to test.
		 * @return Boolean True, if the value is really a container
		 */
		static public function isContainer($value)
		{
			return is_array($value) || (is_object($value) && ($value instanceof Iterator || $value instanceof IteratorAggregate));
		} // end isContainer();

		/**
		 * Returns the first non-empty argument.
		 *
		 * @static
		 * @param mixed ... The arguments.
		 * @return mixed The first non-empty argument
		 */
		static public function firstof()
		{
			$args = func_get_args();
			$cnt = sizeof($args);
			for($i = 0; $i < $cnt; $i++)
			{
				if(!empty($args[$i]))
				{
					return $args[$i];
				}
			}
		} // end firstof();

		/**
		 * Returns true, if the container contains the specified value.
		 *
		 * @static
		 * @param string $item The container
		 * @param mixed $value The value
		 * @return True, if the value exists in the container.
		 */
		static public function contains($item, $value)
		{
			if(is_array($item))
			{
				return in_array($value, $item);
			}
			elseif($item instanceof ArrayAccess && $item instanceof Traversable)
			{
				foreach($item as $r)
				{
					if($r === $value)
					{
						return true;
					}
				}
			}
			return false;
		} // end contains();

		/**
		 * Returns true, if the container contains the specified key.
		 *
		 * @static
		 * @param string $item The container
		 * @param mixed $key The key
		 * @return True, if the key exists in the container.
		 */
		static public function containsKey($item, $key)
		{
			if(is_array($item) || $item instanceof ArrayAccess)
			{
				return isset($item[$key]);
			}
			return false;
		} // end containsKey();

		/**
		 * Puts the specified string $delim between every two characters of $string.
		 * By default, it puts spaces between the $string characters.
		 *
		 * @static
		 * @param String|Container $string The string to be processed.
		 * @param String $delim = ' ' Character delimiter.
		 * @return String|Container The modified text.
		 */
		static public function spacify($string, $delim = ' ')
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'spacify'), array($string, $delim));
			}
		
			$ns = '';
			$len = strlen($string);
			$tpl = Opl_Registry::get('opt');

			for($i = 0; $i < $len; $i++)
			{
				if($tpl->charset == 'utf-8' && ord($string[$i]) > 127)
				{
					break;
				}
				$ns .= $string[$i];
				if($i + 1 < $len)
				{
					$ns .= $delim;
				}
			}
			return $ns;
		} // end spacify();

		/**
		 * Makes an indentation to the specified string.
		 *
		 * @static
		 * @param String|Container $string The source string or the container
		 * @param Int $num The length of the indentation
		 * @param String $with = ' ' The indent character
		 * @return String|Container The modified string
		 */
		static public function indent($string, $num, $with = ' ')
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'indent'), array($string, $num, $with));
			}
		
			return preg_replace('/([\\r\\n]{1,2})/', '$1'.str_repeat($with, $num), $string);
		} // end indent();

		/**
		 * Strips the groups of white characters in $string into a single space.
		 *
		 * @static
		 * @param String|Container $string The source string or the container.
		 * @return String|Container The modified string.
		 */
		static public function strip($string)
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'strip'), array($string));
			}

			return trim(preg_replace('/\s+/', ' ', $string));
		} // end strip();

		/**
		 * Pads the string or a container.
		 *
		 * @static
		 * @param string|container $string The string to pad.
		 * @param int $length The pad length
		 * @param string $padString The string used for padding.
		 * @param string $type The padding type.
		 * @return string|container The modified string.
		 */
		static public function pad($string, $length, $padString = ' ', $type = 'right')
		{
			if(!is_scalar($padString))
			{
				$padString = ' ';
			}
			switch((string)$type)
			{
				case 'left':
					$type = STR_PAD_LEFT;
					break;
				case 'both':
					$type = STR_PAD_BOTH;
					break;
				default:
					$type = STR_PAD_RIGHT;
			}
			if(self::isContainer($string))
			{
				$list = array();
				foreach($string as $idx => $str)
				{
					if(is_scalar($str))
					{
						$list[$idx] = str_pad((string)$str, (int)$length, (string)$padString, $type);
					}
				}
				return $list;
			}

			return str_pad((string)$string, (int)$length, (string)$padString, $type);
		} // end pad();

		/**
		 * Counts the occurences of substring in another string.
		 *
		 * @static
		 * @param String|Container $string The source string or the container.
		 * @param string $searched The searched string
		 * @return String|Container The modified string.
		 */
		static public function countSubstring($string, $searched)
		{
			if(self::isContainer($string))
			{
				$i = 0;
				foreach($string as $item)
				{
					if(is_string($item))
					{
						$i += substr_count($item, $searched);
					}
				}
				return $i;
			}

			return substr_count($string, $searched);
		} // end countSubstring();

		/**
		 * Truncates the string or the container of strings to the specified length.
		 * If the string was really shortened, appends the $etc to the end of it.
		 * By default, the function can truncate a string in the middle of a word.
		 * If you want to round the truncation to the whole words, change the default
		 * value of the last argument to false.
		 *
		 * @static
		 * @param String|Container $string The source string or the container of strings.
		 * @param Int $length Truncate to the specified length.
		 * @param String $etc = '' The extra string appended to the result, if it was really truncated.
		 * @param Boolean $break = true Do we allow to break the words?
		 * @return String|Container The modified string.
		 */
		static public function truncate($string, $length, $etc = '', $break = true)
		{
			if(self::isContainer($string))
			{
				return self::processContainer(array('Opt_Function', 'truncate'), array($string, $length, $etc, $break));
			}

			if($length == 0)
			{
				return '';
			}
			$strlen = strlen($string);
			if($strlen > $length)
			{
				if(!$break)
				{
					$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length));
				}
				return substr($string, 0, $length).$etc;
			}
			return $string;
		} // end truncate();

		/**
		 * Tries to break the string every specified number of characters.
		 *
		 * @param String $string The input string
		 * @param Integer $width The width of a line
		 * @param String $break The break string
		 * @param Boolean $cut Whether to cut too long words
		 * @return String
		 */
		static public function wordwrap($string, $width, $break = '<br />', $cut = null)
		{
			if(!is_null($break))
			{
				$break = str_replace(array('\\n', '\\r', '\\t', '\\\\'), array("\n", "\r", "\t", '\\'), $break);
			}
			return wordwrap($string, $width, $break, $cut);
		} // end wordwrap();

		/**
		 * Formats the input number to be a valid money amount string. The function is not available on
		 * systems without strfmon capabilities (i.e. Microsoft Windows).
		 *
		 * @param Number $number The input number
		 * @param String $format The format (if not specified, using locale settings)
		 * @return String
		 */
		static public function money($number, $format = null)
		{
			if(self::isContainer($number))
			{
				return self::processContainer(array('Opt_Function', 'money'), array($number, $format));
			}
			$opt = Opl_Registry::get('opt');
			$format = (is_null($format) ? $opt->moneyFormat : $format);
			return money_format($format, $number);
		} // end money();

		/**
		 * Formats the input number to look nice in the text. If the extra arguments
		 * are not present, they are taken from OPT configuration.
		 *
		 * @param Number $number The input number
		 * @param Integer $d1 The number of decimals
		 * @param String $d2 The decimal separator
		 * @param String $d3 The thousand separator
		 * @return String
		 */
		static public function number($number, $d1 = null, $d2 = null, $d3 = null)
		{
			if(self::isContainer($number))
			{
				return self::processContainer(array('Opt_Function', 'number'), array($number, $d1, $d2, $d3));
			}
			$opt = Opl_Registry::get('opt');
			$d1 = ($d1 === null ? $opt->numberDecimals : $d1);
			$d2 = ($d2 === null ? $opt->numberDecPoint : $d2);
			$d3 = ($d3 === null ? $opt->numberThousandSep : $d3);
			return number_format($number, $d1, $d2, $d3);
		} // end number();

		/**
		 * Returns the absolute value of a number.
		 *
		 * @param Number|Container $items The input number of container of numbers
		 * @return Number|Container
		 */
		static public function absolute($items)
		{
			if(self::isContainer($items))
			{
				return self::processContainer('abs', array($items));
			}
			
			return abs($items);
		} // end absolute();

		/**
		 * Sums all the numbers in the specified container.
		 *
		 * @param Container $items The container of numbers.
		 * @return Number
		 */
		static public function sum($items)
		{
			if(self::isContainer($items))
			{				
				$sum = 0;
				foreach($items as $item)
				{
					if(!self::isContainer($item))
					{
						$sum += $item;
					}
				}
				return $sum;
			}
			return null;
		} // end sum();

		/**
		 * Returns the average value of the numbers in a container.
		 *
		 * @param Container $items The container of numbers
		 * @return Number
		 */
		static public function average($items)
		{
			if(self::isContainer($items))
			{				
				$sum = 0;
				$cnt = 0;
				foreach($items as $item)
				{
					if(!self::isContainer($item) && !is_null($item))
					{
						$sum += $item;
						$cnt++;
					}
				}
				if($cnt > 0)
				{
					return $sum / $cnt;
				}
			}
			return null;
		} // end average();

		/**
		 * Returns the standard deviation of the numbers in a container.
		 *
		 * @param Container $items The container of numbers.
		 * @return Number
		 */
		static public function stddev($items)
		{
			$average = self::average($items);
			
			if(is_null($average))
			{
				return null;
			}
			
			$sum = 0;
			$cnt = 0;
			foreach($items as $item)
			{
				if(!self::isContainer($item) && !is_null($item))
				{
					$sum += pow($item - $average, 2);
					$cnt++;
				}
			}
			return sqrt(($sum / ($cnt - 1)));
		} // end stddev();

		/**
		 * Replaces the lowercase characters in $item with the uppercase.
		 *
		 * @static
		 * @param String|Container $item The string or the container.
		 * @return String|Container The modified string
		 */
		static public function upper($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'upper'), array($item));
			}
			
			return strtoupper($item);
		} // end upper();

		/**
		 * Replaces the uppercase characters in $item with the lowercase.
		 *
		 * @static
		 * @param String|Container $item The string or the container.
		 * @return String|Container The modified string
		 */
		static public function lower($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'lower'), array($item));
			}
			
			return strtolower($item);
		} // end lower();

		/**
		 * Capitalizes the first character of $item.
		 *
		 * @static
		 * @param String|Container $item The string or the container.
		 * @return String|Container The modified string
		 */
		static public function capitalize($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'capitalize'), array($item));
			}
			
			return ucfirst($item);
		} // end capitalize();

		/**
		 * Changes the newline characters to the BR tag.
		 *
		 * @static
		 * @param String|Container $item The string or the container.
		 * @return String|Container The modified string
		 */
		static public function nl2br($item)
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'nl2br'), array($item));
			}
			
			return nl2br($item);
		} // end nl2br();

		/**
		 * Removes the HTML tags from $item.
		 *
		 * @static
		 * @param String|Container $item The string or the container.
		 * @param String $what The list of allowable tags that must not be removed.
		 * @return String|Container The modified string
		 */
		static public function stripTags($item, $what = '')
		{
			if(self::isContainer($item))
			{
				return self::processContainer(array('Opt_Function', 'stripTags'), array($item, $what));
			}
			
			return strip_tags($item, $what);
		} // end stripTags();

		/**
		 * Helps displaying a nice range between two numbers, if they are
		 * not equal, i.e.: "5 - 10" or the number itself, if they are
		 * equal. By default, the second number is evaluated as the current
		 * year, which allows to create a range of years for the Copyright foot
		 * at the bottom of the website.
		 *
		 * @static
		 * @param Int $number1 Starting number
		 * @param Int $number2=null Ending number
		 * @return String The result string
		 */
		static public function range($number1, $number2 = null)
		{
			if($number2 === null)
			{
				$number2 = date('Y');
			}
			if($number2 == $number1)
			{
				return $number1;
			}
			return $number1.' - '.$number2;			
		} // end range();

		/**
		 * Returns true, if the specified string is a valid URL.
		 *
		 * @static
		 * @param String $address The string to test.
		 * @return Boolean True, if the specified string is a valid URL.
		 */
		static public function isUrl($address)
		{
			return filter_var($address, FILTER_VALIDATE_URL) !== false;
		} // end isUrl();

		/**
		 * Returns true, if the specified URL or file path points to the
		 * image file. The recognition bases on the file extension and
		 * currently recognizes JPG, PNG, GIF, SVG and BMP.
		 *
		 * @static
		 * @param String $address The URL or filesystem path to test.
		 * @return Boolean True, if the specified address points to an image.
		 */
		static public function isImage($address)
		{
			$result = @parse_url($address);
			if(is_array($result))
			{
				if(isset($result['path']))
				{
					// Try to obtain the file extension
					if(($id = strrpos($result['path'], '.')) !== false)
					{
						if(in_array(substr($result['path'], $id+1, 3), array('jpg', 'png', 'gif', 'svg', 'bmp')) || in_array(substr($result['path'], $id+1, 4), array('jpeg')))
						{
							return true;
						}
					}
				}
			}
			return false;
		} // end isImage();

		/**
		 * Returns the next element of the specified list of items.
		 *
		 * @return Mixed
		 */
		static public function cycle()
		{
			$items = func_get_args();
			if(is_array($items[0]))
			{
				$items = $items[0];
			}

			return ($items[(self::$_cycleIterator++) % sizeof($items) ]);
		} // end cycle();

		/**
		 * Creates an entity for the specified string. If used with the 'u:' modifier,
		 * it allows to display the entities in the output document.
		 *
		 * @static
		 * @param String $name A valid entity name.
		 * @return String
		 */
		static public function entity($name)
		{
			if(!preg_match('/^(([a-zA-Z\_\:]{1}[a-zA-Z0-9\_\:\-\.]*)|(\#((x[a-fA-F0-9]+)|([0-9]+))))$/', $name))
			{
				throw new Opt_InvalidEntityName_Exception($name);
			}
			return '&'.$name.';';
		} // end entity();

		/**
		 * Attempts to generate a plural form of the specified string according
		 * to the language rules and the specified number.
		 *
		 * @static
		 * @throws Opt_Pluralize_Exception
		 * @param int $number The number to determine the plural form.
		 * @param string $singularForm The base singular form.
		 * @param string ... The plural forms (if we do not use the translation interface)
		 * @return string The grammar form for the specified number
		 */
		static public function pluralize($number, $singularForm)
		{
			$args = func_get_args();
			$opt = Opl_Registry::get('opt');
			if(sizeof($args) == 0)
			{
				// Use the translation interface.
				
				$tf = $opt->getTranslationInterface();

				if($tf === null || !method_exists($tf, 'pluralize'))
				{
					throw new Opt_Pluralize_Exception('the translation interface does not support pluralization.');
				}

				return $tf->pluralize($number, $singularForm);
			}
			else
			{
				// Translate on the fly.

				$argument = null;
				if(is_object($opt->pluralForms) && is_callable($opt->pluralForms))
				{
					$closure = $opt->pluralForms;
					$argument = $closure($number);
				}
				else
				{
					$last = sizeof($args) - 1;
					$i = 0;
					$arg = null;
					foreach($opt->pluralForms as $expression => $argument)
					{
						$arg = $argument;
						if($i != $last)
						{
							eval('$__result = ('.str_replace('%d', $number, $expression).');');
							if($__result === true)
							{
								break;
							}
						}
					}
				}
				if(!isset($args[$argument+1]))
				{
					throw new Opt_Pluralize_Exception('unknown grammar form number: '.$argument.'.');
				}
				return $args[$argument+1];
			}
		} // end pluralize();

		/**
		 * Turns URL-s into clickable links.
		 *
		 * @static
		 * @param string $text The text to parse.
		 * @param string $class The optional CSS class.
		 * @param string $target The target.
		 * @return string The parsed text.
		 */
		static public function autoLink($text, $class = null, $target = '_blank')
		{
			$extra = '';
			if($class !== null)
			{
				$extra .= ' class="'.(string)htmlspecialchars($class).'"';
			}
			if($target !== null)
			{
				$extra .= ' target="'.(string)htmlspecialchars($target).'"';
			}
			return str_replace(array('<a href="www.', '<a href="ftp.'),
				array('<a href="http://www.', '<a href="ftp://ftp.'),
				preg_replace('/(((http|https|ftp|ftps|gopher)\:\/\/)|(www\.)|(ftp\.))([a-zA-Z0-9\_\-]+\@)?(([0-9a-fA-F\:]{6,39})|(([a-zA-Z0-9\-\_]+\.)*[a-zA-Z0-9\-\_]+(\:[0-9]{1,5})?)|(\[[0-9a-fA-F\:]{6,39}\]\:[0-9]{1,5}))(\/[a-zA-Z0-9\_\-\&\;\?\/\.\=\+\#]*)?/is',
				'<a href="$0"'.$extra.'>$0</a>', htmlspecialchars($text)));
		} // end autoLink();

		/**
		 * Builds XML attributes from an array. The function allows to specify the
		 * ignore list in either text- or array format.
		 *
		 * @param Array $attributes The list of attributes.
		 * @param Mixed $ignoreList The list of ignored items.
		 * @param String $prepend The string prepended to the attribute list.
		 */
		static public function buildAttributes($attributes, $ignoreList = array(), $prepend = '')
		{
			if(is_string($ignoreList))
			{
				$ignoreList = explode(',', $ignoreList);
				array_walk($ignoreList, 'trim');
			}

			foreach($attributes as $name => $value)
			{
				if(!in_array($name, $ignoreList))
				{
					// Do not accept null values.
					if($value === null)
					{
						continue;
					}
					// If the name is correct...
					if(preg_match('/^([a-zA-Z0-9\.\_\-]+\:)?([a-zA-Z0-9\.\_\-]+)$/', $name))
					{
						$prepend .= ''.$name.'="'.htmlspecialchars($value).'" ';
					}
				}
			}
			return rtrim($prepend);
		} // end buildAttributes();
	} // end Opt_Function;
