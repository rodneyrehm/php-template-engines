{extends "framework.tpl"}

{block "construct" append}

{/block}

{block "content"}

    {foreach $tests as $test}
        <div class="chart distribution-test-chart" 
            data-series="{$test.data|json_encode|escape}" 
            data-title="{$test.test|escape} Test" 
            data-subtitle="{$test.distribution|escape}"
        ></div>
    {/foreach}

{/block}