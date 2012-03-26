<?php
/*
 *  OPEN POWER LIBS EXAMPLES <http://www.invenzzia.org>
 *  ===================================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

// Load the libraries core
$config = parse_ini_file('../../paths.ini', true);
require($config['libraries']['Opl'].'Base.php');
Opl_Loader::loadPaths($config);
Opl_Loader::setCheckFileExists(false);
Opl_Loader::register();

try
{
	// Configure the main class
	$tpl = new Opt_Class;

	// The location of the source templates
	$tpl->sourceDir = './templates/';

	// The location of the compiled templates
	$tpl->compileDir = './templates_c/';

	// Set up everything.
	$tpl->setup();

	/* to parse templates, we use views. Views are the templates with
	 * the associated data. Let's create one.
	 */
	$view = new Opt_View('example.tpl');

	// Add a template variable
	$view->hello = 'Hi universe!';

	/* To process a view, we need an output system. It decides, how to
	 * render and where to send the results. Opt_Output_Http sends
	 * the results to the browser.
	 */
	$output = new Opt_Output_Http;

	// Create the content-type header.
	$output->setContentType(Opt_Output_Http::XHTML, 'utf-8');

	// Render a view and send the results to the browser.
	$output->render($view);

}
catch(Opt_Exception $exception)
{
	// Use the standard error handler to display exceptions
	$handler = new Opt_ErrorHandler;
	$handler->display($exception);
}