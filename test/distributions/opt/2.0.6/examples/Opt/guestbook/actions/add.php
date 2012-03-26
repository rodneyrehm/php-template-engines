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
 * The action that displays the entry insert form .
 *
 * @param PDO $pdo The PDO object.
 * @return Opt_View
 */
function action($pdo, $config)
{
	$view = new Opt_View('add.tpl');
	$view->title = 'Add new entry';

	$form = new Form($view);
	$form->setAction('index.php?action=add');
	$form->addField('author', 'required,min_len=3,max_len=30', 'The length must be between 3 and 30 characters.');
	$form->addField('email', 'required,email,min_len=3,max_len=100', 'The value must be a valid mail with maximum 100 characters long.');
	$form->addField('website', 'url,min_len=3,max_len=100', 'The value must be a valid URL with maximum 100 characters long.');
	$form->addField('body', 'required,min_len=3', 'The body must be at least 3 characters long.');

	if($form->validate())
	{
		$values = $form->getValues();
		$stmt = $pdo->prepare('INSERT INTO `entries` (`author`, `email`, `date`, `website`, `body`)
			VALUES(:author, :email, :date, :website, :body)');
		$stmt->bindValue(':author', $values['author'], PDO::PARAM_STR);
		$stmt->bindValue(':email', $values['email'], PDO::PARAM_STR);
		$stmt->bindValue(':date', time(), PDO::PARAM_INT);
		$stmt->bindValue(':website', $values['website'], PDO::PARAM_STR);
		$stmt->bindValue(':body', $values['body'], PDO::PARAM_STR);
		$stmt->execute();

		$view->setTemplate('message.tpl');
		$view->message = 'The entry has been successfully added!';
		$view->redirect = 'index.php?action=list';
	}
	else
	{
		// The form is an object, so we need to inform OPT about it.
		$view->form = $form;
		$view->setFormat('form', 'Objective');
	}

	return $view;
} // end action();