<?php
/*
 * API TEST
 * ------------------------------------
 * The goal of this test is to check that all the API is correctly named and set. This is especially useful
 * while making new releases, when we have to be sure that the function and field names are not changed
 * by mistake. 
 * 
 * The testing procedure also checks the OPT manual.
 */
	require_once('PHPUnit/Framework.php');

	define('M_PUBLIC', 1);
	define('M_PROTECTED', 2);
	define('M_STATIC', 4);
	define('M_FINAL', 8);
	define('M_ABSTRACT', 16);

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

	class apiTest extends PHPUnit_Framework_TestCase
	{
		protected $classes = array();
		protected $methods = array();
		protected $loaded = false;

		protected function setUp()
		{
			if(!$this->loaded)
			{
				$this->classes = array(
					'Opt_Class' => new ReflectionClass('Opt_Class'),
					'Opt_View' => new ReflectionClass('Opt_View'),
					'Opt_Compiler_Class' => new ReflectionClass('Opt_Compiler_Class'),
					'Opt_Compiler_Processor' => new ReflectionClass('Opt_Compiler_Processor'),
					'Opt_Compiler_Format' => new ReflectionClass('Opt_Compiler_Format'),
					'Opt_Output_Http' => new ReflectionClass('Opt_Output_Http'),
					'Opt_Output_Return' => new ReflectionClass('Opt_Output_Return'),
					'Opt_Xml_Buffer' => new ReflectionClass('Opt_Xml_Buffer'),
					'Opt_Xml_Node' => new ReflectionClass('Opt_Xml_Node'),
					'Opt_Xml_Scannable' => new ReflectionClass('Opt_Xml_Scannable'),
					'Opt_Xml_Text' => new ReflectionClass('Opt_Xml_Text'),
					'Opt_Xml_Cdata' => new ReflectionClass('Opt_Xml_Cdata'),
					'Opt_Xml_Expression' => new ReflectionClass('Opt_Xml_Expression'),
					'Opt_Xml_Element' => new ReflectionClass('Opt_Xml_Element'),
					'Opt_Xml_Root' => new ReflectionClass('Opt_Xml_Root'),
					'Opt_Xml_Prolog' => new ReflectionClass('Opt_Xml_Prolog'),
					'Opt_Xml_Dtd' => new ReflectionClass('Opt_Xml_Dtd'),
				);

				if(!is_dir(DOC_DIR.'input/en/'))
				{
					die('Please copy the source documentation to docs/input/en/');
				}
				$this->loaded = true;
			}
		} // end setUp();
	 
		protected function tearDown()
		{
			$this->tpl = NULL;
		} // end tearDown();
		
		protected function inManual($id)
		{
			if(is_null($id))
			{
				// This is not supposed to be documented.
				return true;
			}
			return file_exists(DOC_DIR.'input/en/'.$id.'.txt');
		} // end inManual();
		
		public static function methodProvider()
		{
			return array(0 =>
				// Opt_Class methods
				array(0 => 'Opt_Class', 'getCompiler', 'api.opt-class.get-compiler', M_PUBLIC),
				array(0 => 'Opt_Class', 'setup', 'api.opt-class.setup', M_PUBLIC),
				array(0 => 'Opt_Class', 'register', 'api.opt-class.register', M_PUBLIC),
				array(0 => 'Opt_Class', 'getTranslationInterface', 'api.opt-class.get-translation-interface', M_PUBLIC),
				array(0 => 'Opt_Class', 'setTranslationInterface', 'api.opt-class.set-translation-interface', M_PUBLIC),
				array(0 => 'Opt_Class', 'setCache', 'api.opt-class.set-cache', M_PUBLIC),
				array(0 => 'Opt_Class', 'getCache', 'api.opt-class.get-cache', M_PUBLIC),
				array(0 => 'Opt_Class', '_getList', null, M_PUBLIC),

				array(0 => 'Opt_Class', 'getBufferState', 'api.opt-class.get-buffer-state', M_PUBLIC),
				array(0 => 'Opt_Class', 'setBufferState', 'api.opt-class.set-buffer-state', M_PUBLIC),

				// Opt_View methods
				array(0 => 'Opt_View', '__construct', 'api.opt-view.__construct', M_PUBLIC),
				array(0 => 'Opt_View', '__set', 'api.opt-view.__set', M_PUBLIC),
				array(0 => 'Opt_View', '__isset', 'api.opt-view.__isset', M_PUBLIC),
				array(0 => 'Opt_View', '__unset', 'api.opt-view.__unset', M_PUBLIC),
				array(0 => 'Opt_View', '__get', 'api.opt-view.__get', M_PUBLIC),
				array(0 => 'Opt_View', 'assign', 'api.opt-view.assign', M_PUBLIC),
				array(0 => 'Opt_View', 'assignGroup', 'api.opt-view.assign-group', M_PUBLIC),
				array(0 => 'Opt_View', 'assignRef', 'api.opt-view.assign-ref', M_PUBLIC),
				array(0 => 'Opt_View', 'defined', 'api.opt-view.defined', M_PUBLIC),
				array(0 => 'Opt_View', 'remove', 'api.opt-view.remove', M_PUBLIC),
				array(0 => 'Opt_View', 'get', 'api.opt-view.get', M_PUBLIC),

				array(0 => 'Opt_View', 'assignGlobal', 'api.opt-view.assign-global', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_View', 'assignGroupGlobal', 'api.opt-view.assign-group-global', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_View', 'assignRefGlobal', 'api.opt-view.assign-ref-global', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_View', 'definedGlobal', 'api.opt-view.defined-global', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_View', 'removeGlobal', 'api.opt-view.remove-global', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_View', 'getGlobal', 'api.opt-view.get-global', M_PUBLIC | M_STATIC),

				array(0 => 'Opt_View', 'clear', 'api.opt-view.clear', M_PUBLIC | M_STATIC),
				
				array(0 => 'Opt_View', 'inherit', 'api.opt-view.inherit', M_PUBLIC),
				array(0 => 'Opt_View', 'getBranch', 'api.opt-view.get-branch', M_PUBLIC),
				array(0 => 'Opt_View', 'setBranch', 'api.opt-view.set-branch', M_PUBLIC),
				array(0 => 'Opt_View', 'getTime', 'api.opt-view.get-time', M_PUBLIC),
				array(0 => 'Opt_View', 'getTemplate', 'api.opt-view.get-template', M_PUBLIC),
				array(0 => 'Opt_View', 'setTemplate', 'api.opt-view.set-template', M_PUBLIC),
				array(0 => 'Opt_View', 'setCache', 'api.opt-view.get-cache', M_PUBLIC),
				array(0 => 'Opt_View', 'getCache', 'api.opt-view.set-cache', M_PUBLIC),

				array(0 => 'Opt_View', 'hasDynamicContent', 'api.opt-view.has-dynamic-content', M_PUBLIC),
				array(0 => 'Opt_View', 'getOutputBuffers', 'api.opt-view.get-output-buffers', M_PUBLIC),

				// Opt_Compiler_Class methods
				array(0 => 'Opt_Compiler_Class', '__construct', 'api.opt-compiler-class.__construct', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'getCurrentTemplate', 'api.opt-compiler-class.get-current-template', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_Compiler_Class', 'cleanCompiler', 'api.opt-compiler-class.clean-compiler', M_PUBLIC | M_STATIC),
				array(0 => 'Opt_Compiler_Class', 'get', 'api.opt-compiler-class.get', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'set', 'api.opt-compiler-class.set', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'escape', 'api.opt-compiler-class.escape', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'getFormat', 'api.opt-compiler-class.get-format', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'createFormat', 'api.opt-compiler-class.create-format', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'setFormatList', 'api.opt-compiler-class.set-format-list', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'convert', 'api.opt-compiler-class.convert', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'setConversion', 'api.opt-compiler-class.set-conversion', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'unsetConversion', 'api.opt-compiler-class.unset-conversion', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'setInheritance', 'api.opt-compiler-class.set-inheritance', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'parseEntities', 'api.opt-compiler-class.parse-entities', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'parseShortEntities', 'api.opt-compiler-class.parse-short-entities', M_PUBLIC),

				array(0 => 'Opt_Compiler_Class', 'isIdentifier', 'api.opt-compiler-class.is-identifier', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isInstruction', 'api.opt-compiler-class.is-instruction', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isOptAttribute', 'api.opt-compiler-class.is-opt-attribute', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isFunction', 'api.opt-compiler-class.is-function', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isClass', 'api.opt-compiler-class.is-class', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isNamespace', 'api.opt-compiler-class.is-namespace', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isComponent', 'api.opt-compiler-class.is-component', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isBlock', 'api.opt-compiler-class.is-block', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'isProcessor', 'api.opt-compiler-class.is-processor', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'processor', 'api.opt-compiler-class.processor', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'component', 'api.opt-compiler-class.component', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'block', 'api.opt-compiler-class.block', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'inherits', 'api.opt-compiler-class.inherits', M_PUBLIC),

				array(0 => 'Opt_Compiler_Class', 'addDependantTemplate', 'api.opt-compiler-class.add-dependant-template', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'importDependencies', 'api.opt-compiler-class.import-dependencies', M_PUBLIC),

				array(0 => 'Opt_Compiler_Class', 'compile', 'api.opt-compiler-class.compile', M_PUBLIC),
				array(0 => 'Opt_Compiler_Class', 'compileExpression', 'api.opt-compiler-class.compile-expression', M_PUBLIC),

				// Opt_Compiler_Processor methods
				array(0 => 'Opt_Compiler_Processor', '__construct', 'api.opt-compiler-processor.__construct', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'configure', 'api.opt-compiler-processor.configure', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'reset', 'api.opt-compiler-processor.reset', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'processNode', 'api.opt-compiler-processor.process-node', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'postprocessNode', 'api.opt-compiler-processor.postprocess-node', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'processAttribute', 'api.opt-compiler-processor.process-attribute', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'postprocessAttribute', 'api.opt-compiler-processor.postprocess-attribute', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'processSystemVar', 'api.opt-compiler-processor.process-system-var', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', 'getName', 'api.opt-compiler-processor.get-name', M_PUBLIC),
				array(0 => 'Opt_Compiler_Processor', '_process', 'api.opt-compiler-processor._process', M_PROTECTED),
				array(0 => 'Opt_Compiler_Processor', '_addInstructions', 'api.opt-compiler-processor._add-instructions', M_PROTECTED),
				array(0 => 'Opt_Compiler_Processor', '_addAttributes', 'api.opt-compiler-processor._add-attributes', M_PROTECTED),
				array(0 => 'Opt_Compiler_Processor', '_extractAttributes', 'api.opt-compiler-processor._extract-attributes', M_PROTECTED),

				// Opt_Compiler_Format methods
				array(0 => 'Opt_Compiler_Format', '__construct', 'api.opt-compiler-format.__construct', M_PUBLIC),
				array(0 => 'Opt_Compiler_Format', '_build', 'api.opt-compiler-format._build', M_PROTECTED | M_ABSTRACT),
				array(0 => 'Opt_Compiler_Format', '_getVar', 'api.opt-compiler-format._get-var', M_PROTECTED | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'property', 'api.opt-compiler-format.property', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'action', 'api.opt-compiler-format.action', M_PUBLIC),
				array(0 => 'Opt_Compiler_Format', 'supports', 'api.opt-compiler-format.supports', M_PUBLIC),
				array(0 => 'Opt_Compiler_Format', 'getName', 'api.opt-compiler-format.get-name', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'assign', 'api.opt-compiler-format.assign', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'defined', 'api.opt-compiler-format.defined', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'resetVars', 'api.opt-compiler-format.reset-vars', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'get', 'api.opt-compiler-format.get', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'decorate', 'api.opt-compiler-format.decorate', M_PUBLIC | M_FINAL),
				array(0 => 'Opt_Compiler_Format', 'isDecorating', 'api.opt-compiler-format.is-decorating', M_PUBLIC | M_FINAL),

			);
		} // end provider();

		/**
 		 * @dataProvider methodProvider
 		 */
		public function testMethod($class, $method, $manualId, $type)
		{
			if(!$this->classes[$class]->hasMethod($method))
			{
				$this->fail('Method '.$method.' not found in '.$class);
			}
			$methodObj = $this->classes[$class]->getMethod($method);
			if($type & M_PUBLIC)
			{
				if(!$methodObj->isPublic())
				{
					$this->fail('Method '.$class.'::'.$method.' is not public.');
				}
			}
			elseif($type & M_PROTECTED)
			{
				if(!$methodObj->isProtected())
				{
					$this->fail('Method '.$class.'::'.$method.' is not protected.');
				}
			}
			if($type & M_STATIC)
			{
				if(!$methodObj->isStatic())
				{
					$this->fail('Method '.$class.'::'.$method.' is not static.');
				}
			}
			if($type & M_FINAL)
			{
				if(!$methodObj->isFinal())
				{
					$this->fail('Method '.$class.'::'.$method.' is not final.');
				}
			}
			if($type & M_ABSTRACT)
			{
				if(!$methodObj->isAbstract())
				{
					$this->fail('Method '.$class.'::'.$method.' is not abstract.');
				}
			}

			if(!$this->inManual($manualId))
			{
				$this->fail('Method '.$class.'::'.$method.' not found in the manual.');
			}
			return true;
		} // end testMethod();
	} // end expressionTestSuite;
?>
