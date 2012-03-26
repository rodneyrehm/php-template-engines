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

	class Opl_ErrorHandler
	{
		protected $_library = 'Open Power Libs';
		protected $_context = array(
			'Opl_Debug_Exception' => array(
				'BasicConfiguration' => array(),
				'Backtrace' => array()
			),
			'__UNKNOWN__' => array(
				'BasicConfiguration' => array()
			),
		);

		/**
		 * Displays an exception information using the default OPL graphics
		 * style.
		 *
		 * @param Opl_Exception $exception The exception to be displayed.
		 */	
		public function display(Opl_Exception $exception)
		{
			if(ob_get_level() > 0)
			{
				ob_end_clean();
			}
echo <<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$this->_library} error</title>
<style type="text/css">
/* <![CDATA[ */
html, body{  margin: 0; padding: 0; font-size: 10pt; background: #ffffff;  }
div#oplErrorFrame { font-family: Arial, Verdana, Tahoma, Helvetica, sans-serif; color: #222222; width: 700px; margin-top: 100px; margin-left: auto; margin-right: auto; padding: 2px; }
div#oplErrorFrame h1{ font-size: 16pt; text-align: center; padding: 10px; margin: 2px 0; background: #ffffff; border-top: 4px solid #e60066; }
div#oplErrorFrame div.object{ border: 1px solid #ffdecc; margin: 2px 0;background: #ffeeee; padding: 0; }
div#oplErrorFrame div.object div{ /*border-left: 15px solid #e33a3a;*/ margin: 0; padding: 1px; }
div#oplErrorFrame p{padding: 5px; margin: 5px 0;}
div#oplErrorFrame p.message { font-size: 13pt; }
div#oplErrorFrame p.code{ font-weight: bold; }
div#oplErrorFrame p span{ margin-right: 6px; }
div#oplErrorFrame p.call{ border-top: 1px solid #e33a3a; margin: 5px; padding: 5px 0; }
div#oplErrorFrame p.call span{ float: none; margin-right: 0; font-family: 'Courier New', Courier, monospaced;  font-size: 12px; }
div#oplErrorFrame p.directive span{ font-weight: bold; }
div#oplErrorFrame p.directive span.good{ color: #009900; }
div#oplErrorFrame p.directive span.maybe{ color: #777700; }
div#oplErrorFrame p.directive span.bad{ color: #770000; }
div#oplErrorFrame p.important{ font-weight: bold; text-align: center; width:100%; }
div#oplErrorFrame p.warning span{	float: left; margin-right: 12px; font-weight: bold; }
div#oplErrorFrame a {font-weight: bold; color: #000000}
div#oplErrorFrame a:hover {}
div#oplErrorFrame ul {list-style: none; margin: 5px 15px; padding: 0}
div#oplErrorFrame ul li {margin: 0; padding: 0}
div#oplErrorFrame ul li p {padding:0;}

div#oplErrorFrame li { margin-top: 2px; margin-bottom: 2px; padding: 0; }
div#oplErrorFrame li.value { font-weight: bold; }
div#oplErrorFrame li span{  margin-right: 6px; }
div#oplErrorFrame li.value span.good{ color: #009900; }
div#oplErrorFrame li.value span.maybe{ color: #777700; }
div#oplErrorFrame li.value span.bad{ color: #770000; }

div#oplErrorFrame code{ font-family: 'Courier New', Courier, monospaced; background: #ffdddd;  }
/* ]]> */
</style>  
</head>
<body>

<div id="oplErrorFrame">
	<h1>{$this->_library} error</h1>
	<div class="object"><div>
 
EOF;
	echo '  			<p class="message">'.htmlspecialchars($exception->getMessage())."</p>\r\n";
	echo '  			<p class="code">'.get_class($exception)."</p>\r\n";
	if(Opl_Registry::getState('opl_extended_errors'))
	{
		
		echo '  			<p class="call"><span>'.$exception->getFile().'</span> [<span>'.$exception->getLine()."</span>]</p>\r\n";
	}
	else
	{
		echo "  			<p class=\"call\">Debug mode is disabled. No additional information provided.</p>\r\n";
	}
	echo "  		</div></div>\r\n";

	if(Opl_Registry::getState('opl_extended_errors'))
	{
		echo "			<div class=\"object\"><div>\r\n";
		$this->_resolveContextInfo($exception);
		echo "  		</div></div>\r\n";
	}
	echo <<<EOF
</div>
</body>
</html>
EOF;
		} // end display();
	
		protected function _resolveContextInfo($exception)
		{
			$use = get_class($exception);
			if(!isset($this->_context[$use]))
			{
				$use = '__UNKNOWN__';
			}      
			foreach($this->_context[$use] as $name => $config)
			{
				if(!method_exists($this, '_print'.$name))
				{
					$this->_printErrorInfo($exception, 'Error message filter "'.$name.'" not found.');
				}
				else
				{			
					call_user_func_array(array($this, '_print'.$name), array_merge(array(0 => $exception), $config));
				}
			}
		} // end _resolveContextInfo();	
		
		protected function _printErrorInfo($exception, $text)
		{
			echo '  			<p><strong>Exception information:</strong> '.$text."</p>\r\n";
		} // end _printErrorInfo();

		protected function _printStackInfo($exception, $title)
		{
			echo '		<p class="directive">'.$title.":</p>\r\n";
			$data = $exception->getData();
			$i = 1;
			while(sizeof($data) > 0)
			{
				$item = array_shift($data);
				if(sizeof($data) == 0)
				{
					echo "		<p class=\"directive\">".$i.". <span class=\"bad\">".$item."</span></p>\r\n";
				}
				else
				{
					echo "		<p class=\"directive\">".$i.". <span>".$item."</span></p>\r\n";
				}
				$i++;
			}
		} // end _printStackInfo();

		protected function _printBasicConfiguration($exception)
		{
			/* null */
		} // end _printBasicConfiguration();

		protected function _printBacktrace($exception)
		{
			echo "		<p class=\"directive\"><strong>Debug backtrace:</strong></p>\r\n";
			$data = array_reverse($exception->getTrace());
			$data[] = array(
				'function' => 'Opl_Debug_Exception',
				'file' => $exception->getFile(),
				'line' => $exception->getLine()
			);
			$size = sizeof($data);
			echo "		<ul>";
			while(sizeof($data) > 0)
			{
				$item = array_shift($data);

				$name = (isset($item['class']) ? $item['class'].'::' : '').$item['function'];

				if(sizeof($data) == 0)
				{
					echo "		<li><p class=\"directive\">".$size.". ".$name."() - <span class=\"bad\"><code>".basename($item['file']).'</code> ['.$item['line']."]</span></p></li>\r\n";
				}
				else
				{
					echo "		<li><p class=\"directive\">".$size.". ".$name."() - <span><code>".basename($item['file']).'</code> ['.$item['line']."]</span></p></li>\r\n";
				}
				$size--;
			}
			echo "		</ul>";
		} // end _printBacktrace();
	} // end Opl_ErrorHandler;
