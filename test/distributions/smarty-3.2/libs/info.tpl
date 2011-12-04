<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Smarty::info()</title>

    <style type="text/css">
        {*
            background: #F0C040 (transition from #F5D57F)
            text:       #333
            hilite:     #DD6900
        *}
        
        body {
            width: 800px;
            margin: 0 auto;
        }
        
        table {
            border-collapse: collapse;
            border-spacing: 0; 
            empty-cells: show;
            table-layout: fixed;
            width: 100%;
        }
        th, td {
            text-align: left;
            vertical-align: top;
            border: 1px solid #CCC;
            padding: 3px;
        }
        
        .diff {
            background: #9999FF;
        }
        .equal {
            color: #BBB;
        }
        .not-appliccable {}
        
        .error {
            background: #FF9999;
        }
        .integer {}
        .callable {}
        .float {}
        .array {}
        .object {}
        .resource {}
        .string {}
        .directory {}
        .filename {}
        .classname {}
        .line {}
        .empty,
        .null,
        .boolean,
        .flag {
            font-style: italic;
        }
        
    </style>
</head>
<body>

{function prettyprint value="" flag=""}
    {$_flag = ""}
    {if $flag}
        {$_flag = "flag"}
    {/if}
    {if is_null($value)}
        <span class="null {$_flag}">NULL</span>
    {elseif is_bool($value)}
        <span class="boolean {$_flag}">{if $value}TRUE{else}FALSE{/if}</span>
    {elseif is_int($value)}
        <span class="integer {$_flag}">{$value}</span>
    {elseif is_float($value)}
        <span class="float {$_flag}">{$value|string_format:"%0.4f"}</span>
    {elseif is_array($value)}
        <span class="array {$_flag}">[{$value|join:", "|escape}]</span>
    {elseif is_object($value)}
        <span class="object {$_flag}">{$value|get_class}</span>
    {elseif is_resource($value)}
        <span class="resource {$_flag}">resource</span>
    {elseif $value}
        <span class="string {$_flag}">{$value|escape}</span>
    {else}
        <span class="string empty {$_flag}">(empty string)</span>
    {/if}
{/function}

<header id="top">
    <img id="logo" src="data:image/png;base64,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" alt="Smarty">
    <hgroup>
        <h1>Smarty Version {$data.version}</h1>
        <h2>PHP Version {$data.php.version}</h2>
    </hgroup>
</header>

{if $data.errors}
    <section>
        <header>
            <h1 id="errors">{$data.errors|@count} Errors</h1>
        </header>
        <ul>
        {foreach $data.errors as $error}
            <li><a href="#{$error@key|escape}">{$error|escape}</a></li>
        {/foreach}
        </ul>
    </section>
{/if}
{if $data.warnings}
    <section>
        <header>
            <h1 id="errors">{$data.warnings|@count} Warnings</h1>
        </header>
        <ul>
        {foreach $data.warnings as $warning}
            <li><a href="#{$warning@key|escape}">{$warning|escape}</a></li>
        {/foreach}
        </ul>
    </section>
{/if}

{if $data.php}
<section>
    <header>
        <h1 id="php-environment">PHP Environment</h1>
    </header>
    
    {$data.php|var_dump}
    
</section>
{/if}

{if $data.constants}
<section>
    <header>
        <h1 id="constants">Constants</h1>
    </header>
    
    <table>
        <thead>
            <tr>
                <th>Constant</th>
                <th>Value</th>
                <th>Default</th>
            </tr>
        </thead>
        <tbody>
            {foreach $data.constants as $name => $const}
            <tr>
                <th id="constants-{$name|escape}">{$name|escape}</th>
                <td>{prettyprint value=$const.value}</td>
                <td>{prettyprint value=$const.default}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</section>
{/if}

{if $data.properties}
<section>
    <header>
        <h1 id="properties">Properties</h1>
    </header>
    <table>
        <thead>
            <tr>
                <th>Option</th>
                {if $_template}
                    <th>Template</th>
                {/if}
                <th>Smarty</th>
                <th>Default</th>
            </tr>
        </thead>
        <tbody>
        {foreach $data.properties as $prop}
            <tr>
                <th id="properties-{$prop@key|escape}"><a href="{$prop.link|escape}" title="{$prop.name|escape}">{$prop@key|escape}</a></th>
                {if $_template}
                    {if $prop.template !== "#!$#notappliccable#$!#"}
                        <td class="{if $prop.template_diff}diff{else}equal{/if}">
                            {prettyprint value=$prop.template flag=$prop.flag}
                        </td>
                    {else}
                        <td class="not-appliccable"></td>
                    {/if}
                {/if}
                <td class="{if $prop.smarty_diff}diff{else}equal{/if}">{prettyprint value=$prop.smarty flag=$prop.flag}</td>
                <td>{prettyprint value=$prop.default flag=$prop.flag}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</section>
{/if}

{if $data.filesystem}
<section>
    <header>
        <h1 id="filesystem">Filesystem</h1>
    </header>

    <dl>
        {foreach $data.filesystem as $d => $directories}
        <dt id="filesystem-{$d|escape}">
            {if $d == "template_dir"}Template Directories
            {elseif $d == "config_dir"}Config Directories
            {elseif $d == "plugins_dir"}Plugins Directories
            {elseif $d == "cache_dir"}Cache Directory
            {elseif $d == "compile_dir"}Compile Directory
            {else}{$d|escape}
            {/if}
        </dt>
        <dd>
            <table>
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Configured</th>
                        {if $_smarty->use_include_path}
                            <th>Include_Path</th>
                        {/if}
                        <th>Realpath</th>
                        <th>Readable</th>
                        <th>Writable</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $directories as $dir}
                    <tr>
                        {if $dir.key !== null}
                            <td>{prettyprint value=$dir.key}</td>
                        {else}
                            <td class="not-appliccable"></td>
                        {/if}
                        <td><span class="directory">{$dir.path|escape}</span></td>
                        {if $_smarty->use_include_path}
                            <td><span class="directory">{$dir.includepath|escape}</span></td>
                        {/if}
                        <td><span class="directory">{$dir.realpath|escape}</span></td>
                        {if $dir.error}
                            <td colspan="2" class="error">{$dir.error|escape}</td>
                        {else}
                            <td>{prettyprint value=$dir.readable}</td>
                            <td>{prettyprint value=$dir.writable}</td>
                        {/if}
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </dd>
        {/foreach}
    </dl>
    
</section>
{/if}

{if $data.plugins}
<section>
    <header>
        <h1 id="plugins">Plugins</h1>
    </header>
    
    <p>
        TODO: show function signature (default or reflected), nocache-attributes, file+line of registered plugins
    </p>
 
    <dl>
        {foreach $data.plugins as $p => $plugins}
        <dt id="plugins-{$p|escape}">
            {if $p == "function"}Function Plugins
            {elseif $p == "modifier"}Modifier Plugins
            {elseif $p == "modifiercompiler"}Compiled Modifier Plugins
            {elseif $p == "block"}Block Plugins
            {elseif $p == "compiler"}Compiler Plugins
            {elseif $p == "prefilter"}Prefilter Plugins
            {elseif $p == "postfilter"}Postfilter Plugins
            {elseif $p == "outputfilter"}Outputfilter Plugins
            {elseif $p == "variablefilter"}Variablefilter Plugins
            {elseif $p == "insert"}Insert Plugins
            {elseif $p == "resource"}Resource Plugins
            {elseif $p == "cacheresource"}CacheResource Plugins
            {else}{$p|escape}
            {/if}
        </dt>
        <dd>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Function</th>
                        <th>Caching</th>
                        <th>Origin</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $plugins as $plugin}
                    <tr>
                        <td id="plugins-{$p|escape}-{$plugin.name|escape}">{$plugin.name|escape}</td>
                        <td>{$plugin.signature|escape}</td>
                        <td>{if $plugin.nocache}
                            <span class="string flag">NOCACHE</span>
                            {if $plugin.cache_attr}
                                {prettyprint value=$plugin.cache_attr}
                            {/if}
                        {/if}</td>
                        <td><span class="directory">{$plugin.realpath}</span> <span class="line">{$plugin.line}</span></td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </dd>
        {/foreach}
    </dl>

</section>
{/if}

{if $data.registered}
<section>
    <header>
        <h1 id="registered">Registered Elements</h1>
    </header>
    
    {*
        registered_objects
        registered_classes
        registered_filters
        registered_resources
        registered_cache_resources
    *}
    
    <dl>
        <dt id="registered-objects">Registered Objects</dt>
        <dd>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="string">foo</span></td>
                        <td><span class="classname">Foo_Bar</span></td>
                    </tr>
                </tbody>
            </table>
        </dd>
    </dl>

</section>
{/if}

{if $data.defaults}
<section>
    <header>
        <h1 id="default-variable-handling">Default Variable Handling</h1>
    </header>
    
    {*
        default_modifiers
        registered_filters [variable]
        autoload_filters [variable]
    *}
    
    <dl>
        <dt id="default-variable-handling-modifiers">Default Modifiers</dt>
        <dd>
            <table>
                <thead>
                    <tr>
                        <th>Function</th>
                        <th>Defined in</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="callable">smarty_foo</span></td>
                        <td><span class="filename">/foo/bar/foo.php</span> <span class="line">99</span></td>
                    </tr>
                </tbody>
            </table>
        </dd>
    </dl>

</section>
{/if}

{if $data.security}
<section>
    <header>
        <h1 id="security">Smarty Security</h1>
    </header>
    
    {*
        php_handling (wtf?)
        secure_dir (template_dir config_dir) -> also in {fetch}
        trusted_dir
        static_classes (registered_classes?)
        php_functions
        php_modifiers
        allowed_tags, disabled_tags
        allowed_modifiers, disabled_modifiers
        streams
        allow_constants
        allow_super_globals
        
    *}

</section>
{/if}

</body>
</html>
