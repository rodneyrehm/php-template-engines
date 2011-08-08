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
 * This is a sample configuration file. Rename it to config.php
 * and configure your connection settings.
 */

$config = array(
	'script' => array(
		'dateFormat' => 'F j, Y, g:i a',
		'baseHref' => '/'
	),
	'db' => array(
		// The DSN for PDO library.
		'dsn' => 'mysql:host=localhost;dbname=guestbook',
		'user' => 'root',
		'password' => 'root',
		// The database charset
		'charset' => 'utf8'
	),
	'opt' => array(
		// The source directory for the templates
		'sourceDir' => './templates/',
		// The compilation directory for the templates
		'compileDir' => './templates_c/',
		'stripWhitespaces' => false
	)
);
