{extends "framework.tpl"}

{block "construct" append}

{/block}

{block "content"}
    
    <ul>
        <li><a href="tests.html">Tests</a></li>
        {foreach $distributions as $key => $versions}
            {foreach $versions as $version => $t}{strip}
                {if $t@first}
                    <li>
                        <a href="distribution/{$key|escape}/index.html">{$key|escape}</a>
                        <ul>
                {/if}
                            <li><a href="distribution/{$key|escape}/{$version|escape}.html">({$version|escape})</a></li>
                {if $t@last}
                        </ul>
                    </li>
                {/if}
            {/strip}{/foreach}
        {/foreach}
{/block}