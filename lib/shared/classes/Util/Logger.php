<?php
namespace Util;

/**
 * Simple logging utility class for logging output to a file.
 */
class Logger {
    private $file = null;
    private $level = 0;
    private $levels = array('trace', 'info', 'warn', 'error');

    /**
     * Constructs a new Logger.
     *
     * @param string $file the filepath of the output file to log to
     * @param string $level the level at which to initialize the logger.
     * @throws \Exception if a null file is provided or an error occurs while opening the file
     */
    public function __construct($file, $level = 'info') {
        if ($file == null) {
            throw new \Exception('Failed to create new logger: filename required');
        }
        $this->setLevel($level);
        $this->file = fopen($file, 'a');
        if (!$this->file) {
            throw new \Exception('Failed to create new logger: failed to open log file to append entries to');
        }
    }

    /**
     * Destroys the instance and closes the file handler on the log file it is using.
     */
    public function __destruct() {
        if ($this->file) {
            fclose($this->file);
        }
    }

    /**
     * Adjusts the level of the logger.
     * 
     * Valid string levels are `trace`, `info`, `warn`, and `error`. If an invalid level is provided and the level
     * has not previously been set, then the level will be set to `info`. Otherwise the invalid level provided will be
     * discarded and the level will not change.
     * will 
     *
     * @param string $level the level to set the logger to
     * @return boolean true if the value is changed successfully, false otherwise
     */
    public function setLevel($level) {
        $i = array_search($level, $this->levels);
        if (!$i) {
            if ($this->level == null) {
                $this->level = 1;
            }

            return false;
        } else {
            $this->level = $i;
        }

        return true;
    }

    /**
     * Logs a message at the `trace` level.
     *
     * @param string $message the message to log
     * @return void
     */
    public function trace($message) {
        if ($this->level <= 0) {
            $this->log('TRACE', $message);
        }
    }

    /**
     * Logs a message at the `info` level.
     *
     * @param string $message the message to log
     * @return void
     */
    public function info($message) {
        if ($this->level <= 1) {
            $this->log('INFO', $message);
        }
    }

    /**
     * Logs a message at the `warn` level.
     *
     * @param string $message the message to log
     * @return void
     */
    public function warn($message) {
        if ($this->level <= 2) {
            $this->log('WARN', $message);
        }
    }

    /**
     * Logs a message at the `error` level.
     *
     * @param string $message the message to log
     * @return void
     */
    public function error($message) {
        if ($this->level <= 3) {
            $this->log('ERROR', $message);
        }
    }

    /**
     * Writes the log message to the output file. The output will include a timestamp.
     *
     * @param string $level the level of the message
     * @param string $message the message to log
     * @return void
     */
    private function log($level, $message) {
        $str = $level . ' [' . date('Y/m/d h:i:s', time()) . '] ' . $message . "\n";
        fwrite($this->file, $str);
    }
}
