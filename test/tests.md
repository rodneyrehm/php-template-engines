
* UTF-8 compatibility
* Security / Sandboxing
* Template Inheritance
* Modularity
* Template Sources (File, DB, ...)
* Output Caching (File, DB, ...) 
* Selective Output Caching
* multiple template sources (themes and stuff)
* Concurrency control (race condition mitigation)

i18n interface

{foreach}




http://webification.com/best-php-template-engines
http://www.webresourcesdepot.com/19-promising-php-template-engines/
http://www.livexp.net/technology/php/top-25-php-template-engines.html

http://www.linkedin.com/groupItem?view=&gid=42140&type=member&item=39599414&commentID=46412859#commentID_46412859


# PHP Template Engine Comparison #

Before we get started, I'd like to point out that I'm a Smarty developer. So yes, in a way I'm biased. If you feel I did your engine wrong, drop me a line or fork the github repo and fix it yourself.


Template engines don't compare too well, in my opinion. On the one hand we have libraries like Twig and Smarty, swiss army knives of templating, on the other hand we have very simple abstractions like RainTPL. 


## Why even bother? ##

I originally designed this benchmark to identify improvements and regressions along the line of Smarty development. The overall performance of Smarty3 wasn't that great and we needed to know what cases would cause the system to slow down. It's been quite enlightening for us developers and was never inteded to be published. We didn't want to start any pissing contests on "which engine is better".

After discovering [phpcomparison.net](http://phpcomparison.net) (published by Federico Ulfo, the author of [RainTPL](http://raintpl.com)) and having a somewhat closer look at the benchmarks he did (downloadable from phpcomparison.net) I decided to broaden the spectrum to other template engines. 

I hope Federico is not taking this too personally, but phpcomparison.net is wrong in every possible way. He is comparing apples to bananas. And his way of comparing is, well, very far away from any realistic implementation. I won't go into details here, just have a look at his benchmarking code and you'll understand. 

Aside from that, phpcomparison.net is only showing some very primitive scenarios. Assigning and looping some data may be the fundamental thing we do with templates engines, but heck, if it were only that, I didn't need an engine for the job.

I'm still trying to avoid any sorts of pissing contests. Authors of other engines may take these results into consideration for improvement. They also may contact me if their respective test cases could be improved by design. I'm not firm in all engines, so the tests are pretty much copypasted examples from their sites.


## Test helpers ##

```
php -r 'for($i=1; $i <= 10000; $i++) printf("<li>{\$value_$i}</li>\n");' | pbcopy
```