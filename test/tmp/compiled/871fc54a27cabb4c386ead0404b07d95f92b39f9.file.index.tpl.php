<?php /* Smarty version Smarty 3.1-DEV, created on 2011-12-04 19:48:50
         compiled from "/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15134670974edbc092131ac5-20938398%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '871fc54a27cabb4c386ead0404b07d95f92b39f9' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/index.tpl',
      1 => 1323024514,
      2 => 'file',
    ),
    'f01805b69948de1353e47c4c05c00cda9af3dec3' => 
    array (
      0 => '/Users/rrehm/Projekte/test.dev/htdocs/template-engines/render/../render/templates/framework.tpl',
      1 => 1311708187,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15134670974edbc092131ac5-20938398',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1-DEV',
  'unifunc' => 'content_4edbc0921c079',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edbc0921c079')) {function content_4edbc0921c079($_smarty_tpl) {?>
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


    
    <ul>
    <li><a href="tests.html">Tests</a>
    <?php  $_smarty_tpl->tpl_vars['active'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['distributions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['active']->key => $_smarty_tpl->tpl_vars['active']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['active']->key;
?>
        <li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['key']->value, ENT_QUOTES, 'UTF-8', true);?>
.html"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['key']->value, ENT_QUOTES, 'UTF-8', true);?>
</a>
    <?php }} ?>
    </li>


</body>
</html>
<?php }} ?>