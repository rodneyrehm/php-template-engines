<?php
/*
 * XML TEST
 * ------------------------------------
 * These tests check whether the XML tree API works properly.
 */
	require_once('PHPUnit/Framework.php');

	if(version_compare(phpversion(), '5.3.0-dev', '<'))
	{
		die("This test suite requires at least PHP 5.3.0. Your version: ".phpversion()."\r\n");
	}

	if(!defined('GROUPED'))
	{
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}

	/**
	 * @backupStaticAttributes disabled
	 */
	class treeTest extends PHPUnit_Framework_TestCase
	{
		protected $tpl;

		protected function setUp()
		{

			$this->tpl = new Opt_Class;
			$this->tpl->sourceDir = './templates';
			$this->tpl->compileDir = './templates_c';
		} // end setUp();

		protected function tearDown()
		{
			$this->tpl = NULL;
		} // end tearDown();

		public function testBufferGetBuffer()
		{
			$buffer = new Opt_Xml_Root();
			if(!is_array($buffer->getBuffer(Opt_Xml_Buffer::TAG_BEFORE)))
			{
				$this->fail('The buffer value is not an array.');
			}
			return true;
		} // end testBufferGetBuffer();

		public function testBufferAddAfter1()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'foo')
			{
				return true;
			}
			$this->fail('The value did not reach the code buffer.');
		} // end testBufferAddAfter1();

		public function testBufferAddAfter2()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'foo'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][1] == 'bar'
				)
			{
				return true;
			}
			$this->fail('The values did not reach the code buffer in the correct order.');
		} // end testBufferAddAfter2();

		public function testBufferAddBefore1()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'foo');

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE]) && $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'foo')
			{
				return true;
			}
			$this->fail('The value did not reach the code buffer.');
		} // end testBufferAddBefore1();

		public function testBufferAddBefore2()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'bar');

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'bar'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][1] == 'foo'
				)
			{
				return true;
			}
			$this->fail('The values did not reach the code buffer in the correct order.');
		} // end testBufferAddBefore2();

		public function testBufferAddMixed()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');
			$buffer->addBefore(Opt_Xml_Buffer::TAG_BEFORE, 'joe');

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'joe'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][1] == 'foo'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][2] == 'bar'
				)
			{
				return true;
			}
			$this->fail('The values did not reach the code buffer in the correct order.');
		} // end testBufferAddMixed();

		public function testBufferCopy1()
		{
			$buffer1 = new Opt_Xml_Root();
			$buffer1->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer1->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');

			$buffer2 = new Opt_Xml_Root();
			$buffer2->copyBuffer($buffer1, Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_BEFORE);

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer2);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'foo'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][1] == 'bar'
				)
			{
				return true;
			}
			$this->fail('The values have not been copied between nodes.');
		} // end testBufferCopy1();

		public function testBufferCopy2()
		{
			$buffer1 = new Opt_Xml_Root();
			$buffer1->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer1->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');

			$buffer2 = new Opt_Xml_Root();
			$buffer2->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'joe');

			$buffer2->copyBuffer($buffer1, Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_BEFORE);

			$property = new ReflectionProperty('Opt_Xml_Buffer', '_buffers');
			$property->setAccessible(true);
			$value = $property->getValue($buffer2);

			if(is_array($value) && is_array($value[Opt_Xml_Buffer::TAG_BEFORE])
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][0] == 'foo'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][1] == 'bar'
				&& $value[Opt_Xml_Buffer::TAG_BEFORE][2] == 'joe'
				)
			{
				return true;
			}
			echo "testBufferCopy list:\r\n";
			var_dump($value);
			$this->fail('The copied values have not been added before the existing values.');
		} // end testBufferCopy2();

		public function testBufferSize()
		{
			$buffer = new Opt_Xml_Root();

			$size0 = $buffer->bufferSize(Opt_Xml_Buffer::TAG_BEFORE);

			$buffer->addAfter(Opt_Xml_Buffer::TAG_AFTER, 'foo');
			$size1 = $buffer->bufferSize(Opt_Xml_Buffer::TAG_BEFORE);

			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$size2 = $buffer->bufferSize(Opt_Xml_Buffer::TAG_BEFORE);

			if($size0 == 0 && $size1 == 0 && $size2 == 1)
			{
				return true;
			}

			$this->fail('Invalid buffer size reported by bufferSize().');
		} // end testBufferSize();

		public function testBufferBuildCode1()
		{
			$buffer = new Opt_Xml_Root();

			$this->assertEquals('', $buffer->buildCode('foo'));
		} // end testBufferBuildCode1();

		public function testBufferBuildCode2()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');

			$this->assertEquals('<?php foo  echo \'foo\';  ?>', $buffer->buildCode(Opt_Xml_Buffer::TAG_BEFORE, 'foo'));
		} // end testBufferBuildCode2();

		public function testBufferBuildCode3()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_AFTER, 'joe');

			$this->assertEquals('<?php foo bar joe  ?>', $buffer->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_AFTER));
		} // end testBufferBuildCode3();

		public function testBufferBuildCode4()
		{
			$buffer = new Opt_Xml_Root();
			$buffer->set('nophp', true);
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'foo');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_BEFORE, 'bar');
			$buffer->addAfter(Opt_Xml_Buffer::TAG_AFTER, 'joe');

			$this->assertEquals('foo bar joe', $buffer->buildCode(Opt_Xml_Buffer::TAG_BEFORE, Opt_Xml_Buffer::TAG_AFTER));
		} // end testBufferBuildCode4();

		public function testNodeSetParent()
		{
			$buffer = new Opt_Xml_Root();
			$element = new Opt_Xml_Element('foo');
			$element->setParent($buffer);

			if($element->getParent() === $buffer)
			{
				return true;
			}
			$this->fail('The element parent has not been set properly.');
		} // end testNodeSetParent();

		public function testRootGetType()
		{
			$buffer = new Opt_Xml_Root();
			$this->assertEquals('Opt_Xml_Root', $buffer->getType());
		} // end testRootGetType();

		public function testElementGetType()
		{
			$buffer = new Opt_Xml_Element('foo');
			$this->assertEquals('Opt_Xml_Element', $buffer->getType());
		} // end testElementGetType();

		public function testTextGetType()
		{
			$buffer = new Opt_Xml_Text();
			$this->assertEquals('Opt_Xml_Text', $buffer->getType());
		} // end testTextGetType();

		public function testCdataGetType()
		{
			$buffer = new Opt_Xml_Cdata('foo');
			$this->assertEquals('Opt_Xml_Cdata', $buffer->getType());
		} // end testTextGetType();

		public function testScannableAppendChild()
		{
			$topNode = new Opt_Xml_Element('foo');

			$subNode = new Opt_Xml_Element('bar');

			$topNode->appendChild($subNode);

			foreach($topNode as $n)
			{
				$this->assertTrue($n === $subNode);
			}
		} // end testScannableAppendChild();

		public function testScannableInsertBefore1()
		{
			$topNode = new Opt_Xml_Element('foo');

			$subnodes = array(0 =>
				new Opt_Xml_Element('bar'),
				new Opt_Xml_Element('joe'),
				new Opt_Xml_Element('goo'),
			);

			$topNode->appendChild($subnodes[0]);
			$topNode->appendChild($subnodes[2]);
			$topNode->insertBefore($subnodes[1], 1);

			$ok = true;
			$i = 0;
			foreach($topNode as $n)
			{
				if($n !== $subnodes[$i])
				{
					$ok = false;
				}
				$i++;
			}
			$this->assertTrue($ok);
		} // end testScannableInsertBefore1();

		public function testScannableInsertBefore2()
		{
			$topNode = new Opt_Xml_Element('foo');

			$subnodes = array(0 =>
				new Opt_Xml_Element('bar'),
				new Opt_Xml_Element('joe'),
				new Opt_Xml_Element('goo'),
			);

			$topNode->appendChild($subnodes[0]);
			$topNode->appendChild($subnodes[1]);
			$topNode->insertBefore($subnodes[2]);

			$ok = true;
			$i = 0;
			foreach($topNode as $n)
			{
				if($n !== $subnodes[$i])
				{
					$ok = false;
				}
				$i++;
			}
			$this->assertTrue($ok);
		} // end testScannableInsertBefore2();

	} // end treeTest;
