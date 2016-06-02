<?php

require_once dirname(__FILE__) . '/../include/erigIndex.php';

$e = new erigIndex(erigTemplate::$tp);
$e->Handle($_REQUEST);

?>
