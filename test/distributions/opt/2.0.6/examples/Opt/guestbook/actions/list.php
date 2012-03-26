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

/**
 * The action that displays the list of the entries.
 * @param PDO $pdo The PDO object.
 * @return Opt_View
 */
function action($pdo, $config)
{
	$stmt = $pdo->query('SELECT `id`, `author`, `date`, `email`, `website`, `body` FROM `entries` ORDER BY `date` DESC');

	$results = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$row['date'] = date($config['script']['dateFormat'], $row['date']);
		$results[] = $row;
	}

	$view = new Opt_View('list.tpl');
	$view->title = 'Index';
	$view->entries = $results;

	return $view;
} // end action();