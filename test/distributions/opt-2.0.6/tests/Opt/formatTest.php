<?php
/*
 * FORMAT TEST
 * ------------------------------------
 * This test checks the correctness of the data formats implemented in OPT 2.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('FEAT_DIR', './format/');
		define('CPL_DIR', './templates_c/');
		define('RES_DIR', './results/');
		define('DAT_DIR', './data/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		require('./includes/filesystemWrapper.php');
		require('./includes/testFormat.php');
	}

	/**
	 * @backupStaticAttributes disabled
	 */
	class formatTest extends PHPUnit_Framework_TestCase
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
			$tpl->register(Opt_Class::OPT_FORMAT, 'Test');
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
				array('array_1.txt'),
				array('array_2.txt'),
				array('array_3.txt'),
				array('array_4.txt'),
				array('array_5.txt'),
				array('custom_assign_1.txt'),
				array('custom_assign_2.txt'),
				array('custom_assign_3.txt'),
				array('decoration_1.txt'),
				array('decoration_2.txt'),
				array('objective_1.txt'),
				array('objective_2.txt'),
				array('objective_3.txt'),
				array('objective_4.txt'),
				array('objective_5.txt'),
				array('objective_6.txt'),
				array('objective_7.txt'),
				array('objective_8.txt'),
				array('objective_9.txt'),
				array('objective_10.txt'),
				array('spldatastructure_1.txt'),
				array('spldatastructure_2.txt'),
				array('spldatastructure_3.txt'),
				array('spldatastructure_4.txt'),
				array('spldatastructure_5.txt'),
				array('spldatastructure_6.txt'),
				array('spldatastructure_7.txt'),
				array('spldatastructure_8.txt'),
				array('spldatastructure_9.txt'),
				array('spldatastructure_10.txt'),
				array('spldatastructure_11.txt'),
				array('runtimegen_1.txt'),
				array('singlearray_1.txt'),
				array('singlearray_2.txt'),
				array('singlearray_3.txt'),
				array('singlearray_4.txt'),
				array('staticgen_1.txt'),
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
