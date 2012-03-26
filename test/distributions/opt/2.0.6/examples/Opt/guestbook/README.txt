
  OPEN POWER TEMPLATE 2 EXAMPLES
  ==============================

This is a sample guestbook for Open Power Template. It illustrates some concepts
of working with this template engine. The code is commented, so you may read it
in order to get to know, how to use some of the template engine features.

The illustrated features are:

 - Basic API example
 - Template inheritance
 - Components and form building
 - Data formats
 - Sections

INSTALLATION
============

1. Rename "config.sample.php" to "config.php"
2. Configure the database connection settings in "config.php"
3. Create "paths.ini" file in the "/examples" directory:

~~~~
directory = "./"

[libraries]
Opl = "/yourpath/Opl/lib/"
Opt = "/yourpath/Opt/lib/"
~~~~

And configure the paths to the libraries.

4. Create the database and import "database.sql" to it.
5. Run the example.