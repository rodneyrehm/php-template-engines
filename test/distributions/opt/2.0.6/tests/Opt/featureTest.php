<?php
/*
 * FEATURE TEST
 * ------------------------------------
 * This test checks other syntax features that are not necessarily instructions, but
 * may be related to them.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('FEAT_DIR', './feature/');
		define('CPL_DIR', './templates_c/');
		define('RES_DIR', './results/');
		define('DAT_DIR', './data/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		require('./includes/filesystemWrapper.php');
	}

	/**
	 * @backupStaticAttributes disabled
	 */
	class featureTest extends PHPUnit_Framework_TestCase
	{
		protected $tpl;
		protected $dataGenerators = array();

		protected function setUp()
		{
			$tpl = new Opt_Class;
			$tpl->sourceDir = 'test://templates/';
			$tpl->compileDir = CPL_DIR;
			$tpl->compileMode = Opt_Class::CM_REBUILD;
			$tpl->stripWhitespaces = false;
			$tpl->prologRequired = true;

			$tpl->register(Opt_Class::PHP_CLASS, 'foo', 'stdClass');

			$tpl->setup();
			$this->tpl = $tpl;
		} // end setUp();
		
		protected function tearDown()
		{
			unset($this->tpl);
		} // end tearDown();

		public static function correctProvider()
		{
			return array(0 =>
				array('backticks_1.txt'),
				array('backticks_2.txt'),
				array('escaping_1.txt'),
				array('escaping_2.txt'),
				array('escaping_3.txt'),
				array('escaping_4.txt'),
				array('escaping_5.txt'),
				array('no_oop_1.txt'),
				array('no_oop_2.txt'),
				array('no_oop_3.txt'),
				array('translation_1.txt'),
				array('translation_2.txt'),
				array('specialvar_1.txt'),
				array('stripws_1.txt'),
			);
		} // end correctProvider();
		
		private function stripWs($text)
		{
			return str_replace(array("\r", "\n"),array('', ''), $text);
		} // end stripws();

 		/**
 		 * @dataProvider correctProvider
 		 */
		public function testCorrect($test)
		{			
			testFSWrapper::loadFilesystem(FEAT_DIR.$test);
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
					$this->assertEquals($this->stripWs(trim(file_get_contents('test://result.txt'))), $this->stripWs(trim($result)));
				}
				catch(Opt_Exception $e)
				{
					$this->fail('Exception returned: #'.get_class($e).': '.$e->getMessage());
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
						$this->fail('Invalid exception returned: #'.get_class($e).', '.$expected.' expected.');
					}
					return true;
				}
				$this->fail('Exception NOT returned, but should be: '.$expected);
			}
		} // end testCorrect();
	}
?>
