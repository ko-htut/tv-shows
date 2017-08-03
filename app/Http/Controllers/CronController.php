<?php

namespace App\Http\Controllers;

class CronController extends LayoutController {

    public function __construct() {
        parent::__construct();
    }

    public function import() {
       
        if (stripos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
            ini_set('max_execution_time', 60 * 60 * 24);
            set_time_limit(0);
            ini_set('default_socket_timeout', 5 * 60);

            $url = 'http://www.serialovna.cz/import';
            $times = 30;
            $i = 0;

            $start = microtime(true);

            $opts = array('http' => array('header' => "User-Agent:CronImport/1.0\r\n"));
            $context = stream_context_create($opts);
            while ($i < $times) {
                file_get_contents($url, false, $context);
                sleep(3 * 60);
                $i++;
            }
            $end = microtime(true);
            $elapsed_time = $end - $start;
            die('Cron ended at: ' . date('y-m-d H:i:s', $end) . ' elapsed time:' . ($elapsed_time));
        } else {
            die('Cron import at ' . $_SERVER['SERVER_NAME']);
        }
    }

}
