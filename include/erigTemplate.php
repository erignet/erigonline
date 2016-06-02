<?php

require_once 'Smarty.class.php';

class erigTemplate {
    public static $tp;
}

erigTemplate::$tp = new Smarty();
erigTemplate::$tp->template_dir = dirname(__FILE__) . '/../templates';
erigTemplate::$tp->compile_dir  = dirname(__FILE__) . '/../templates_c';

?>
