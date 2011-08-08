<?php /* Smarty version Smarty 3.1-DEV, created on 2011-08-05 19:44:28
         compiled from "/users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/distribution.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20207503344e3c2bfc50d811-11627720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae6d3619b2ffccabba43d0823018aa5e60af6717' => 
    array (
      0 => '/users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/distribution.tpl',
      1 => 1311712126,
      2 => 'file',
    ),
    'e698878c0f3d74b3b0d27489035fa41cfb23c62c' => 
    array (
      0 => '/users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/framework.tpl',
      1 => 1311708187,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20207503344e3c2bfc50d811-11627720',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1-DEV',
  'unifunc' => 'content_4e3c2bfc5fce9',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4e3c2bfc5fce9')) {function content_4e3c2bfc5fce9($_smarty_tpl) {?>
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
        <div class="chart distribution-test-chart" 
            data-series="<?php echo htmlspecialchars(json_encode($_smarty_tpl->tpl_vars['test']->value['data']), ENT_QUOTES, 'UTF-8', true);?>
" 
            data-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['test'], ENT_QUOTES, 'UTF-8', true);?>
 Test" 
            data-subtitle="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['test']->value['distribution'], ENT_QUOTES, 'UTF-8', true);?>
"
        ></div>
    <?php }} ?>



</body>
</html>
<?php }} ?>