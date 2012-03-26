{extends "framework.tpl"}

{block "construct" append}

{/block}

{block "content"}
    
    <ul>
    <li><a href="tests.html">Tests</a>
    {foreach $distributions as $key => $versions}
        {foreach $versions as $version => $t}
            <li><a href="{$key|escape}-{$version|escape}.html">{$key|escape} ({$version|escape})</a>
        {/foreach}
    {/foreach}
    </li>
{/block}