<ul>
  {foreach from=$values item="key" key="value"}
    {include file="loop-include.child.tpl" key=$key value=$value}
  {/foreach}
</ul>