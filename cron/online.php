<?php

require_once dirname(__FILE__) . '/../include/erigOnline.php';

$data = erigOnline::get();
erigOnline::writeRRDs($data);
erigOnline::createGraphs($data);

?>
