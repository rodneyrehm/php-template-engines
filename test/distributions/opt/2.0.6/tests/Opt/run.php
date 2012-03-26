#!/usr/bin/php
<?php
/**
 * Because PHPUnit cannot run a particular case from the data provider,
 * we provide our own utility that allows to run and study a single test
 * case that uses the filesystem wrapper.
 * 
 * Usage: run.php /directory/something.txt
 */

if($argc != 2 && $argc != 3)
{
	die("Invalid call!\n");
}

define('INS_DIR', './instruction/');
define('CPL_DIR', './templates_c/');
define('RES_DIR', './results/');
define('DAT_DIR', './data/');
$config = parse_ini_file('../paths.ini', true);
require($config['libraries']['Opl'].'Base.php');
Opl_Loader::loadPaths($config);
Opl_Loader::register();
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

require('./includes/filesystemWrapper.php');

if($argc == 3)
{
	require('./includes/'.$argv[2]);
}

class test
{
	protected $tpl;

	public function init()
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = 'test://templates/';
		$tpl->compileDir = CPL_DIR;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->stripWhitespaces = false;
		$tpl->prologRequired = true;
		$tpl->setup();
		$this->tpl = $tpl;
	} // end init();

	public function run($test)
	{
		testFSWrapper::loadFilesystem($test);
	   	$view = new Opt_View('test.tpl');
		if(file_exists('test://data.php'))
		{
			eval(file_get_contents('test://data.php'));
		}

		$out = new Opt_Output_Return;
		$expected = file_get_contents('test://expected.txt');

		if(strpos($expected, 'OUTPUT') === 0)
		{
			// This test shoud give correct results
	   		try
	   		{
				$result = $out->render($view);
	   			if($this->stripWs(trim(file_get_contents('test://result.txt'))) === ($o = $this->stripWs(trim($result))))
				{
					return true;
				}
				die('Invalid output: '.$o."\n");
	   		}
	   		catch(Opt_Exception $e)
	   		{
	   			die('Exception returned: #'.get_class($e).': '.$e->getMessage()."\n");
	   		}
		}
		else
		{
			// This test should generate an exception
			$expected = trim($expected);
			try
			{
				$out->render($view);
			}
			catch(Opt_Exception $e)
			{
	   			if($expected != get_class($e))
	   			{
	   				die('Invalid exception returned: #'.get_class($e).', '.$expected." expected.\n");
	   			}
	   			return true;
			}
			die("Exception NOT returned, but should be: ".$expected."\n");
		}
	} // end run();

	private function stripWs($text)
	{
		return str_replace(array("\r", "\n"),array('', ''), $text);
	} // end stripws();
} // end test;

if(!file_exists($argv[1]))
{
	die("The specified test does not exist!\n");
}

$test = new test;
$test->init();
if($test->run($argv[1]))
{
	die("OK\n");
}
