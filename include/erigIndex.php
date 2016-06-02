<?php

require_once 'erigTemplate.php';
require_once 'erigOnline.php';

class erigIndex {
    private $tp;

    function __construct($tp) {
        $this->tp = $tp;
    }

    function Handle($r) {
        try {
            $method = @$r['a'];

            if (!$method)
                $method = 'Index';

            $method = 'Handle' . $method;

            if (!method_exists($this, $method))
                throw new Exception('Method does not exist: ' . $method);

            $this->$method($r);
        } catch (Exception $e) {
            // insert fancy error handling here
            echo htmlspecialchars($e->getMessage());
        }
    }

    function HandleIndex($r) {
        $worlds = $this->GetWorlds();
        $this->tp->assign('worlds', $worlds);
        $this->tp->display('index.tpl');
    }

    function HandleGraph($r) {
        $world = @$r['w'];

        if (!$world)
            throw new Exception('World required');

        $worlds = $this->GetWorlds();

        if (!isset($worlds[$world]))
            throw new Exception('Invalid world');

        $this->tp->assign('world', $world);
        $this->tp->display('index-graph.tpl');
    }

    function GetWorlds() {
        $worlds = glob(erigOnline::$graphpath . '/*-day.png');

        $ret = array();
        $ret['Total'] = 'Total';

        foreach ($worlds as $world) {
            $world = basename($world);
            if (preg_match('@^(.+?)-day\.png$@', $world, $m))
                $ret[$m[1]] = $m[1];
        }

        return $ret;
    }
}

?>
