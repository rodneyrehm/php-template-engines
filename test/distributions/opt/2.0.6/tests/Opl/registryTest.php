<?php
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		Opl_Loader::loadPaths($config);
		Opl_Loader::register();
	}

	class exceptionTest extends PHPUnit_Framework_TestCase
	{
		public function testSettingState()
		{
			Opl_Registry::setState('foo', 'bar');

			$this->assertEquals(Opl_Registry::getState('foo'), 'bar');
		} // end testSettingState();

		public function testSettingValue()
		{
			Opl_Registry::register('foo', 'bar');

			$this->assertEquals(Opl_Registry::get('foo'), 'bar');
		} // end testSettingValue();

		public function testExistence()
		{
			Opl_Registry::register('foo', 'bar');
			$this->assertTrue(Opl_Registry::exists('foo'));
		} // end testExistence();
	} // end exceptionTest;