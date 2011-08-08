{extends "framework.tpl"}

{block "construct" append}

{/block}

{block "content"}

    {foreach $tests as $test}
        <div class="chart test-chart" 
            data-series="{$test.data.duration|json_encode|escape}" 
            data-title="{$test.test|escape} Test" 
            data-subtitle="Duration"
            data-axis-name="Duration"
            data-axis-abbr="s"
        ></div>
        <div class="chart test-chart" 
            data-series="{$test.data.memory|json_encode|escape}" 
            data-title="{$test.test|escape} Test" 
            data-subtitle="Memory consumption"
            data-axis-name="Memory"
            data-axis-abbr="MB"
        ></div>
    {/foreach}
{/block}