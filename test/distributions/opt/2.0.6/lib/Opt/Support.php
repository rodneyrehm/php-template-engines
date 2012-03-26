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
	 * A support class that currently manages the debug console.
	 */
	class Opt_Support
	{
		const CACHED_TPL = 1;
		const STANDARD_TPL = 2;
		
		static private $_viewCount = 1;
		static private $_cplCount = 1;
		static private $_sectCount = 1;
		
		static private $_totalTime = 0;
		static private $_xmlMemory = 0;
		static private $_optWarnings = false;

		/**
		 * Initializes the debug console for Open Power Template by adding its
		 * own information frames and tables.
		 *
		 * @internal
		 * @static
		 * @param Opt_Class $tpl The main class
		 */
		static public function initDebugConsole($tpl)
		{
			Opl_Debug_Console::addList('opt_options', 'OPT Options and settings');
			Opl_Debug_Console::addList('opt_stats', 'OPT Stats');
			Opl_Debug_Console::addTable('opt_views', 'OPT: Executed views', array('#', 'View', 'Output', 'Time', 'Cached'));
			Opl_Debug_Console::addTable('opt_compiled', 'OPT: Compiled templates', array('#', 'Template', 'Estimated XML tree memory'));
			Opl_Debug_Console::addTable('opt_sections', 'OPT: Sections (Template compilation)', array('#', 'Name', 'Parent', 'Data format', 'Type'));
			Opl_Debug_Console::addListOptions('opt_options', $tpl->getConfig());
		} // end initDebugConsole();

		/**
		 * Adds the information about the view to the debug console.
		 *
		 * @internal
		 * @static
		 * @param String $view The view template
		 * @param String $outputName The name of the output system
		 * @param Float $time Template execution time.
		 * @param Boolean $cached Is the template cached?
		 */
		static public function addView($view, $outputName, $time, $cached)
		{
			self::$_totalTime += $time;
			Opl_Debug_Console::addTableItem('opt_views', array(self::$_viewCount++, $view, $outputName,  number_format($time, 5).' s', ($cached) ? 'Yes' : 'No'));
		} // end addDebugTemplate();

		/**
		 * Adds the information about template compilation.
		 *
		 * @static
		 * @internal
		 * @param String $template The template filename.
		 * @param Int $memory Estimated XML tree memory usage.
		 */
		static public function addCompiledTemplate($template, $memory)
		{
			self::$_xmlMemory += $memory;
			Opl_Debug_Console::addTableItem('opt_compiled', array(self::$_cplCount++, $template, number_format($memory).' b'));
		} // end addCompiledTemplate();

		/**
		 * Adds the information about a section to the debug console.
		 *
		 * @static
		 * @internal
		 * @param String $name Section name.
		 * @param String $parent Parent section name or the "Datasource" word.
		 * @param String $format The name of the data format used to populate the section.
		 * @param String $type The name of the section XML tag.
		 */
		static public function addSection($name, $parent, $format, $type)
		{
			Opl_Debug_Console::addTableItem('opt_sections', array(self::$_sectCount++, $name, $parent, $format, $type));
		} // end addSection();

		/**
		 * Updates the information about timers in the debug console.
		 *
		 * @static
		 * @internal
		 */
		static public function updateTimers()
		{
			Opl_Debug_Console::addListOption('opt_stats', 'Executed views: ', self::$_viewCount-1);
			Opl_Debug_Console::addListOption('opt_stats', 'Compiled templates: ', self::$_cplCount-1);
			Opl_Debug_Console::addListOption('opt_stats', 'Total template time: ', number_format(self::$_totalTime, 5).' s');
			if(self::$_cplCount-1 > 0)
			{
				Opl_Debug_Console::addListOption('opt_stats', 'Average XML memory per template: ', number_format(self::$_xmlMemory / (self::$_cplCount-1)).' b');
			}
		} // end updateTimers();

		/**
		 * Prints a warning to the debug console.
		 *
		 * @static
		 * @internal
		 * @param String $text The warning message.
		 */
		static public function warning($text)
		{
			if(!self::$_optWarnings)
			{
				Opl_Debug_Console::addTable('opt_warnings', 'OPT: Warnings', array('Message'));
				self::$_optWarnings = true;
			}
			Opl_Debug_Console::addTableItem('opt_warnings', array($text));
		} // end warning();
	} // end Opt_Support;
