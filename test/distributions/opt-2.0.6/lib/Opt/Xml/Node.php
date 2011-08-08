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
	 * The abstract XML node class - the base of all the node types.
	 *
	 * @abstract
	 */
	abstract class Opt_Xml_Node extends Opt_Xml_Buffer
	{
		protected $_type;
		/**
		 * The parent of the current node.
		 * @var Opt_Xml_Node
		 */
		protected $_parent = null;

		/**
		 * Constructs the new node object.
		 */
		public function __construct()
		{
			/* null */
		} // end __construct();

		/**
		 * Sets the parent of the current node. USE WITH CAUTION!
		 *
		 * @param Opt_Xml_Node $parent The new parent or NULL.
		 */
		public function setParent($parent)
		{
			$this->_parent = $parent;
		} // end setParent();

		/**
		 * Returns the type of the current node (taken from the class name).
		 * @return String
		 */
		public function getType()
		{
			return get_class($this);
		} // end getType();

		/**
		 * Returns the node parent.
		 * @return Opt_Xml_Node
		 */
		public function getParent()
		{
			return $this->_parent;
		} // end getParent();

		/**
		 * Prints the node type.
		 * @return String
		 */
		public function __toString()
		{
			return get_class($this);
		} // end __toString();

		/**
		 * Prepares the node to be collected by the
		 * garbage collector. You must use this method before
		 * removing the last reference to the node to avoid the
		 * memory leak.
		 */
		public function dispose()
		{
			$this->_dispose();
		} // end dispose();

		/**
		 * This method allows to define some custom code that needs to
		 * be executed to dispose the node.
		 *
		 * @internal
		 */
		protected function _dispose()
		{
			$this->_parent = null;
			$this->_buffers = null;
			$this->_args = null;
		} // end _dispose();
	} // end Opt_Xml_Node;