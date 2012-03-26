<?php /* Smarty version Smarty-3.1.8, created on 2012-03-26 15:58:54
         compiled from "/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/distribution.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5951187104f70761e1fada7-41588630%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12ea3363b9fb8d8a3bd61038ca8a76d4958bbfca' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/distribution.tpl',
      1 => 1311712126,
      2 => 'file',
    ),
    'f01805b69948de1353e47c4c05c00cda9af3dec3' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/framework.tpl',
      1 => 1311708187,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5951187104f70761e1fada7-41588630',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4f70761e253d07_60045427',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f70761e253d07_60045427')) {function content_4f70761e253d07_60045427($_smarty_tpl) {?>
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



    <?php  $_smarty_tpl->tpl_vars['test'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['test']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tests']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['test']->key => $_smarty_tpl->tpl_vars['test']->value){
$_smarty_tpl->tpl_vars['test']->_loop = true;
?>
        <div class="chart distribution-test-chart" 
            data-series="<?php echo htmlspecialchars(json_encode($_smarty_tpl->tpl_vars['test']->value['data']), ENT_QUOTES, 'UTF-8', true);?>
" 
            data-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['test'], ENT_QUOTES, 'UTF-8', true);?>
 Test" 
            data-subtitle="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['distribution'], ENT_QUOTES, 'UTF-8', true);?>
"
        ></div>
    <?php } ?>



</body>
</html>
<?php }} ?>