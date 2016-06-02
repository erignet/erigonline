<?php

class erigOnline {
    const VERSION = 1;
    const URL = 'http://www.tibia.com/community/?subtopic=worlds';
    const INTERVAL = 300;

    public static $rrdtool_bin = '/usr/local/bin/rrdtool';
    public static $rrdpath = '/var/db/erig/rrds';
    public static $graphpath = null; // dynamically set after class definition

    static function createStreamContext() {
        $opts = array();
        $opts['http'] = array();
        $opts['http']['user_agent'] = __CLASS__ . ' ' . self::VERSION;

        return stream_context_create($opts);
    }

    static function handleError($errno, $errstr, $errfile, $errline) {
        throw new Exception("Error #$errno in $errfile($errline): $errstr");
    }

    static function isValidWorldName($name) {
        return preg_match('@^[A-Z][a-z]{0,20}$@', $name);
    }

    static function handleValidWorldName($name) {
        $ret = self::isValidWorldName($name);

        if (!$ret)
            throw new Exception("Invalid world name: $name $ret");
    }

    static function get() {
        $context = self::createStreamContext();

        set_error_handler(array(__CLASS__, 'handleError'));
        $ret = file_get_contents(self::URL, false, $context);
        restore_error_handler();

        if ($ret == '')
            throw new Exception('Output is empty');

// <tr class="Even" ><td><a href="http://www.tibia.com/community/?subtopic=worlds&world=Furora" >Furora</a></td><td>86</td><td>Europe</td><td>Open PvP</td></tr>
        if (!preg_match_all('@<tr.*?>.*?<td><a href=.*?subtopic=worlds&world=.*?>(.+?)</a></td><td>(\d+?)</td>@i', $ret, $m))
            throw new Exception('Unable to parse website output');

        $worlds = array_values($m[1]);
        $online = array_values($m[2]);

        if (count($worlds) < 20)
            throw new Exception('Not enough worlds');

        if (count($worlds) > 1000)
            throw new Exception('Too many worlds');

        array_walk($worlds, array(__CLASS__, 'handleValidWorldName'));

        return array_combine($worlds, $online);
    }

    static function checkRRDBinary() {
        if (!is_file(self::$rrdtool_bin))
            throw new Exception('Cannot find: ' . self::$rrdtool_bin);
    }

    static function createRRD($filename) {
        $cmd = sprintf('%s create %s'
        . ' DS:online:GAUGE:%s:0:U'
        . ' RRA:AVERAGE:0.5:1:360'
        . ' RRA:AVERAGE:0.5:4:360'
        . ' RRA:AVERAGE:0.5:16:360'
        . ' RRA:AVERAGE:0.5:64:360'
        . ' RRA:MIN:0.5:1:360'
        . ' RRA:MIN:0.5:4:360'
        . ' RRA:MIN:0.5:16:360'
        . ' RRA:MIN:0.5:64:360'
        . ' RRA:MAX:0.5:1:360'
        . ' RRA:MAX:0.5:4:360'
        . ' RRA:MAX:0.5:16:360'
        . ' RRA:MAX:0.5:64:360'
        . ' 2>&1'
        , self::$rrdtool_bin, escapeshellarg($filename), self::INTERVAL);

        $ret = 0;
        $lastline = exec($cmd, $out = array(), $ret);

        if ($ret)
            throw new Exception($lastline);
    }

    static function writeRRD($world, $online) {
        if (!is_dir(self::$rrdpath))
            throw new Exception('Not a directory: ' . self::$rrdpath);

        $filename = sprintf('%s/%s.%s.rrd', self::$rrdpath, __CLASS__, $world);

        if (!file_exists($filename)) {
            self::createRRD($filename);
        }

        $time = time();
        if ($time % 300 > 0)
            $time -= $time % 300;

        $cmd = sprintf('%s update %s %d:%d 2>&1', self::$rrdtool_bin, escapeshellarg($filename), $time, $online);

        $ret = 0;
        $lastline = exec($cmd, $out = array(), $ret);

        if ($ret)
            throw new Exception($lastline);
    }

    static function writeRRDs($data) {
        self::checkRRDBinary();

        foreach ($data as $world => $online) {
            self::writeRRD($world, $online);
        }

        self::writeRRD('Total', array_sum($data));
    }

    static function createGraphs($data) {
        $data['Total'] = 0;

        $time = time();

        $graphs = array(
            'day'   => 60*60*24,
            'week'  => 60*60*24*7,
            'month' => 60*60*24*31,
            'year'  => 60*60*24*365,
        );

        foreach ($data as $world => $ignore) {
            foreach ($graphs as $intervalname => $interval) {
                $cmd = sprintf('%s graph %s-%s.png --title "Players Online - %s - %s" --slope-mode --start %d --end %d DEF:online=%s/%s.%s.rrd:online:AVERAGE LINE1:online#FF0000',
                    self::$rrdtool_bin,
                    self::$graphpath . '/' . $world,
                    $intervalname,
                    $world,
                    ucfirst($intervalname),
                    $time - $interval,
                    $time + 60,
                    self::$rrdpath,
                    __CLASS__,
                    $world
                );

                $ret = 0;
                $lastline = exec($cmd, $out = array(), $ret);

                if ($ret)
                    throw new Exception($lastline);
            }
        }
    }
}

erigOnline::$graphpath = dirname(__FILE__) . '/../web';

?>
