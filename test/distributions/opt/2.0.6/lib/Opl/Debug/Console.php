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
 * $Id$
 */

	/**
	 * A class for managing the debug console.
	 */
	class Opl_Debug_Console
	{
		/**
		 * The list of lists displayed in the debug console.
		 *
		 * @static
		 * @var array
		 */
		static private $_lists = array();

		/**
		 * The list of multicolumn tables displayed in the debug console
		 *
		 * @static
		 * @var array
		 */
		static private $_tables = array();

		/**
		 * Creates a new list in the debug console, entitled with $title.
		 *
		 * @static
		 * @param string $id The unique list ID for adding the data
		 * @param string $title The title displayed in the debug console
		 */
		static public function addList($id, $title)
		{
			if(isset(self::$_lists[$id]))
			{
				throw new Opl_Debug_ItemExists_Exception('list', $id);
			}
			
			self::$_lists[$id] = array(
				'title' => $title,
				'values' => array()
			);
		} // end addList();

		/**
		 * Adds a new option to the specified list.
		 *
		 * @static
		 * @param string $id The list ID
		 * @param string $title The option title
		 * @param string $value The option value
		 */
		static public function addListOption($id, $title, $value)
		{
			if(!isset(self::$_lists[$id]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('list', $id);
			}

			self::$_lists[$id]['values'][$title] = $value;
		} // end addListOption();

		/**
		 * Adds new options to the list from an array.
		 *
		 * @static
		 * @param string $id The list ID
		 * @param array $options The assotiative array of pairs title => value
		 */
		static public function addListOptions($id, $options)
		{
			if(!isset(self::$_lists[$id]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('list', $id);
			}
			foreach($options as $title => $value)
			{
				self::$_lists[$id]['values'][$title] = $value;
			}			
		} // end addListOptions();

		/**
		 * Adds a new multicolumn table to the debug console. The columns
		 * are described by their titles and optionally their size encoded
		 * as "size:title".
		 *
		 * @static
		 * @param string $id The unique table ID used for adding new data.
		 * @param string $title The table title.
		 * @param array $columns The list of available columns.
		 */
		static public function addTable($id, $title, Array $columns)
		{
			if(isset(self::$_tables[$id]))
			{
				throw new Opl_Debug_ItemExists_Exception('table', $id);
			}
			
			self::$_tables[$id] = array(
				'title' => $title,
				'columns' => $columns,
				'information' => null,
				'values' => array()
			);
		} // end addTable();

		/**
		 * Adds a new item to the table. The column values are
		 * specified in the order the columns were defined with.
		 *
		 * @static
		 * @param string $id The table ID
		 * @param array $colValues The list of column values
		 */
		static public function addTableItem($id, Array $colValues)
		{
			if(!isset(self::$_tables[$id]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('table', $id);
			}
			self::$_tables[$id]['values'][] = $colValues;
		} // end addTableItem();

		/**
		 * Adds a table information displayed under the list values.
		 *
		 * @static
		 * @param string $id The table ID
		 * @param string $information The information to display
		 */
		static public function addTableInformation($id, $information)
		{
			if(!isset(self::$_tables[$id]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('table', $id);
			}
			self::$_tables[$id]['information'] = $information;
		} // end addTableInformation();

		/**
		 * Returns the registered tables and their data back to the
		 * script.
		 *
		 * @static
		 * @return array
		 */
		static public function getTables()
		{
			return self::$_tables;
		} // end getTables();

		/**
		 * Returns the registered lists and their data back to the
		 * script.
		 *
		 * @static
		 * @return array
		 */
		static public function getLists()
		{
			return self::$_lists;
		} // end getLists();

		/**
		 * Generates a HTML output for the debug console and flushes
		 * it to the script output. The console is NOT displayed, if
		 * the user registered a global state 'opl_custom_console'
		 * in Opl_Registry.
		 *
		 * @static
		 */
		static public function display()
		{
			// Skip, if the user has a custom console
			if(Opl_Registry::getState('opl_custom_console'))
			{
				return;
			}

			$code = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>OPL Debug Console</title>
<style type="text/css"><!--
body{margin: 0; padding: 0; background: #ffffff; font-family: Arial, Tahoma, Verdana, Helvetica; font-size: 12px; }
div#all{ margin: 0; padding: 0 0; }
table.lay {border: none; border-spacing: 0; width: 100%; border-collapse: collapse;}
table.lay td.lay {width: 50%; vertical-align: top;}
table.info, table.list { width: 100%; padding: 0; margin: 0 0 10px 0; border-spacing: 0;  border: 1px #333333 solid; }
table caption { width: 100%; margin: 0; padding: 5px 0; font-weight: bold; font-size: 13px; background: #444444; color: #ffffff; }
table.info {}
table.info tbody td { border-bottom: 1px solid #ddd; padding: 3px 5px; font-size: 12px; margin: 0}
table.info tbody td.field{  width: 40%; color: #666666; background-color: #eeeeee;}
table.info tbody td.value{ width: 60%;   background-color: #ffffff;  }
table.list {}
table.list thead td{ text-align: left;padding: 3px 5px; font-size: 12px; color: #474747; border-bottom: 1px solid #b2b2b2; background-color: #dadada; font-weight: bold; }
table.list tbody td{ font-size: 12px; padding: 3px 5px; background-color: #ffffff; border-bottom: 1px solid #ddd;}
span.good{ color: #009900; }
span.maybe{ color: #777700; }
span.bad{ color: #770000; }
h1 {font-size: 22px; margin 0 0 10px 0; padding: 6px 15px; background:#FFFFCC; border-bottom: 2px solid #FFCC33}
--></style>
</head>
<body>
<h1>OPL Debug Console</h1>
<table class="lay"><tr><td class="lay">';
			foreach(self::$_lists as &$list)
			{
				$code .= '   <table class="info" cellspacing="0">
<caption>'.$list['title'].'</caption>
<tbody>
';
				foreach($list['values'] as $title => &$value)
				{
					if(is_bool($value))
					{
						$value = $value ? 'Yes' : 'No';
					}
					if(is_null($value))
					{
						$value = '<em>NULL</em>';
					}
	
					$code .= '    <tr>
     <td class="field">'.$title.'</td>
     <td class="value">'.$value.'</td>
    </tr>
';
				}
				$code .= '    </tbody>
   </table>
';
			}
			$code .= '</td><td class="lay">';

			foreach(self::$_tables as &$table)
			{
				$code .= '  <table class="list" cellspacing="0">
    <caption>'.$table['title'].'</caption>
    <thead>
     <tr>
';
				foreach($table['columns'] as $column)
				{
					if(($split = strpos($column, ':'))!== false)
					{
						$code .= '      <td width="'.substr($column, 0, $split).'">'.substr($column, $split).'</td>';
					}
					else
					{
						$code .= '      <td>'.$column.'</td>';
					}
				}
				$code .= '     </tr>
    </thead>
    <tbody>';
				if(is_null($table['information']))
				{
					foreach($table['values'] as &$data)
					{
						$code .= '     <tr>';
						foreach($data as $item)
						{
								$code .= '      <td>'.$item.'</td>';
						}
						$code .= '     </tr>';
					}
				}
				else
				{
					$code .= '     <tr>
	      <td colspan="'.sizeof($table['columns']).'">'.$table['information'].'</td>
	     </tr>
	';
				}
				$code .= '    </tbody>
   </table>
';
			}
			$code .= ' </td></tr></table>
</body>
</html>
';
			echo '<script type="text/javascript">';
			echo 'opl_console = window.open("","OPL_debug_console","width=1000,height=500,resizable,scrollbars=yes,menubar=no,toolbar=no,location=no,status=no");';
			echo 'opl_console.document.close();';
			echo 'opl_console.document.open();';
			$exp = explode("\n", $code);
			foreach($exp as $line)
			{
				echo 'opl_console.document.write(\''.addcslashes(trim($line), "'\\")."');\r\n";
			}
			echo '</script>';
		} // end display();
	
	} // end Opl_Debug_Console;
