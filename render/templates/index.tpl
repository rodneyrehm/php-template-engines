{extends "framework.tpl"}

{block "construct" append}

{/block}

{block "content"}
    
    <ul>
    <li><a href="tests.html">Tests</a>
    {foreach $distributions as $key => $active}
        <li><a href="{$key|escape}.html">{$key|escape}</a>
    {/foreach}
    </li>
{/block}