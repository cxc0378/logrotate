<?php


namespace Primo;

class LogRotate
{
    private $maxFileNum; //Maximum number of log files
    private $maxFileSize;//Maximum size in MB of log files
    private $maxDays;//Maximum saved days
    private $logPath;//Save directory of log files
    private $logFile;//Log file name without suffix. The default suffix is ".log"


    private static $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * init
     * @Param $maxNum int
     * @Param $maxSize int
     * @Param $logPath string
     * @Param $file string
     * @Param $maxDays int
     * @Return object
     */
    public function init($maxNum, $maxSize, $logPath, $file, $maxDays = 30)
    {
        $maxNum = intval($maxNum);
        $maxSize = intval($maxSize);
        $maxDays = intval($maxDays);
        !is_dir($logPath) && mkdir($logPath, 0777, true);
        if ($maxNum <= 0 || $maxSize <= 0 || $maxDays <= 0 || !is_dir($logPath)) {
            return false;
        }
        $this->maxFileNum = $maxNum;
        $this->maxFileSize = $maxSize * 1000 * 1000;
        $this->logPath = $logPath;
        $this->logFile = $file;
        $this->maxDays = $maxDays;

        return $this;
    }

    /**
     * Format time by microtime()
     */
    public function formatTime()
    {
        $ustime = explode(" ", microtime());
        return "[" . date('Y-m-d H:i:s', time()) . "." . ($ustime[0] * 1000) . "]";
    }


    /**
     * Record log files by rotation
     */
    public function rotate($msg)
    {
        clearstatcache();
        $this->clear();
        $path = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile . ".log";
        if (file_exists($path)) {
            if (filesize($path) >= $this->maxFileSize) {
                $index = 1;
                //Get the maximum number of rotation logs
                for (; $index < $this->maxFileNum; $index++) {
                    if (!file_exists($this->logPath . DIRECTORY_SEPARATOR . $this->logFile . "_" . $index . ".log")) {
                        break;
                    }
                }
                //Maxfilenum log files already exist
                if ($index == $this->maxFileNum) {
                    $index--;
                }
                //Rotate log
                for (; $index > 1; $index--) {
                    $new = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile . "_" . $index . ".log";
                    $old = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile . "_" . ($index - 1) . ".log";
                    rename($old, $new);
                }

                $newFile = $this->logPath . DIRECTORY_SEPARATOR . $this->logFile . "_1.log";
                rename($path, $newFile);
            }
        }
        $fp = fopen($path, "a+b");
        fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        return true;
    }

    /**
     *  Clear expired log files
     */
    public function clear()
    {
        $pattern = $this->logPath . DIRECTORY_SEPARATOR . "*.log";
        foreach (glob($pattern) as $fn) {
            if (filectime($fn) < strtotime('-' . $this->maxDays . ' days')) {
                unlink($fn);
            }
        }
    }

    public function debug($msg)
    {
        $this->rotate($this->formatTime() . "[DEBUG]: ${msg}" . PHP_EOL);
    }


    public function info($msg)
    {
        $this->rotate($this->formatTime() . "[INFO]: ${msg}" . PHP_EOL);
    }

    public function error($msg)
    {
        $this->rotate($this->formatTime() . "[ERROR]: ${msg}" . PHP_EOL);
    }

}


