# PHP Template Engines Comparison #

»There are three kinds of lies: Lies, damned lies, and benchmarks«

Please note that this is a premature publication. You won't get anything off this as of now…

## Running Tests ##

modify constant HTTP_BASE_PATH in test/run.php to match the URL this repo is accessible on. Then run the following commands in your shell:

	php test/run.php
	php render/render.php 
	
Results are written to the htdocs directory


http://coding.smashingmagazine.com/2011/10/17/getting-started-with-php-templating/

## TODO ##

add ab tests `/usr/sbin/ab -n 5000 -c 100 'http://test.dev/php-template-engines/test/invoke.php?distribution=smarty-3.1.8&test=Page&factor=1&method=evaluate&output=1'`