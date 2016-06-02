<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>erig.net</title>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script type="text/javascript">
{literal}
<!--
/* <![CDATA[ */
    $(function() {
        $('#worldselector').change(function() {
            $('#graph').load('?a=Graph&w=' + $(this).val());
        });

        $('#worldselector').change();
    });
/* ]]> */
-->
{/literal}
  </script>
 </head>
 <body>
  <div>
   Source code: <a href="erigonline-1.1.tar.gz">erigonline-1.1.tar.gz</a>
  </div>
  <div>
   <select id="worldselector">{html_options options=$worlds selected='Total'}</select>
  </div>
  <div id="graph">
  </div>
 </body>
</html>
