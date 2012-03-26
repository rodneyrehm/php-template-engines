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
		public function testOutputCancellation()
		{
			ob_start();

			ob_start();

			echo 'XYZ';

			$eh = new Opl_ErrorHandler;
			$eh->display(new Opl_Exception('Foo'));

			$out = ob_get_clean();

			if(strpos($out, 'XYZ') !== false)
			{
				$this->fail('The earlier string XYZ was not cancelled by the exception handler.');
			}
			return true;
		} // end testOutputCancellation();

	} // end exceptionTest;