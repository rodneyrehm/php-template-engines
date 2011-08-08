<?php
/*
 * FUNCTION TEST
 * ------------------------------------
 * This test checks the functions available with OPT.
 * 
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('FEAT_DIR', './function/');
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
	class functionTest extends PHPUnit_Framework_TestCase
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
				array('absolute_1.txt'),
				array('absolute_2.txt'),
				array('autolink_1.txt'),
				array('autolink_2.txt'),
				array('average_1.txt'),
				array('capitalize_1.txt'),
				array('capitalize_2.txt'),
				array('contains_1.txt'),
				array('contains_2.txt'),
				array('contains_3.txt'),
				array('contains_key_1.txt'),
				array('contains_key_2.txt'),
				array('contains_key_3.txt'),
				array('count_1.txt'),
				array('count_chars_1.txt'),
				array('count_substring_1.txt'),
				array('count_words_1.txt'),
				array('cycle_1.txt'),
				array('cycle_2.txt'),
				array('date_1.txt'),
				array('entity_1.txt'),
				array('entity_2.txt'),
				array('firstof_1.txt'),
				array('indent_1.txt'),
				array('isimage_1.txt'),
				array('isurl_1.txt'),
				array('lower_1.txt'),
				array('lower_2.txt'),
				array('money_1.txt'),
				array('money_2.txt'),
				array('nl2br_1.txt'),
				array('nl2br_2.txt'),
				array('number_1.txt'),
				array('number_2.txt'),
				array('number_3.txt'),
				array('pad_1.txt'),
				array('pad_2.txt'),
				array('pluralize_1.txt'),
				array('pluralize_2.txt'),
				array('position_1.txt'),
				array('range_1.txt'),
				array('regex_replace_1.txt'),
				array('replace_1.txt'),
				array('scalar_1.txt'),
				array('spacify_1.txt'),
				array('spacify_2.txt'),
				array('stddev_1.txt'),
				array('strip_1.txt'),
				array('strip_2.txt'),
				array('strip_tags_1.txt'),
				array('strip_tags_2.txt'),
				array('sum_1.txt'),
				array('truncate_1.txt'),
				array('upper_1.txt'),
				array('upper_2.txt'),
				array('wordwrap_1.txt'),
				array('wordwrap_2.txt'),
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
