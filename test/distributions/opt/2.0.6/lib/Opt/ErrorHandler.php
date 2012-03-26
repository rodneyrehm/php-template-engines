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
	 * This class is an extension to the default OPL error handler.
	 * See the help of OPL to get more details on using it.
	 */
	class Opt_ErrorHandler extends Opl_ErrorHandler
	{	
		protected $_library = 'Open Power Template';
		protected $_context = array(
			'Opl_Debug_Exception' => array(
				'BasicConfiguration' => array(),
				'Backtrace' => array(),
				'ErrorInfo' => array(1 => 'This exception is generated to inform about a possible bug in OPL/OPT code.
					Check out whether it is not caused by any of the installed extensions, and if not - please report
					it to the Invenzzia Bugtracker, providing all the debug information.'),
			),
			'Opt_TemplateNotFound_Exception' => array(
				'ErrorInfo' => array(1 => 'The template cannot be located in one of <em>sourceDir</em> directories.
					Check the file name for mistakes and the filesystem permissions.')
			),
			'Opt_OutputOverloaded_Exception' => array(
				'ErrorInfo' => array(1 => 'The default screen output allows you to send <em>only one</em> XML template
					to the browser using the parse() method. Because in the template, all the opened tags must be
					closed, this would lead to produce invalid XML output. To modularize your templates, you have
					to use <em>opt:include</em> instruction and/or the template inheritance.')
			),
			'Opt_FilesystemAccess_Exception' => array(
				'ErrorInfo' => array(1 => 'OPT encountered a problem with filesystem permissions. Check if the script
					has a proper permission to read from <em>sourceDir</em> and write to <em>compileDir</em>.'),
				'BasicConfiguration' => array()
			),
			'Opt_InvalidEntityName_Exception' => array(
				'ErrorInfo' => array(1 => 'This exception is thrown by the <em>entity()</em> template function if
					the specified argument is not a valid XML entity name. The entity name must be either a decimal/hexadecimal
					character number or the character name built of letters, number, underscores, pauzes and colons.'),
			),
			'Opt_TreeInvalidDepth_Exception' => array(
				'ErrorInfo' => array(1 => 'This exception is thrown by the <em>opt:tree()</em> template instruction which attempts
					to render a tree structure. The list elements must contain a "depth" field that specifies the element
					depth. The first element determines the minimum depth and no element can have a lower value there.
					In order to remove the problem, please check your PHP script and/or database to see, what depth values
					it provides.'),
			),
			'Opt_SnippetRecursion_Exception' => array(
				'StackInfo' => array(1 => 'Snippet call stack'),
				'ErrorInfo' => array(1 => 'The compiler detected the infinite snippet insertion. For example, the snippet A inserts B, which inserts A again, and so on.
					The stack output shows all the currently inserted snippets. The last one contains the invalid call.')
			),
			'Opt_CompilerRecursion_Exception' => array(
				'StackInfo' => array(1 => 'Template call stack'),
				'ErrorInfo' => array(1 => 'The exception is thrown, if the compiler detecs the infinite template inclusion
					(for example, A includes B, which includes A), mostly in opt:root tags. The stack output shows all the currently
					included templates. The last one, marked on red, contains the invalid inclusion.')
			),
			'Opt_InheritanceRecursion_Exception' => array(
				'StackInfo' => array(1 => 'Template extension stack'),
				'ErrorInfo' => array(1 => 'This exception indicates a situation, when the template A extends B which extends
					A again. This leads to the infinite recursion that surely crashes your scripts. Use the presented stack
					trace to find out which templates cause the problem and fix the inherited template names in opt:extend
					tags.')
			),			
			'Opt_TreeContent_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'This error occurs, because you have enclosed the tree instruction <em>opt:content</em> tag
					within another dynamic tag, e.g. <em>opt:section</em> or <em>opt:if</em>. This is not allowed due of the tree
					compilation algorithm used in OPT. Basically, remember: you can neither display the tree branches conditionally
					nor repeat them several times.')
			),
			'Opt_InstructionTooManyItems_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'Many instructions consist of many XML tags and some of them must follow various restrictions.
					For example, in <em>opt:if</em> the <em>opt:else</em> tag may occur at least once. This exception concerns similar
					situations - a tag appears somewhere too many times and the compiler does not know, how to compile it. Please refer
					the OPT manual in order to check, how to use this instruction.')
			),
			'Opt_InstructionInvalidParent_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'Many instructions consist of many XML tags and some of them must follow various restrictions.
					In this case, the reported tag must be enclosed directly within another tag. Any other location causes the exception.
					Please refer the OPT manual in order to check, how to use this instruction.')
			),
			'Opt_XmlInvalidOrder_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'The tags must be closed in the same order they have been opened. Check the tags mentioned in
					the exception message to ensure that you have closed them properly. Note that single tags need to be ended
					with " /&gt;" sequence.')
			),
			'Opt_XmlNoProlog_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'By default, the OPT templates must be valid XML documents. This means also that they have to have
					an XML prolog (it is not sent to the user browser). You may either add a prolog to your templates or switch the compiler
					to less restrictive mode by setting the configuration option <em>prologRequired</em> to <strong>false</strong>.')
			),
			'Opt_XmlRootElement_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'By default, the OPT templates must be valid XML documents. This means that there can be only one
					root tag in the file. If you are writing a sub-template for your website, consider using a fake root node provided by
					OPT: <em>opt:root</em>. Alternatively, you may also switch the compiler to less restrictive mode by setting <em>singleRootNode</em>
					to <strong>false</strong>.')
			),
			'Opt_SectionNotFound_Exception' => array(
				'TemplateInfo' => array(),
				'StackInfo' => array(1 => 'Actual section stack'),
				'ErrorInfo' => array(1 => 'The nested sections can be connected together with a relationship. By default,
					OPT tries to establish such relationship automatically, but you can modify the default behaviour with
					the "parent" attribute. This exception occurs, because the template tries use the "parent" attribute
					to create a relationship to a section that does not exist. Check whether the specified section names
					are correct.')
			),
			/* Compiler API errors */
			'Opt_APIMissingDefaultValue_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'The optional XML tag attributes must have the default value defined in order to be parsed properly.
					The optional values are provided in the third field of the attribute definition: (OPTIONAL, TYPE, optional_value).'),
				'BugtrackerInfo' => array(),
				'Backtrace' => array()
			),
			'Opt_APIInvalidNodeType_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'The XML tree nodes cannot be placed wherever you want them to be. Opt_Xml_Expression and Opt_Xml_Cdata
					cannot contain children. Moreover, they may be themselves the children of Opt_Xml_Text only and Opt_Xml_Root is limited to
					be the root node only.'),
				'BugtrackerInfo' => array(),
				'Backtrace' => array()
			),
			'Opt_APINoWildcard_Exception' => array(
				'TemplateInfo' => array(),
				'ErrorInfo' => array(1 => 'The method that sorts the nodes on the children list must know,
					where it should locate the nodes that have not been specified explicitely. The position,
					where such unmatched nodes should go is specified with a wildcard provided as one of
					the sort list elements.'),
				'BugtrackerInfo' => array(),
				'Backtrace' => array()
			),
			'__UNKNOWN__' => array(
				'BasicConfiguration' => array()
			),
		);

		/**
		 * The informator that prints the basic OPT configuration to the error
		 * output.
		 *
		 * @param Opt_Exception $exception Exception
		 */
		protected function _printBasicConfiguration($exception)
		{
			$compileMode = array(
				Opt_Class::CM_DEFAULT => 'Default',
				Opt_Class::CM_REBUILD => 'Rebuild',
				Opt_Class::CM_PERFORMANCE => 'Performance'			
			);
		
			$tpl = Opl_Registry::get('opt');
			echo "  			<p class=\"directive\">Source directories:</p>\r\n";
			echo "  			<ol>\r\n";
			foreach($tpl->sourceDir as $name => $path)
			{
				echo '  			<li><code>'.$name.'</code>: <span>'.$path."</span></li>\r\n";
			}
			echo "  			</ol>\r\n";
			echo '  			<p class="directive">Compilation directory: <span>'.$tpl->compileDir."</span></p>\r\n";
			echo '  			<p class="directive">Compilation mode: <span'.($tpl->compileMode == Opt_Class::CM_REBUILD ? ' class="bad"' : '').'>'.$compileMode[$tpl->compileMode]."</span></p>\r\n";
		} // end _printBasicConfiguration();

		/**
		 * The informator that prints the currently compiled template name
		 * for the compilation errors.
		 *
		 * @param Opt_Exception $exception Exception
		 */
		protected function _printTemplateInfo($exception)
		{
			echo "		<p class=\"directive\">Template: <span>".Opt_Compiler_Class::getCurrentTemplate()."</span></p>\r\n";
		} // end _printTemplateInfo();

		/**
		 * The informator that displays an information that the exception is caused by
		 * the bug in the source code and should be reported, or at least, consulted
		 * with the Invenzzia team.
		 *
		 * @param Opt_Exception $exception Exception
		 */
		protected function _printBugtrackerInfo($exception)
		{
			echo "  			<p><strong>Important information:</strong> this exception concerns the instruction API code and should occur only <u>if you are writing the new instruction for OPT</u>. If you encounter this exception in one of OPT default instructions, please visit the <a href=\"http://bugs.invenzzia.org\">bugtracker</a> immediately, providing the template that causes this exception.</p>\r\n";
		} // end _printBugtrackerInfo();
	} // end Opt_ErrorHandler;
