<?php

class Logger {
    
    private $file = null;

    public function __construct($file) {
        $this->file = fopen($file, "a");
    }

    public function __destruct() {
        if ($this->file) fclose($this->file);
    }

    public function trace($message) {
        $this->log('TRACE', $message);
    }

    public function info($message) {
        $this->log('INFO', $message);
    }

    public function warn($message) {
        $this->log('WARN', $message);
    }

    public function error($message) {
        $this->log('ERROR', $message);
    }

    private function log($level, $message) {
        $str = $level . " [" . date("Y/m/d h:i:s", time()) . "] " . $message . "\n";
        fwrite($this->file, $str);
    }
}