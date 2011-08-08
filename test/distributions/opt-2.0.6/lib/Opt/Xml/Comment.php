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
  * The class represents an XML comment.
  */
	class Opt_Xml_Comment extends Opt_Xml_Cdata
	{
		/**
		 * Creates a comment node.
		 * @param string $cdata The initial comment text
		 */
		public function __construct($cdata = '')
		{
			parent::__construct($cdata);
			$this->set('commented', true);
		} // end __construct();

		/**
		 * Validates the comment to ensure the compatibility with XML
		 * syntax.
		 * @param string &$text The reference to a text to validate
		 * @return boolean
		 * @throws Opt_XmlComment_Exception
		 */
		protected function _validate(&$text)
		{
			if(strpos($text, '--') !== false)
			{
				throw new Opt_XmlComment_Exception('--');
			}
			return true;
		} // end _validate();
	} // end Opt_Xml_Comment;
