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

 /*
  * Character data node implementation.
  */

	class Opt_Xml_Cdata extends Opt_Xml_Node
	{
		private $_text = '';
		static public $mode;

		/**
		 * Constructs a new Opt_Xml_Cdata node.
		 *
		 * @param String $cdata The initial text.
		 */
		public function __construct($cdata)
		{
			parent::__construct();
			self::$mode != Opt_Class::QUIRKS_MODE and $this->_validate($cdata);
			$this->_text = $cdata;
		} // end __construct();

		/**
		 * Appends the string to the existing node content.
		 * @param String $cdata The new string.
		 */
		public function appendData($cdata)
		{
			self::$mode != Opt_Class::QUIRKS_MODE and $this->_validate($cdata);
			$this->_text .= $cdata;
		} // end appendData();

		/**
		 * Inserts the string in the specified offset.
		 * @param Integer $offset The offset.
		 * @param String $cdata The new string.
		 */
		public function insertData($offset, $cdata)
		{
			$this->_text = substr($this->_text, 0, $offset).$cdata.substr($this->_text, $offset, strlen($this->_text)-$offset);
		} // end insertData();

		/**
		 * Deletes the specified part of the content.
		 * @param Integer $offset The position of the first character to delete.
		 * @param Integer $count The number of characters to delete.
		 */
		public function deleteData($offset, $count)
		{
			$this->_text = substr($this->_text, 0, $offset).substr($this->_text, $offset+$count, strlen($this->_text)-$offset-$count);
		} // end insertData();

		/**
		 * Replaces the specified amount of the original text with the part of the new string.
		 * @param Integer $offset The position of the first character to replace.
		 * @param Integer $count The number of characters to replace.
		 * @param String $text The replacing string.
		 */
		public function replaceData($offset, $count, $text)
		{
			$this->_text = substr($this->_text, 0, $offset).substr($text, 0, $count).substr($this->_text, $offset+$count, strlen($this->_text)-$offset-$count);
		} // end replaceData();

		/**
		 * Returns the specified part of the content.
		 * @param Integer $offset The position of the first character to return.
		 * @param Integer $count The number of characters to return.
		 * @return String
		 */
		public function substringData($offset, $count)
		{
			return substr($this->_text, $offset, $count);
		} // end substringData();

		/**
		 * Returns the content length.
		 * @return Integer
		 */
		public function length()
		{
			return strlen($this->_text);		
		} // end length();

		/**
		 * Returns the content.
		 * @return String
		 */
		public function __toString()
		{
			return $this->_text;
		} // end __toString();

		/**
		 * Validates the inserted text.
		 * @param String $text The text to validate
		 * @return Boolean
		 */
		protected function _validate(&$text)
		{
			return true;
			if($this->get('cdata'))
			{
				return true;
			}
			if(strcspn($text, '<>') != strlen($text))
			{
				throw new Opt_XmlInvalidCharacter_Exception(htmlspecialchars($text));
			}
			return true;
		} // end _validate();
	} // end Opt_Xml_Cdata;