# Notes #

---

## PHPTAL ##

* Caching is somewhat inconvenient, only within templates, with restrictions to template inheritance

---

## Twig ##

* No output cache

---

## Savant ##

* practically plain PHP, thus no convenience whatsoever.
* regular PHP errors, warnings, notices
* no caching
* no security
* reads TPLs only from file system
* No output cache


---

## RainTPL ##

### Error Handling ###

```
Parse error: syntax error, unexpected '}' in /path/foo.72140ddaa86134d7ec0ad5ad0c1b54f5.rtpl.php on line 10
```

### Variable Output ###

* no auto escaping, [mentioned](http://www.raintpl.com/Forum/Development-Forum/Rain-TPL-3/?t=151#p_517) as of v3
* modifiers `{$foo|modifier}` may take only a single argument `{$foo|modifier:1}` is ok, `{$foo|modifier:1:2}` is not
* no default modifiers (other than what php-core-functions can be used directly) supplied

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

### Cache ###

* Output-Cache for complete output
* apparently sub-templates may have individual cache-timeouts

### Security ###

* no function white-list (only black-list)
* [black_list](http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Methods/Configure/#black_list) doesn't differentiate between `{function="foo"}` and `{$foo}`.

---

# Integration #

[PHP Framework MVC Benchmark](http://www.ruilog.com/blog/view/b6f0e42cf705.html)
[PHP Frameworks](http://www.phpframeworks.com/)
[PHP frameworks comparison](http://socialcompare.com/en/comparison/php-frameworks-comparison)


## Smarty ##

* [Symfony](https://github.com/noiselabs/SmartyBundle)
* [Kohana](https://github.com/MrAnchovy/Kohana_Smarty3)
* [CodeIgniter](https://github.com/Vheissu/Plenty-Parser)
* [Zend Framework](http://www.gediminasm.org/article/smarty-3-extension-for-zend-framework)
* [Yii Framework](https://github.com/yiiext/smarty-renderer)
* [CakePHP](https://github.com/kanshin/CakeSmarty)

## Twig ##

* [Symfony](http://symfony.com/) ("native")
* [Kohana](https://github.com/jonathangeiger/kohana-twig)
* [CodeIgniter](https://github.com/Vheissu/Plenty-Parser)
* [Zend Framework](http://code.google.com/p/zwig/)
* [Yii Framework](https://github.com/yiiext/twig-renderer)
* [CakePHP](https://github.com/m3nt0r/cakephp-twig-view)


DRUPAL
http://drupal.org/project/theme%20engines
http://drupal.org/project/smarty

Wordpress
http://wordpress.org/extend/plugins/smarty-for-wordpress/
www.dylanmccall.com/blog/2011/06/17/my-new-wordpress-blog-and-smarty/
http://inchoo.net/wordpress/twig-wordpress-part2/
http://wordpress.org/extend/plugins/ap-twig-bridge/