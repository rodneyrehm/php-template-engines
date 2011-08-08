<?php
/*
 *
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

/**
 * This is a sample bootstrap file for the guestbook. Remember to
 * configure the paths.ini file properly before running it!
 */
date_default_timezone_set('Europe/Warsaw');

$config = parse_ini_file('../../paths.ini', true);
require($config['libraries']['Opl'].'Base.php');
Opl_Loader::loadPaths($config);
Opl_Loader::setCheckFileExists(false);
Opl_Loader::register();

Opl_Registry::setState('opl_extended_errors', true);

if(!file_exists('./config.php'))
{
	die('Please create the config.php file from config.sample.php!');
}
require('./config.php');
require('./includes/forms.php');
require('./components/base.php');
require('./components/input.php');
require('./components/textarea.php');

try
{
	$tpl = new Opt_Class;

	// Load the configuration
	$tpl->loadConfig($config['opt']);

	// Register the components
	$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:input', 'InputComponent');
	$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:textarea', 'TextareaComponent');

	$tpl->setup();
	
	Opt_View::assignGlobal('baseHref', $config['script']['baseHref']);

	// Connect to the database.
	$pdo = new PDO($config['db']['dsn'], $config['db']['user'], $config['db']['password']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->query('SET NAMES `'.$config['db']['charset'].'`');

	// Load and execute the action.
	$action = 'list';
	if(isset($_GET['action']) && ctype_alpha($_GET['action']))
	{
		$action = $_GET['action'];
	}
	require('./actions/'.$action.'.php');
	$view = action($pdo, $config);

	// Render the returned view to the browser.
	$output = new Opt_Output_Http;
	$output->setContentType(Opt_Output_Http::XHTML, 'utf-8');
	$output->render($view);

}
catch(PDOException $exception)
{
	die('PDO Exception occured: '.$exception->getMessage());
}
catch(Opt_Exception $exception)
{
	$handler = new Opt_ErrorHandler;
	$handler->display($exception);
}
catch(Opl_Exception $exception)
{
	$handler = new Opl_ErrorHandler;
	$handler->display($exception);
}
