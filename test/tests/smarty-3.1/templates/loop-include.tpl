<ul>
  {foreach $values as $key => $value}
    {include file="loop-include.child.tpl" key=$key value=$value}
  {/foreach}
</ul>