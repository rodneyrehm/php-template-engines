<?php /* Smarty version Smarty 3.1-DEV, created on 2011-12-06 18:18:55
         compiled from "/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/test.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18905306074ede4e7fb01615-71390763%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82903bd14b1b1aa692d1b2e60f39a99796e67fba' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/test.tpl',
      1 => 1312561077,
      2 => 'file',
    ),
    'f01805b69948de1353e47c4c05c00cda9af3dec3' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/framework.tpl',
      1 => 1311708187,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18905306074ede4e7fb01615-71390763',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1-DEV',
  'unifunc' => 'content_4ede4e7fb7cac',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ede4e7fb7cac')) {function content_4ede4e7fb7cac($_smarty_tpl) {?>
    <?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable("PHP Template Engine Comparison", null, 0);?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true);?>
</title>
	<link rel="stylesheet" type="text/css" href="css/screen.css" />
	<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="js/highcharts/highcharts.js"></script>
    <script type="text/javascript" src="js/highcharts/modules/exporting.js"></script>
	<script type="text/javascript" src="js/charts.js"></script>
</head>
<body>



    <?php  $_smarty_tpl->tpl_vars['test'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['tests']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['test']->key => $_smarty_tpl->tpl_vars['test']->value){
?>
        <div class="chart test-chart" 
            data-series="<?php echo htmlspecialchars(json_encode($_smarty_tpl->tpl_vars['test']->value['data']['duration']), ENT_QUOTES, 'UTF-8', true);?>
" 
            data-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['test'], ENT_QUOTES, 'UTF-8', true);?>
 Test" 
            data-subtitle="Duration"
            data-axis-name="Duration"
            data-axis-abbr="s"
        ></div>
        <div class="chart test-chart" 
            data-series="<?php echo htmlspecialchars(json_encode($_smarty_tpl->tpl_vars['test']->value['data']['memory']), ENT_QUOTES, 'UTF-8', true);?>
" 
            data-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['test'], ENT_QUOTES, 'UTF-8', true);?>
 Test" 
            data-subtitle="Memory consumption"
            data-axis-name="Memory"
            data-axis-abbr="MB"
        ></div>
    <?php }} ?>


</body>
</html>
<?php }} ?>