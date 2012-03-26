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
 * This is a container for Opt_Xml_Cdata and Opt_Xml_Expression objects.
 */
	class Opt_Xml_Text extends Opt_Xml_Scannable
	{
		/**
		 * Constructs a new text object. If the argument is a
		 * string, it automatically creates the initial Opt_Xml_Cdata
		 * node and initializes it with the argument value.
		 *
		 * @param string $cdata The optional text for CDATA node.
		 */
		public function __construct($cdata = null)
		{
			parent::__construct();
			if(!is_null($cdata))
			{
				$this->appendData($cdata);
			}
		} // end __construct();

		/**
		 * Appends the text to the last character data node. If the last
		 * node of Opt_Xml_Text is not Opt_Xml_Cdata, it automatically
		 * creates the new node and initializes it with the argument value.
		 *
		 * @param string $cdata The string to append.
		 */
		public function appendData($cdata)
		{
			$node = $this->getLastChild();
			if(is_null($node) || $node->getType() != 'Opt_Xml_Cdata' || $node->get('cdata') == true)
			{
				$node = new Opt_Xml_Cdata($cdata);
				$this->appendChild($node);
			}
			else
			{
				$node->appendData($cdata);
			}
		} // end appendData();

		/**
		 * Tests, if the node we want to add is either Opt_Xml_Expression or Opt_Xml_Cdata.
		 * The error is signalized with an exception.
		 *
		 * @internal
		 * @param Opt_Xml_Node $node The node to test.
		 * @throws Opt_APIInvalidNodeType_Exception
		 */
		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() != 'Opt_Xml_Expression' && $node->getType() != 'Opt_Xml_Cdata')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Text', $node->getType());
			}
		} // end _testNode();
	} // end Opt_Xml_Text;
