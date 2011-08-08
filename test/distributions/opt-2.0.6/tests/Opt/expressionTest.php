<?php

	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}
	
	class tl implements Opl_Translation_Interface
	{
		public function _($group, $id){ }
		public function assign($group, $id){ }
	} // end tl;

	/**
	 * @backupStaticAttributes disabled
	 */
	class expressionTest extends PHPUnit_Framework_TestCase
	{
		protected $tpl;
		protected $cpl;

		protected function setUp()
		{
			$tpl = new Opt_Class;
			$tpl->sourceDir = '/';
			$tpl->sourceDir = './templates/';
			$tpl->compileDir = './templates_c/';
			$tpl->escape = false;

			$tpl->register(Opt_Class::PHP_FUNCTION, '_', '_');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'foo', 'foo');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'bar', 'bar');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'joe', 'joe');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'lol', 'lol');			
			$tpl->register(Opt_Class::PHP_FUNCTION, 'funct', 'funct');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'rotfl', 'rotfl');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'lmao1', '#1#lmao');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'lmao2', '#2,1#lmao');
			$tpl->register(Opt_Class::PHP_FUNCTION, 'lmao3', '#3,1,2:null#lmao');
			$tpl->register(Opt_Class::PHP_CLASS, 'class', '_class');
			$tpl->register(Opt_Class::PHP_CLASS, 'e', 'e');
			$tpl->register(Opt_Class::PHP_CLASS, 'u', 'u');
			$tpl->register(Opt_Class::PHP_CLASS, 'a', 'a');
			Opl_Registry::register('opl_translate', new tl);
			$tpl->setup();
			$this->tpl = $tpl;
			$this->cpl = new Opt_Compiler_Class($tpl);
		} // end setUp();
	 
		protected function tearDown()
		{
			$this->tpl = NULL;
			$this->cpl = NULL;
		} // end tearDown();

		public static function provider()
		{
			$exceptionClass = 'Opt_Expression_Exception';
			return array(
				array(false, '\'foo\'', '\'foo\'', 0),
				array(false, '42', '42', 0),
				array(false, '$foo', '$this->_data[\'foo\']', 0),
				array(false, '$2foo', '', $exceptionClass),
				array(false, '@foo', 'self::$_vars[\'foo\']', 0),
				array(false, '$foo@bar', '$this->_tf->_(\'foo\',\'bar\')', 0),
				array(false, '$foo[\'bar\']', '$this->_data[\'foo\'][\'bar\']', 0),
				array(false, '$foo.bar', '$this->_data[\'foo\'][\'bar\']', 0),
				array(false, '$foo.bar.joe', '$this->_data[\'foo\'][\'bar\'][\'joe\']', 0),
				array(false, '$foo.bar[\'joe\']', '$this->_data[\'foo\'][\'bar\'][\'joe\']', 0),
				array(false, '$foo[\'bar\'].joe', '', $exceptionClass),
				array(false, '$foo[1]', '$this->_data[\'foo\'][1]', 0),
				array(false, '$foo[$i]', '$this->_data[\'foo\'][$this->_data[\'i\']]', 0),
				array(false, '$foo[$i][\'bar\']', '$this->_data[\'foo\'][$this->_data[\'i\']][\'bar\']', 0),
				array(false, '@foo.bar', 'self::$_vars[\'foo\'][\'bar\']', 0),
				array(false, '$foo + $bar', '$this->_data[\'foo\']+$this->_data[\'bar\']', 0),
				array(false, '$foo - $bar', '$this->_data[\'foo\']-$this->_data[\'bar\']', 0),
				array(false, '-$bar', '-$this->_data[\'bar\']', 0),
				array(false, '$foo == $bar', '$this->_data[\'foo\']==$this->_data[\'bar\']', 0),
				array(false, '$foo + 5', '$this->_data[\'foo\']+5', 0),
				array(false, '$foo == 5', '$this->_data[\'foo\']==5', 0),
				array(false, '$foo~\'bar\'', '$this->_data[\'foo\'].\'bar\'', 0),
				array(false, '$foo == \'bar\'', '$this->_data[\'foo\']==\'bar\'', 0),
				array(false, '$foo gt 3', '$this->_data[\'foo\']>3', 0),
				array(true, '$foo is $bar + $joe', '$this->_data[\'foo\']=$this->_data[\'bar\']+$this->_data[\'joe\']', 0),
				array(false, '($foo + $bar) * $joe', '($this->_data[\'foo\']+$this->_data[\'bar\'])*$this->_data[\'joe\']', 0),
				array(false, '$joe * ($foo + $bar)', '$this->_data[\'joe\']*($this->_data[\'foo\']+$this->_data[\'bar\'])', 0),
				array(false, 'funct($foo + $bar)', 'funct($this->_data[\'foo\']+$this->_data[\'bar\'])', 0),
				array(false, 'funct($foo + $bar, 5, \'joe\')', 'funct($this->_data[\'foo\']+$this->_data[\'bar\'],5,\'joe\')', 0),
				array(false, 'foo($a, funct($foo + $bar, 5, \'joe\'), $c)', 'foo($this->_data[\'a\'],funct($this->_data[\'foo\']+$this->_data[\'bar\'],5,\'joe\'),$this->_data[\'c\'])', 0),
				array(false, 'foo(,,)', '', $exceptionClass),
				array(false, 'foo($a,)', '', $exceptionClass),
				array(false, '$object::funct($foo + $bar, 5, \'joe\')', '$this->_data[\'object\']->funct($this->_data[\'foo\']+$this->_data[\'bar\'],5,\'joe\')', 0),
				array(false, 'class::funct($foo + $bar, 5, \'joe\')', '_class::funct($this->_data[\'foo\']+$this->_data[\'bar\'],5,\'joe\')', 0),
				array(true, '$object is new class', '$this->_data[\'object\']=new _class', 0),
				array(false, 'funct(new class)', 'funct(new _class)', 0),
				array(true, '$object is new class($foo)', '$this->_data[\'object\']=new _class($this->_data[\'foo\'])', 0),
				array(false, 'funct(new class($foo))', 'funct(new _class($this->_data[\'foo\']))', 0),
				array(false, 'foo(bar(joe(1)))', 'foo(bar(joe(1)))', 0),
				array(false, 'foo()', 'foo()', 0),
				array(false, 'foo()::bar', 'foo()->bar', 0),
				array(false, 'foo()::bar()', 'foo()->bar()', 0),
				array(false, 'foo()::bar()::joe', 'foo()->bar()->joe', 0),
				array(false, '($a + $b))::bar()', '', $exceptionClass),
				array(false, 'foo(bar(joe(1))', '', $exceptionClass),
				array(false, 'foo()::', '', $exceptionClass),
				array(false, '$foo add $bar', '$this->_data[\'foo\']+$this->_data[\'bar\']', 0),
				array(false, '$foo sub $bar', '$this->_data[\'foo\']-$this->_data[\'bar\']', 0),
				array(false, '$foo mul $bar', '$this->_data[\'foo\']*$this->_data[\'bar\']', 0),
				array(false, '$foo div $bar', '$this->_data[\'foo\']/$this->_data[\'bar\']', 0),
				array(false, '$foo mod $bar', '$this->_data[\'foo\']%$this->_data[\'bar\']', 0),
				array(false, 'add ~ sub', '\'add\'.\'sub\'', 0),
				array(false, 'mul', '\'mul\'', 0),
				array(false, 'mul ~ div ~ mod', '\'mul\'.\'div\'.\'mod\'', 0),
				array(false, 'add $bar', '', $exceptionClass),
				array(false, '++$bar', '++$this->_data[\'bar\']', 0),
				array(false, '$bar++', '$this->_data[\'bar\']++', 0),
				array(false, '++$bar', '++$this->_data[\'bar\']', 0),
				array(false, '$bar++', '$this->_data[\'bar\']++', 0),
				array(false, 'foo(++$bar)', 'foo(++$this->_data[\'bar\'])', 0),
				array(false, 'foo($bar++)', 'foo($this->_data[\'bar\']++)', 0),
				array(false, 'foo($bar++)++', '', $exceptionClass),
				array(false, '++$obj::foo', '++$this->_data[\'obj\']->foo', 0),
				array(false, '$obj::foo++', '$this->_data[\'obj\']->foo++', 0),
				array(false, '++$obj::foo()', '', $exceptionClass),
				array(false, '$obj::foo()++', '', $exceptionClass),
				array(false, 'not($foo lt $bar)', '!($this->_data[\'foo\']<$this->_data[\'bar\'])', 0),
				array(false, 'not($foo lt $bar and $foo gt 6)', '!($this->_data[\'foo\']<$this->_data[\'bar\']&&$this->_data[\'foo\']>6)', 0),
				array(false, '$foo lt $bar and $foo gt 6', '$this->_data[\'foo\']<$this->_data[\'bar\']&&$this->_data[\'foo\']>6', 0),
				array(false, '$a + () + $c', '', $exceptionClass),
				array(false, '$x + (++foo() - 5) * 2', '', $exceptionClass),
				array(false, '--$obj::foo()', '', $exceptionClass),
				array(false, '++($a + $b)', '', $exceptionClass),
				array(false, '($a + $b)++', '', $exceptionClass),
				array(false, 'null', 'null', 0),
				array(false, 'false', 'false', 0),
				array(false, 'true', 'true', 0),
				array(false, 'true add false', 'true+false', 0),
				array(false, 'foo(null)', 'foo(null)', 0),
				array(false, '5 true 5', '', $exceptionClass),
				array(false, '5 false 5', '', $exceptionClass),
				array(false, '5 null 5', '', $exceptionClass),
				array(false, '$foo[null]', '$this->_data[\'foo\'][null]', 0),
				array(false, 'rotfl()', 'rotfl()', 0),
				array(false, 'rotfl($a)', 'rotfl($this->_data[\'a\'])', 0),
				array(false, 'rotfl($a, $b)', 'rotfl($this->_data[\'a\'],$this->_data[\'b\'])', 0),
				array(false, 'lmao1()', 'lmao()', 'Opt_FunctionArgument_Exception'),
				array(false, 'lmao1($a)', 'lmao($this->_data[\'a\'])', 0),
				array(false, 'lmao2($a, $b)', 'lmao($this->_data[\'b\'],$this->_data[\'a\'])', 0),
				array(false, 'lmao3($a, $b, $c)', 'lmao($this->_data[\'b\'],$this->_data[\'c\'],$this->_data[\'a\'])', 0),
				array(false, 'lmao3($a, $b)', 'lmao($this->_data[\'b\'],null,$this->_data[\'a\'])', 0),
				array(false, 'lol()', 'lol()', 0),
				array(false, 'lol($a)', 'lol($this->_data[\'a\'])', 0),
				array(false, 'lol($a, $b)', 'lol($this->_data[\'a\'],$this->_data[\'b\'])', 0),
				array(false, 'lol($a, $b, $c)', 'lol($this->_data[\'a\'],$this->_data[\'b\'],$this->_data[\'c\'])', 0),	
				array(false, 'assign($foo@bar, 5)', '$this->_tf->assign(\'foo\',\'bar\',5)', 0),
				array(true, '$foo@bar is 5', '', $exceptionClass),

				// Assignment stuff
				array(true, '$a = 5', '$this->_data[\'a\']=5', 0),
				array(true, '@a = 5', 'self::$_vars[\'a\']=5', 0),
				array(true, '$a = $b = 5', '$this->_data[\'a\']=$this->_data[\'b\']=5', 0),
				array(true, '++$a is 5', '', $exceptionClass),
				array(true, '$a + $b is $c + $d', '', $exceptionClass),
				array(true, 'foo() is $c + $d', '', $exceptionClass),
				array(true, '5 is 2', '', $exceptionClass),
				array(true, '$foo is', '', $exceptionClass),
				array(true, '$foo is ($bar is 3 * (5 + 3))', '$this->_data[\'foo\']=($this->_data[\'bar\']=3*(5+3))', 0),
				array(true, '$a = (((($b = 5))))', '$this->_data[\'a\']=(((($this->_data[\'b\']=5))))', 0),
				array(true, 'rotfl($a = 5)', 'rotfl($this->_data[\'a\']=5)', 0),
				array(true, 'rotfl($a = 5, $b = 10)', 'rotfl($this->_data[\'a\']=5,$this->_data[\'b\']=10)', 0),
				array(true, 'is', '\'is\'', $exceptionClass),
				array(true, 'is eq is', '\'is\'==\'is\'', $exceptionClass),
				array(true, '$foo is is', '$this->_data[\'foo\']=\'is\'', $exceptionClass),
				// Stupid syntax misuses
				array(false, '\'Text body\'~{$brackettedVariable}', '', $exceptionClass),
				array(false, 'Text body {$variable}', '', $exceptionClass),
				array(false, 'foo(\'text\'~$foo\')', '', $exceptionClass),
				array(false, 'foo(\'text\'~$foo\")', '', $exceptionClass),
				array(false, 'foo bar', '', $exceptionClass),
				// Other issues
				array(false, '_()', '_()', 0),
				array(false, '_(\'foo\')', '_(\'foo\')', 0),

				// Expression modifiers
				array(false, 'u:\'foo\'', '\'foo\'', 0),
				array(false, 'e:\'foo\'', 'htmlspecialchars(\'foo\')', 0),
				array(false, 'a:\'foo\'', '', 'Opt_InvalidExpressionModifier_Exception'),
				array(false, '\':\'', '\':\'', 0),
				array(false, 'e::method()', 'e::method()', 0),
				array(false, 'u::method()', 'u::method()', 0),
				array(false, 'a::method()', 'a::method()', 0),
		  	);
		} // end provider();

		/**
		 * @dataProvider provider
		 */
		public function testCompile($assign, $src, $dst, $result)
		{
			try
			{
				$info = $this->cpl->compileExpression($src, $assign, Opt_Compiler_Class::ESCAPE_BOTH);
				
				if($result == 0)
				{
					$this->assertEquals($dst, $info[0]);
					return true;
				}
				$this->fail('Exception NOT returned, but should be: '.$result);
			}
			catch(Opt_Exception $e)
			{
				if($result !== 0)
				{
					if($result != get_class($e))
					{
						$this->fail('Invalid exception returned: #'.get_class($e).', '.$result.' expected.');
					}
					return true;
				}
				$this->fail('Exception returned: #'.get_class($e).': '.$e->getMessage().' (line: '.$e->getLine().')');
			}
		} // end testCompile();
	} // end expressionTestSuite;
?>
