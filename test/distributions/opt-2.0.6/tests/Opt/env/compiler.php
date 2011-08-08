<?php
	function getCompilerEnvironment()
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = './templates/';
		$tpl->compileDir = './templates_c/';
		$tpl->setup();
		$tpl->compiler = new Opt_Compiler_Class($tpl);
		
		return $tpl;
	} // end getCompilerEnvironment();
?>
