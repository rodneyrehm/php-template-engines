<?php
/*
 * INSTRUCTION TEST
 * ------------------------------------
 * This test checks, whether the instructions work, as they should work, by checking the generated content.
 * The procedure does not contain template inheritance issues, because they have a separate procedure.
 *
 * Warning: in PHP 5.3 one of the tests (single_2.txt) fails, but of the PHPUnit fault. The external tester
 * shows that the file gives the correct output.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
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
		require('./includes/testComponent.php');
		require('./includes/testBlock.php');
	}

	/**
	 * @runTestsInSeparateProcesses
	 */
	class instructionTest extends PHPUnit_Framework_TestCase
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
			$tpl->register(Opt_Class::OPT_COMPONENT, 'opt:myComponent', 'myComponent');
			$tpl->register(Opt_Class::OPT_BLOCK, 'opt:myBlock', 'myBlock');
			$tpl->setup();
			$this->tpl = $tpl;

			Opt_View::clear();
		} // end setUp();
		
		protected function tearDown()
		{
			unset($this->tpl);
		} // end tearDown();

		public static function correctProvider()
		{
			return array(0 =>
				array('attribute_1.txt'),
				array('attribute_2.txt'),
				array('attribute_3.txt'),
				array('attribute_4.txt'),
				array('attribute_5.txt'),
				array('attribute_6.txt'),
				array('attribute_7.txt'),
				array('attribute_8.txt'),
				array('attribute_9.txt'),
				array('attribute_10.txt'),
				array('attribute_11.txt'),
				array('attribute_12.txt'),
				array('attribute_13.txt'),
				array('attribute_14.txt'),
				array('attribute_15.txt'),
				array('attribute_16.txt'),
				array('attribute_17.txt'),
				array('attributes_build_1.txt'),
				array('attributes_build_2.txt'),
				array('attributes_build_3.txt'),
				array('block_1.txt'),
				array('block_2.txt'),
				array('block_3.txt'),
				array('block_4.txt'),
				array('block_5.txt'),
				array('capture_1.txt'),
				array('capture_2.txt'),
				array('capture_3.txt'),
				array('capture_4.txt'),
				array('capture_5.txt'),
				array('component_1.txt'),
				array('component_2.txt'),
				array('component_3.txt'),
				array('component_4.txt'),
				array('component_5.txt'),
				array('component_6.txt'),
				array('component_7.txt'),
				array('component_8.txt'),
				array('component_9.txt'),
				array('component_10.txt'),
				array('component_11.txt'),
				array('component_12.txt'),
				array('component_13.txt'),
				array('content_1.txt'),
				array('content_2.txt'),
				array('dtd_1.txt'),
				array('dtd_2.txt'),
				array('dtd_3.txt'),
				array('dtd_4.txt'),
				array('dtd_5.txt'),
				array('dtd_6.txt'),
				array('dtd_7.txt'),
				array('dtd_8.txt'),
				array('dtd_9.txt'),
				array('extend_1.txt'),
				array('extend_2.txt'),
				array('extend_3.txt'),
				array('extend_4.txt'),
				array('extend_5.txt'),
				array('extend_6.txt'),
				array('extend_7.txt'),
				array('extend_8.txt'),
				array('extend_9.txt'),
				array('extend_10.txt'),
				array('extend_11.txt'),
				array('extend_12.txt'),
				array('extend_13.txt'),
				array('for_1.txt'),
				array('for_2.txt'),
				array('for_3.txt'),
				array('foreach_1.txt'),
				array('foreach_2.txt'),
				array('foreach_3.txt'),
				array('foreach_4.txt'),
				array('foreach_5.txt'),
				array('foreach_6.txt'),
				array('foreach_7.txt'),
				array('foreach_8.txt'),
				array('foreach_9.txt'),
				array('foreach_10.txt'),
				array('grid_1.txt'),
				array('grid_2.txt'),
				array('grid_3.txt'),
				array('grid_4.txt'),
				array('grid_5.txt'),
				array('grid_6.txt'),
				array('grid_7.txt'),
				array('grid_8.txt'),
				array('grid_9.txt'),
				array('if_1.txt'),
				array('if_2.txt'),
				array('if_3.txt'),
				array('if_4.txt'),
				array('if_5.txt'),
				array('if_6.txt'),
				array('if_7.txt'),
				array('if_8.txt'),
				array('if_9.txt'),
				array('if_10.txt'),
				array('include_1.txt'),
				array('include_2.txt'),
				array('include_3.txt'),
				array('include_4.txt'),
				array('include_5.txt'),
				array('include_6.txt'),
				array('include_7.txt'),
				array('include_8.txt'),
				array('include_9.txt'),
				array('insert_1.txt'),
				array('insert_2.txt'),
				array('insert_3.txt'),
				array('insert_4.txt'),
				array('insert_5.txt'),
				array('literal_1.txt'),
				array('literal_2.txt'),
				array('literal_3.txt'),
				array('literal_4.txt'),
				array('literal_5.txt'),
				array('literal_6.txt'),
				array('on_1.txt'),
				array('prolog_1.txt'),
				array('prolog_2.txt'),
				array('prolog_3.txt'),
				array('put_1.txt'),
				array('put_2.txt'),
				array('put_3.txt'),
				array('repeat_1.txt'),
				array('repeat_2.txt'),
				array('repeat_3.txt'),
				array('repeat_4.txt'),
				array('root_1.txt'),
				array('root_2.txt'),
				array('root_3.txt'),
				array('root_4.txt'),
				array('root_5.txt'),
				array('section_1.txt'),
				array('section_2.txt'),
				array('section_3.txt'),
				array('section_4.txt'),
				array('section_5.txt'),
				array('section_6.txt'),
				array('section_7.txt'),
				array('section_8.txt'),
				array('section_9.txt'),
				array('section_10.txt'),
				array('section_11.txt'),
				array('section_12.txt'),
				array('selector_1.txt'),
				array('selector_2.txt'),
				array('selector_3.txt'),
				array('selector_4.txt'),
				array('selector_5.txt'),
				array('selector_6.txt'),
				array('selector_7.txt'),
				array('selector_8.txt'),
				array('show_1.txt'),
				array('show_2.txt'),
				array('show_3.txt'),
				array('show_4.txt'),
				array('show_5.txt'),
				array('show_6.txt'),
				array('show_7.txt'),
				array('show_8.txt'),
				array('show_9.txt'),
				array('show_10.txt'),
				array('single_1.txt'),
				array('single_2.txt'),
				array('single_3.txt'),
				array('snippet_1.txt'),
				array('snippet_2.txt'),
				array('snippet_3.txt'),
				array('snippet_4.txt'),
				array('snippet_5.txt'),
				array('snippet_6.txt'),
				array('snippet_7.txt'),
				array('snippet_8.txt'),
				array('tag_1.txt'),
				array('tag_2.txt'),
				array('tag_3.txt'),
				array('tag_4.txt'),
				array('tag_5.txt'),
				array('tree_1.txt'),
				array('tree_2.txt'),
				array('tree_3.txt'),
				array('tree_4.txt'),
				array('tree_5.txt'),
				array('tree_6.txt'),
				array('tree_7.txt'),
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
			testFSWrapper::loadFilesystem(INS_DIR.$test);
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
