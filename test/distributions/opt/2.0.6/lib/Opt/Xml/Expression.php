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
  * A node representing an expression in brackets: { and  }.
  */

	class Opt_Xml_Expression extends Opt_Xml_Node
	{
		/**
		 * The expression stored in the node.
		 * @var String
		 */
		private $_expression;

		/**
		 * Initializes the expression node. The argument determines the
		 * expression that it represents.
		 * @param String $expression The expression represented by the node.
		 */
		public function __construct($expression)
		{
			parent::__construct();
			$this->_expression = $expression;
		} // end __construct();

		/**
		 * Returns the expression stored in the node.
		 * @return String
		 */
		public function getExpression()
		{
			return $this->_expression;
		} // end getExpression();

		/**
		 * Sets a new expression for the node.
		 * @param String $expression The new expression.
		 */
		public function setExpression($expression)
		{
			$this->_expression = $expression;
		} // end setExpression();

		/**
		 * Returns the expression stored in the node.
		 * @return String
		 */
		public function __toString()
		{
			return $this->_expression;
		} // end __toString();
	} // end Opt_Xml_Expression;
