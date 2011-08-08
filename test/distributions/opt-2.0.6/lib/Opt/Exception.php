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
	 * Function definitions
	 */

	function Opt_Error_Handler(Opl_Exception $exception)
	{
		$eh = new Opt_ErrorHandler;
		$eh->display($exception);
	} // end Opt_Error_Handler();

	class Opt_Exception extends Opl_Exception
	{
		private $_data;

		public function setData($data)
		{
			$this->_data = $data;
			return $this;		
		} // end setData();
		
		public function getData()
		{
			return $this->_data;
		} // end getData();
	} // end Opt_Exception;
	
	/*
	 * Parser exception
	 */
	
	class Opt_API_Exception extends Opt_Exception{}

	class Opt_Initialization_Exception extends Opt_API_Exception
	{
		public function __construct()
		{
			$args = func_get_args();
			$this->message = ($args[0] ? 'Cannot '.$args[1].': the library has already been initialized.' : 'Cannot '.$args[1].': the library has already been initialized.');
		} // end __construct();
	} // end Opt_Initialization_Exception;
	
	class Opt_ContentType_Exception extends Opt_API_Exception
	{
		protected $_message = 'Unknown content type: %s.';
	} // end Opt_ContentType_Exception;

	class Opt_ObjectNotExists_Exception extends Opt_API_Exception
	{
		protected $_message = 'The %s "%s" is not defined in OPT.';
	} // end Opt_ObjectNotExists_Exception;
	
	class Opt_TemplateNotFound_Exception extends Opt_API_Exception
	{
		protected $_message = '"%s" has not been found in the source template directory.';
	} // end Opt_TemplateNotFound_Exception;

	class Opt_OutputOverloaded_Exception extends Opt_API_Exception
	{
		protected $_message = 'The screen output is overloaded.';
	} // end Opt_OutputOverloaded_Exception;
	
	class Opt_FilesystemAccess_Exception extends Opt_API_Exception
	{
		protected $_message = 'The %s directory is not %s by PHP.';
	} // end Opt_FilesystemAccess_Exception;

	class Opt_InvalidEntityName_Exception extends Opt_Exception
	{
		protected $_message = '%s is not a valid entity name.';
	} // end Opt_InvalidEntityName_Exception;

	class Opt_TreeInvalidDepth_Exception extends Opt_Exception
	{
		protected $_message = 'The tree element depth is too low: %d. It must be greater or equal to the initial depth %d';
	} // end Opt_TreeInvalidDepth_Exception;

	class Opt_Pluralize_Exception extends Opt_Exception
	{
		protected $_message = 'Invalid pluralize() function use: %s.';
	} // end Opt_Pluralize_Exception;

	/*
	 * User template problems
	 */
	
	class Opt_Template_Exception extends Opt_Exception
	{
	//	public function clean()
	//	{
	//		Opt_Compiler_Class::cleanCompiler();
	//	} // end __construct();
	} // end Opt_Template_Exception;
	
	class Opt_XmlNoProlog_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The template %s has no XML prolog and the OPT settings require you to add it.';
	} // end Opt_XmlNoProlog_Exception;
	
	class Opt_XmlInvalidAttribute_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: incorrect attribute format in tag: %s.';
	} // end Opt_XmlInvalidAttribute_Exception;

	class Opt_XmlDuplicatedAttribute_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: duplicated attribute %s in %s.';
	} // end Opt_XmlDuplicatedAttribute_Exception;

	class Opt_XmlInvalidProlog_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Error while parsing XML prolog: %s.';
	} // end Opt_XmlInvalidAttribute_Exception;

	class Opt_XmlInvalidDoctype_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Error while parsing XML doctype: %s.';
	} // end Opt_XmlInvalidDoctype_Exception;
	
	class Opt_XmlInvalidTagStructure_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: the following tag has an invalid structure: %s.';
	} // end Opt_XmlInvalidTagStructure_Exception;
	
	class Opt_XmlRootElement_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: too many root elements in the template: %s.';
	} // end Opt_XmlRootElement_Exception;

	class Opt_InvalidNamespace_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: invalid namespace format in element: %s.';
	} // end Opt_InvalidNamespace_Exception;

	class Opt_XmlInvalidCharacter_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: the static text "%s" contains raw special XML characters.';
	} // end Opt_XmlRootElement_Exception;
	
	class Opt_XmlInvalidOrder_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: the following tag has been closed in the incorrect order: %s; expected: %s.';
	} // end Opt_XmlInvalidTagStructure_Exception;

	class Opt_UnclosedTag_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: the following tag has not been closed at the end of the template: %s.';
	} // end Opt_UnclosedTag_Exception;

	class Opt_XmlComment_Exception extends Opt_Template_Exception
	{
		protected $_message = 'XML Error: the %s construct is not allowed within XML comments.';
	} // end Opt_XmlComment_Exception;
	
	class Opt_InvalidExpressionModifier_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Invalid expression modifier "%s" in %s.';
	} // end Opt_InvalidExpressionModifier_Exception;
	
	class Opt_InvalidAttributeType_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Invalid type for the attribute "%s" in %s: %s expected.';
	} // end Opt_InvalidAttributeType_Exception;
	
	class Opt_FormatNotFound_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Error parsing %s: the format %s not found.';
	} // end Opt_FormatNotFound_Exception;

	class Opt_FormatNotSupported_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The format %s does not support %s.';
	} // end Opt_FormatNotSupported_Exception;

	class Opt_AssignNotSupported_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The data format of the %s variable does not allow to assign a new value to it.';
	} // end Opt_AssignNotSupported_Exception;

	class Opt_FormatNotDecorated_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The format %s cannot be used without decoration.';
	} // end Opt_FormatNotDecorated_Exception;
	
	class Opt_Expression_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Unexpected token: %s (%s) in expression %s';
	} // end Opt_Expression_Exception;

	class Opt_EmptyExpression_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The specified exception is empty.';
	} // end Opt_EmptyExpression_Exception;
	
	class Opt_FunctionArgument_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Argument %d is not defined in %s()';
	} // end Opt_FunctionArgument_Exception;
	
	class Opt_InvalidArgumentFormat_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The argument format %s in function %s() is not valid.';
	} // end Opt_InvalidArgumentFormat_Exception; 

	class Opt_ExpressionOptionDisabled_Exception extends Opt_Template_Exception
	{
		protected $_message = '%s is not available due to %s';
	} // end Opt_ExpressionOptionDisabled_Exception;
	
	class Opt_ItemNotAllowed_Exception extends Opt_Template_Exception
	{
		protected $_message = '%s %s is not allowed to be used in templates.';
	} // end Opt_ItemNotAllowed_Exception;
	
	class Opt_SysVariableLength_Exception extends Opt_Template_Exception
	{
		protected $_message = 'OPT variable %s is too %s.';
	} // end Opt_SysVariableLength_Exception;

	class Opt_SysVariableUnknown_Exception extends Opt_Template_Exception
	{
		protected $_message = 'Unknown action in OPT variable %s.';
	} // end Opt_SysVariableUnknown_Exception;
	
	class Opt_SysVariableInvalidUse_Exception extends Opt_Template_Exception
	{
		protected $_message = 'OPT variable %s can be used in %s only.';
	} // end Opt_SysVariableInvalidUse_Exception;
	
	class Opt_AttributeNotDefined_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The required attribute "%s" has not been defined in "%s".';
	} // end Opt_AttributeNotDefined_Exception;

	class Opt_AttributeEmpty_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The required attribute "%s" is empty in "%s".';
	} // end Opt_AttributeEmpty_Exception;
	
	class Opt_InvalidCallback_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The callback for %s is invalid.';
	} // end Opt_InvalidCallback_Exception;

	class Opt_UnknownEntity_Exception extends Opt_Template_Exception
	{
		protected $_message = 'The entity %s is not registered in the XML parser.';
	} // end Opt_UnknownEntity_Exception;

	/*
	 * Compiler code and API problems.
	 */	
	class Opt_Compiler_Exception extends Opt_Exception
	{
	//	public function clean()
	//	{
	//		Opt_Compiler_Class::cleanCompiler();
	//	} // end __construct();
	} // end Opt_Compiler_Exception;
	
	class Opt_UnknownProcessor_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Unknown processor for tag %s.';
	} // end Opt_UnknownProcessor_Exception;
	
	class Opt_CompilerLocked_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Cannot compile %s: the compiler already compiles another template: %s.';
	} // end Opt_CompilerLocked_Exception;
	
	class Opt_CompilerCodeBufferConflict_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Linker error: OPT code buffer conflict detected. More than %d snippets in %s for $s node.';
	} // end Opt_CompilerCodeBufferConflict_Exception;
	
	class Opt_APINoWildcard_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: No wildcard provided in the prototype list for the sorting function.';
	} // end Opt_APINoWildcard_Exception;
	
	class Opt_APIInvalidBorders_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: Invalid insertion borders.';
	} // end Opt_APIInvalidBorders_Exception;
	
	class Opt_APIInvalidNodeType_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: Invalid node type added to %s object: %s.';
	} // end Opt_APIInvalidBorders_Exception;

	class Opt_APIHookNotDefined_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: Hook %s is not defined in %s.';
	} // end Opt_APIInvalidBorders_Exception;
	
	class Opt_APIMissingDefaultValue_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: Missing default value for optional attribute "%s" in %s.';
	} // end Opt_APIMissingDefaultValue_Exception;

	class Opt_APINoDataReturned_Exception extends Opt_Compiler_Exception
	{
		protected $_message = 'Compiler API: No data returned for %s while %s.';
	} // end Opt_APINoDataReturned_Exception;
	
	/*
	 * Instruction problems
	 */
	class Opt_Instruction_Exception extends Opt_Template_Exception
	{
	//	public function clean()
	//	{
	//		Opt_Compiler_Class::cleanCompiler();
	//	} // end __construct();
	} // end Opt_Instruction_Exception;
	
	class Opt_InstructionInvalidParent_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Invalid use of "%s". The parent must be "%s".';
	} // end Opt_InstructionInvalidParent_Exception;
	
	class Opt_InstructionTooManyItems_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Too many "%s" elements inside %s. %s allowed.';
	} // end Opt_InstructionTooManyItems_Exception;
	
	class Opt_SectionExists_Exception extends Opt_Instruction_Exception
	{
		protected $_message = '%s: Section %s already exists on the stack.';
	} // end Opt_SectionExists_Exception;

	class Opt_SectionNotFound_Exception extends Opt_Instruction_Exception
	{
		protected $_message = '%s: section %s has not been found on the stack.';
	} // end Opt_SectionNotFound_Exception;
	
	class Opt_InstructionInvalidLocation_Exception extends Opt_Instruction_Exception
	{
		protected $_message = '"%s" must be located within "%s".';	
	} // end Opt_InstructionInvalidLocation_Exception;
	
	class Opt_TreeContent_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'opt:tree error: %s is a dynamic tag that generates some PHP code.';
	} // end Opt_TreeContent_Exception;
	
	class Opt_SnippetRecursion_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Infinite recursion detected: trying to insert the snippet "%s" inside its insertion.';
	} // end Opt_SnippetRecursion_Exception;
	
	class Opt_CompilerRecursion_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Infinite recursion detected: trying to compile the template "%s" inside its inclusion.';
	} // end Opt_CompilerRecursion_Exception;
	
	class Opt_InheritanceRecursion_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Infinite recursion detected: trying to extend already extended template "%s".';
	} // end Opt_InheritanceRecursion_Exception;
	
	class Opt_IncludeNoAttributes extends Opt_Instruction_Exception
	{
		protected $_message = 'The required attributes either "from" or "file" have not been defined in %s.';
	} // end Opt_IncludeNoAttributes;
	
	class Opt_ComponentNotActive_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Cannot use %s: no component is active.';
	} // end Opt_ComponentNotInitialized_Exception;
	
	class Opt_CycleNoValues_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Trying to declare a cycle "%s" without values.';
	} // end Opt_CycleNoValues_Exception;

	class Opt_AttributeInvalidNamespace_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Cannot apply opt:single attribute to an OPT tag: %s';
	} // end Opt_AttributeInvalidNamespace_Exception;

	class Opt_InvalidValue_Exception extends Opt_Instruction_Exception
	{
		protected $_message = 'Invalid value of %s';
	} // end Opt_InvalidValue_Exception;

	class Opt_CannotBeNested_Exception extends Opt_Instruction_Exception
	{
		protected $_message = '%s instruction cannot be nested: %s';
	} // end Opt_CannotBeNested_Exception;

	/*
	 * Other exceptions.
	 */
	
	class Opt_NotImplemented_Exception extends Opt_Exception
	{
		protected $_message = 'The %s is not implemented.';
	} // end Opt_NotImplemented_Exception;
	
	class Opt_NotSupported_Exception extends Opt_Exception
	{
		protected $_message = 'The %s is not supported: %d.';
	} // end Opt_NotSupported_Exception;
