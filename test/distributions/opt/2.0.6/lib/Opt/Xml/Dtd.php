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

	class Opt_Xml_Dtd
	{
		private $_doctype;

		/**
		 * Creates a new DTD.
		 * @param String $dtd The DTD content.
		 */
		public function __construct($dtd)
		{
			$this->setDoctype($dtd);
		} // end __construct();

		/**
		 * Sets a new doctype content.
		 * @param String $doctype The new content.
		 */
		public function setDoctype($doctype)
		{
			$this->_doctype = $doctype;
		} // end setDoctype();

		/**
		 * Returns the doctype.
		 * @return String.
		 */
		public function getDoctype()
		{
			return $this->_doctype;
		} // end getDoctype();
	} // end Opt_Xml_Dtd;
