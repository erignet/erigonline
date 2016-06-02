This software requires:

PHP 5.2+ (php.net)
Smarty (smarty.net)
RRDTool (oss.oetiker.ch/rrdtool)

Directory structure:

cron        - run programs in here once every 5 minutes
include     - PHP classes for including
templates   - smarty templates for web
templates_c - automatically compiled smarty templates, must be writable by the webserver user
web         - the docroot, point your Apache/etc. here

You must create the following directory:

/var/db/erig/rrds

It contains the Round Robin Databases (RRDs), if you'd like to change this location, modify include/erigOnline.php

Some other settings are configurable in include/erigOnline.php as well.

Best of luck!

Erig
