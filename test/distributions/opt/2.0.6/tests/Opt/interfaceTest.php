<?php
/*
 * INTERFACE TEST
 * ------------------------------------
 * This unit test suite checks the features provided by the API that cannot
 * be checked in the other way or do not belong to any other category.
 */
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		define('INS_DIR', './instruction/');
		define('CPL_DIR', './templates_c/');
		define('RES_DIR', './results/');
		define('DAT_DIR', './data/');
		define('DOC_DIR', '../docs/');
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}

	class inactiveCache implements Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view)
		{
			echo 'CACHE-CHECK-START ';
			return false;
		} // end templateCacheStart();

		public function templateCacheStop(Opt_View $view)
		{
			echo ' CACHE-CHECK-STOP';
		} // end templateCacheStop();
	} // end inactiveCache;

	class activeCache implements Opt_Caching_Interface
	{
		public function templateCacheStart(Opt_View $view)
		{
			echo 'CACHE-CHECK-START ';
			return true;
		} // end templateCacheStart();

		public function templateCacheStop(Opt_View $view)
		{
			echo ' CACHE-CHECK-STOP';
		} // end templateCacheStop();
	} // end activeCache;

	class interfaceTest extends PHPUnit_Framework_TestCase
	{
		private function stripWs($text)
		{
			return trim(str_replace(array("\r", "\n"),array('', ''), $text));
		} // end stripws();

		protected function setUp()
		{
			$tpl = new Opt_Class;
			$tpl->sourceDir = './interface/';
			$tpl->compileDir = CPL_DIR;
			$tpl->compileMode = Opt_Class::CM_REBUILD;
			$tpl->stripWhitespaces = false;
			$tpl->prologRequired = true;
			$tpl->setup();
			$this->tpl = $tpl;
		} // end setUp();

		protected function tearDown()
		{
			Opl_Registry::register('opt', null);
			unset($this->tpl);
		} // end tearDown();

		/**
		 * Does the engine add the slashes to the paths?
		 */
		public function testSlashing()
		{
			$foo = new Opt_Class;
			$foo->sourceDir = './templates';
			$foo->compileDir = './templates_c';
			$foo->setup();

			if($foo->sourceDir['file'] != './templates/' || $foo->compileDir != './templates_c/')
			{
				$this->fail('No ending slash in the paths. SourceDir: '.$foo->sourceDir.'; CompileDir: '.$foo->compileDir);
			}

			unset($foo);
		} // end testSlashing();

		/**
		 * Test if the exception is returned, if the template does not exist.
		 */
		public function testInvalidTemplate()
		{
			try
			{
				$view = new Opt_View('template_that_doesnt_exist.tpl');
				$out = new Opt_Output_Return;
				$out->render($view);
			}
			catch(Opt_TemplateNotFound_Exception $exception)
			{
				return true;
			}
			$this->fail('Opt_TemplateNotFound_Exception not returned.');
		} // end testInvalidTemplate();

		/**
		 * Check if the Return output actually returns the output.
		 */
		public function testReturnOutput()
		{
			$view = new Opt_View('sample.tpl');

			$output = new Opt_Output_Return;
			$this->assertEquals('ORIGINAL', $this->stripWs($output->render($view)));
		} // end testReturnOutput();

		/**
		 * Check if the Return output requests to throw the exceptions.
		 */
		public function testReturnOutputException()
		{
			$view = new Opt_View('fake.tpl');

			try
			{
				$output = new Opt_Output_Return;
				$output->render($view);
			}
			catch(Opt_TemplateNotFound_Exception $exception)
			{
				return true;
			}
			$this->fail('Opt_TemplateNotFound_Exception not returned');
		} // end testReturnOutputException();

		/**
		 * Check if the Http output actually sends the output to the browser
		 * and if the output is buffered.
		 */
		public function testHttpOutput()
		{
			$view = new Opt_View('sample.tpl');

			ob_start();

			$output = new Opt_Output_Http;
			$output->render($view);
			$this->assertEquals('ORIGINAL', $this->stripWs(ob_get_clean()));
		} // end testHttpOutput();

		/**
		 * Test the lock of the HTTP output.
		 */
		public function testHttpOutputLock()
		{
			$view = new Opt_View('sample.tpl');

			try
			{
				$output = new Opt_Output_Http;
				ob_start();
				$output->render($view);
				$output->render($view);
				ob_end_clean();
				ob_end_clean();
			}
			catch(Opt_OutputOverloaded_Exception $exception)
			{
				ob_end_clean();
				ob_end_clean();
				return true;
			}
			@ob_end_clean();
			@ob_end_clean();
			$this->fail('Opt_OutputOverloaded_Exception not returned');
		} // end testHttpOutput();

		/**
		 * Check if the Http output requests to throw the exceptions.
		 */
		public function testHttpOutputException()
		{
			$view = new Opt_View('fake.tpl');

			try
			{
				$output = new Opt_Output_Http;
				$output->render($view);
			}
			catch(Opt_TemplateNotFound_Exception $exception)
			{
				return true;
			}
			$this->fail('Exception not returned for the template that does not exist.');
		} // end testReturnOutputException();

		/**
		 * Check if the template execution works, if there is
		 * no cache in the view.
		 */
		public function testCacheInactive()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache();

			$output = new Opt_Output_Return;
			$this->assertEquals('ORIGINAL', trim($output->render($view)));
		} // end testCacheInactive();

		/**
		 * The original template must also be executed, if there is
		 * a caching subsystem, but it decides that the template must be recompiled.
		 */
		public function testCacheProvidedButNotUsed()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache(new inactiveCache);

			$output = new Opt_Output_Return;
			$this->assertEquals('CACHE-CHECK-START ORIGINAL CACHE-CHECK-STOP', $this->stripWs($output->render($view)));
		} // end testCacheProvidedButNotUsed();

		/**
		 * In this case, the result is generated directly by the caching engine, so
		 * the original template should not be displayed.
		 */
		public function testCacheProvidedAndUsed()
		{
			$view = new Opt_View('sample.tpl');
			$view->setCache(new activeCache);

			$output = new Opt_Output_Return;
			$this->assertEquals('CACHE-CHECK-START', $this->stripWs($output->render($view)));
		} // end testCacheProvidedAndUsed();

		/**
		 * Testing the various compilation modes: CM_DEFAULT
		 */
		public function testCompileModeDefault()
		{
			$this->tpl->compileMode = Opt_Class::CM_DEFAULT;
			file_put_contents('./interface/mod_template.tpl', 'TEST 1');

			$view = new Opt_View('mod_template.tpl');

			$output = new Opt_Output_Return;
			$this->assertEquals('TEST 1', $this->stripWs($output->render($view)));

			$this->assertEquals('TEST 1', $this->stripWs($output->render($view)));

			sleep(2);

			file_put_contents('./interface/mod_template.tpl', 'TEST 2');

			$this->assertEquals('TEST 2', $this->stripWs($output->render($view)));
		} // end testCompileModeDefault();

		/**
		 * Testing the various compilation modes: CM_PERFORMANCE
		 */
		public function testCompileModePerformance()
		{
			sleep(2);
			$this->tpl->compileMode = Opt_Class::CM_DEFAULT;
			file_put_contents('./interface/mod_template.tpl', 'TEST 1');

			$view = new Opt_View('mod_template.tpl');

			$output = new Opt_Output_Return;
			$this->assertEquals('TEST 1', $this->stripWs($output->render($view)));

			$this->tpl->compileMode = Opt_Class::CM_PERFORMANCE;

			$this->assertEquals('TEST 1', $this->stripWs($output->render($view)));
			sleep(2);

			file_put_contents('./interface/mod_template.tpl', 'TEST 2');

			$this->assertEquals('TEST 1', $this->stripWs($output->render($view)));
		} // end testCompileModePerformance();

		/**
		 * Testing the various compilation modes: CM_DEFAULT
		 */
		public function outputBufferingTest()
		{
			ob_start();
			echo 'Foo';
			ob_start();
			echo 'Bar';

			$view = new Opt_View('sample.tpl');

			$output = new Opt_Output_Http;
			$output->render($view);

			Opl_Registry::register('opt', null);
			unset($this->tpl);
			
			$this->assertEquals(2, ob_get_level());

			ob_end_clean();
			ob_end_clean();
		} // end outputBufferingTest();

		/**
		 * Tests if the relative path switch works.
		 * @expectedException Opt_NotSupported_Exception
		 */
		public function testRelativePathsDisabled()
		{
			$this->tpl->allowRelativePaths = false;

			$view = new Opt_View('../template.tpl');
			$output = new Opt_Output_Return;
			$output->render($view);
		} // end testRelativePathsDisabled();

		/**
		 * Tests if the relative path switch works.
		 * @expectedException Opt_TemplateNotFound_Exception
		 */
		public function testRelativePathsEnabled()
		{
			$this->tpl->allowRelativePaths = true;

			$view = new Opt_View('../template.tpl');
			$output = new Opt_Output_Return;
			$output->render($view);
		} // end testRelativePathsEnabled();

		/**
		 * Testing the various compilation modes: CM_DEFAULT
		 */
		public function testTemplateInheritanceRecompileWithDataFormat()
		{
			$this->tpl->compileMode = Opt_Class::CM_DEFAULT;
			file_put_contents('./interface/base.tpl', "<opt:root>\n<opt:insert snippet=\"foo\" />\n</opt:root>");

			$view = new Opt_View('inherits.tpl');
			$item = new stdClass;
			$item->bar = 'bar';
			$view->item = $item;
			$view->setFormat('item', 'Objective');

			$output = new Opt_Output_Return;
			$this->assertEquals('bar', $this->stripWs($output->render($view)));
		} // end testTemplateInheritanceRecompileWithDataFormat();

		/**
		 * @covers Opt_Class::register
		 */
		public function testRegisterFromArray()
		{
			$tpl = new Opt_Class;
			$tpl->register(Opt_Class::PHP_CLASS, array(
				'foo' => 'bar',
				'joe' => 'goo'
			));

			$out = $tpl->_getList('_classes');
			if(!isset($out['foo']) || $out['foo'] != 'bar')
			{
				$this->fail('foo key not set');
			}
			if(!isset($out['joe']) || $out['joe'] != 'goo')
			{
				$this->fail('joe key not set');
			}
			return true;
		} // end testRegisterFromArray();

		/**
		 * @covers Opt_Class::register
		 */
		public function testRegisterShortForm()
		{
			$tpl = new Opt_Class;
			$tpl->register(Opt_Class::OPT_NAMESPACE, 'Foo');

			$out = $tpl->_getList('_namespaces');
			$this->assertContains('Foo', $out);
			return true;
		} // end testRegisterShortForm();

		/**
		 * @covers Opt_Class::register
		 */
		public function testRegisterLongForm()
		{
			$tpl = new Opt_Class;
			$tpl->register(Opt_Class::OPT_COMPONENT, 'Foo', 'Bar');

			$out = $tpl->_getList('_components');
			$this->assertArrayHasKey('Foo', $out);
			$this->assertContains('Bar', $out);
			return true;
		} // end testRegisterLongForm();

		/**
		 * @covers Opt_Class::register
		 */
		public function testRegisterFormat()
		{
			$tpl = new Opt_Class;
			$tpl->register(Opt_Class::OPT_FORMAT, 'Foo');
			$tpl->register(Opt_Class::OPT_FORMAT, 'Bar', 'Bar_Joe');

			$out = $tpl->_getList('_formats');
			$this->assertArrayHasKey('Foo', $out);
			$this->assertContains('Opt_Format_Foo', $out);

			$this->assertArrayHasKey('Bar', $out);
			$this->assertContains('Bar_Joe', $out);
			return true;
		} // end testRegisterFormat();

		/**
		 * @covers Opt_Class::register
		 */
		public function testRegisterInstruction()
		{
			$tpl = new Opt_Class;
			$tpl->register(Opt_Class::OPT_INSTRUCTION, 'Foo');
			$tpl->register(Opt_Class::OPT_INSTRUCTION, 'Bar', 'Bar_Joe');

			$out = $tpl->_getList('_instructions');
			$this->assertArrayHasKey('Foo', $out);
			$this->assertContains('Opt_Instruction_Foo', $out);

			$this->assertArrayHasKey('Bar', $out);
			$this->assertContains('Bar_Joe', $out);
			return true;
		} // end testRegisterInstruction();

		/**
		 * @covers Opt_Class::register
		 * @expectedException Opt_Initialization_Exception
		 */
		public function testRegisterLockedAfterSetup()
		{
			$tpl = new Opt_Class;
			$tpl->sourceDir = './foo/';
			$tpl->compileDir = './foo/';
			$tpl->setup();
			$tpl->register(Opt_Class::OPT_FORMAT, 'Foo');
			return true;
		} // end testRegisterLockedAfterSetup();

		/**
		 * @covers Opt_Class::setBufferState
		 * @covers Opt_Class::getBufferState
		 */
		public function testBufferCounter1()
		{
			$this->assertFalse($this->tpl->getBufferState('test1'));
			$this->tpl->setBufferState('test1', false);
			$this->assertFalse($this->tpl->getBufferState('test1'));
			$this->tpl->setBufferState('test1', true);
			$this->assertTrue($this->tpl->getBufferState('test1'));
			return true;
		} // end testBufferCounter1();

		/**
		 * @covers Opt_Class::setBufferState
		 * @covers Opt_Class::getBufferState
		 */
		public function testBufferCounter2()
		{
			$this->assertFalse($this->tpl->getBufferState('test2'));
			$this->tpl->setBufferState('test2', true);
			$this->assertTrue($this->tpl->getBufferState('test2'));
			$this->tpl->setBufferState('test2', true);
			$this->assertTrue($this->tpl->getBufferState('test2'));
			return true;
		} // end testBufferCounter2();

		/**
		 * @covers Opt_Class::setBufferState
		 * @covers Opt_Class::getBufferState
		 */
		public function testBufferCounter3()
		{
			$this->assertFalse($this->tpl->getBufferState('test3'));
			$this->tpl->setBufferState('test3', true);
			$this->assertTrue($this->tpl->getBufferState('test3'));
			$this->tpl->setBufferState('test3', true);
			$this->assertTrue($this->tpl->getBufferState('test3'));
			$this->tpl->setBufferState('test3', false);
			$this->assertTrue($this->tpl->getBufferState('test3'));
			$this->tpl->setBufferState('test3', false);
			$this->assertFalse($this->tpl->getBufferState('test3'));
			return true;
		} // end testBufferCounter3();

		/**
		 * @covers Opt_Output_Http
		 */
		public function testContentNegotiation1()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html; q=0.9, application/xhtml+xml; q=0.5';
			$visit = Opc_Visit::getInstance();
			$visit->mimeTypes = null;


			$tpl = new Opt_Class;
			$tpl->headerBuffering = true;
			$tpl->contentNegotiation = true;

			$output = new Opt_Output_Http;
			$output->setContentType(Opt_Output_Http::XHTML);
			$headers = $output->getHeaders();
			$this->assertEquals('text/html;charset=utf-8', $headers['Content-type']);
		} // end testContentNegotiation1();

		/**
		 * @covers Opt_Output_Http
		 */
		public function testContentNegotiation2()
		{
			$_SERVER['HTTP_ACCEPT'] = 'text/html; q=0.4, application/xhtml+xml; q=0.8';
			$visit = Opc_Visit::getInstance();
			$visit->mimeTypes = null;

			$tpl = new Opt_Class;
			$tpl->headerBuffering = true;
			$tpl->contentNegotiation = true;

			$output = new Opt_Output_Http;
			$output->setContentType(Opt_Output_Http::XHTML);
			$headers = $output->getHeaders();
			$this->assertEquals('application/xhtml+xml;charset=utf-8', $headers['Content-type']);
		} // end testContentNegotiation2();

		/**
		 * @covers Opt_Output_Http
		 */
		public function testMalformedHeaders()
		{
			unset($_SERVER['HTTP_ACCEPT']);
			$visit = Opc_Visit::getInstance();
			$visit->mimeTypes = null;

			$tpl = new Opt_Class;
			$tpl->headerBuffering = true;
			$tpl->contentNegotiation = false;

			$output = new Opt_Output_Http;
			$output->setContentType(Opt_Output_Http::XHTML);
			$headers = $output->getHeaders();
			$this->assertEquals('text/html;charset=utf-8', $headers['Content-type']);
		} // end testMalformedHeaders();

		/**
		 * @covers Opt_View
		 */
		public function testViewGettersSetters()
		{
			$view = new Opt_View('foo.tpl');

			$view->foo = 5;
			$this->assertEquals(5, $view->foo);

			$view->assign('bar', 10);
			$this->assertEquals(10, $view->get('bar'));

			$view->assignGroup(array(
				'joe' => 15,
				'goo' => 20
			));
			$this->assertEquals(15, $view->joe);
			$this->assertEquals(20, $view->get('goo'));

			$view->compound = array('group' => 10);
			$this->assertEquals(10, $view->compound['group']);
			$view->compound['group'] = 15;
			$this->assertEquals(15, $view->compound['group']);
		} // end testViewGettersSetters();

		/**
		 * @covers Opt_View
		 */
		public function testViewGetterAccessesUnexistingVariable()
		{
			$view = new Opt_View('foo.tpl');
			$this->assertSame(null, $view->unexisting);
			$this->assertSame(null, $view->get('unexisting'));
		} // end testViewGetterAccessesUnexistingVariable();
	} // end interfaceTest;
