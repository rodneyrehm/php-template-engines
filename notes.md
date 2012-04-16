# Notes #


## RainTPL ##

### Error Handling ###

```
Parse error: syntax error, unexpected '}' in /path/foo.72140ddaa86134d7ec0ad5ad0c1b54f5.rtpl.php on line 10
```

### Variable Output ###

* no auto escaping, [mentioned](http://www.raintpl.com/Forum/Development-Forum/Rain-TPL-3/?t=151#p_517) as of v3
* modifiers `{$foo|modifier}` may take only a single argument `{$foo|modifier:1}` is ok, `{$foo|modifier:1:2}` is not

### Iterating Arrays ###

* overwriting variables key, value, counter
* Missing convenience $foo.[first|last|total|…]
* nested loops have no access to outer loop's data (unless mapped explicitly)
* only loops arrays (no ArrayObject, ArrayAccess, Iterator, …)

### Template Structure ###

* `{include}` does not accept variables to set for the included template
* no template inheritance

### Files ###

* can read template source only from file system
* can cache only on file system
* has no *race condition protection* whatsoever

### Security ###

* no function white-list (only black-list)
* [black_list](http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Methods/Configure/#black_list) doesn't differentiate between `{function="foo"}` and `{$foo}`.
