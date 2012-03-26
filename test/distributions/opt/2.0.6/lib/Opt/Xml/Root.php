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
  * XML tree root node.
  */

	class Opt_Xml_Root extends Opt_Xml_Scannable
	{
		private $_prolog = NULL;
		private $_dtd = NULL;

		/**
		 * Constructs the root node.
		 */
		public function __construct()
		{
			parent::__construct();
		} // end __construct();

		/**
		 * Overwritten parent setter - the root node cannot have parents.
		 *
		 * @param Opt_Xml_Node $parent The new parent (ignored).
		 */
		public function setParent($parent)
		{
			/* null */
		} // end setParent();

		/**
		 * Sets the new document prolog.
		 * @param Opt_Xml_Prolog $prolog The new prolog.
		 */
		public function setProlog(Opt_Xml_Prolog $prolog)
		{
			$this->_prolog = $prolog;
		} // end setProlog();

		/**
		 * Sets the new DTD.
		 * @param Opt_Xml_Dtd $dtd The new DTD
		 */
		public function setDtd(Opt_Xml_Dtd $dtd)
		{
			$this->_dtd = $dtd;
		} // end setDtd();

		/**
		 * Returns true, if the document has a prolog.
		 * @return Boolean
		 */
		public function hasProlog()
		{
			return !is_null($this->_prolog);
		} // end hasProlog();

		/**
		 * Returns true, if the document has DTD.
		 * @return Boolean
		 */
		public function hasDtd()
		{
			return !is_null($this->_dtd);
		} // end hasDtd();

		/**
		 * Returns the existing document prolog
		 * @return Opt_Xml_Prolog
		 */
		public function getProlog()
		{
			return $this->_prolog;
		} // end getProlog();

		/**
		 * Returns the existing DTD.
		 * @return Opt_Xml_Dtd
		 */
		public function getDtd()
		{
			return $this->_dtd;
		} // end getDtd();

		/**
		 * Tests, if the specified node can be a child of root.
		 * @param Opt_Xml_Node $node The node to test.
		 */
		protected function _testNode(Opt_Xml_Node $node)
		{
			if($node->getType() == 'Opt_Xml_Expression' && $node->getType() == 'Opt_Xml_Cdata')
			{
				throw new Opt_APIInvalidNodeType_Exception('Opt_Xml_Root', $node->getType());
			}
		} // end _testNode();
	} // end Opt_Xml_Root;
