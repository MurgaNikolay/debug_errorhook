<?php

namespace Debug\ErrorHook;
/**
 * Wrapper which denies duplicated notifications to be
 * processed again and again. It is needed to lower the
 * traffic to mail server in case the site is down.
 *
 * This class stores meta-information in filesystem.
 * It takes care about garbage collecting.
 */

class RemoveDupsWrapper implements INotifier
{
    use OptionsAware;

    const DEFAULT_PERIOD = 300;

    const ERROR_FILE_SUFFIX = ".error";

    const GC_PROBABILITY = 0.01;

    /**
     * @var INotifier
     */
    private $_notifier;

    /**
     * @var string
     */
    private $_tmpPath;

    /**
     * @var int
     */
    private $_period = self::DEFAULT_PERIOD;

    /**
     * @var bool
     */
    private $_gcExecuted = false;

    public function __construct(INotifier $notifier, $options = [])
    {
        //Set defaults
        $this->setPeriod(self::DEFAULT_PERIOD);
        $this->setTmpPath($this->_getDefaultTmpPath());

        $this->setNotifier($notifier);
        $this->setOptions($options);
    }

    public function notify($errNo, $errStr, $errFile, $errLine, $trace)
    {
        $hash = md5(join(":", array($errNo, $errFile, $errLine)));
        if ($this->_isExpired($hash)) {
            $this->_notifier->notify($errNo, $errStr, $errFile, $errLine, $trace);
        }
        // Touch always, even if we did not send anything. Else same errors will
        // be mailed again and again after $period (e.g. once per 5 minutes).
        $this->_touch($hash, $errFile, $errLine);
    }

    protected function _getDefaultTmpPath()
    {
        return sys_get_temp_dir() . "/" . get_class($this);
    }

    protected function _getGcProbability()
    {
        return self::GC_PROBABILITY;
    }

    private function _getLockFname($hash)
    {
        return $this->_tmpPath . '/' . $hash . self::ERROR_FILE_SUFFIX;
    }

    private function _isExpired($hash)
    {
        $file = $this->_getLockFname($hash);
        return !file_exists($file) || filemtime($file) < time() - $this->_period;
    }

    private function _touch($hash, $errFile, $errLine)
    {
        $file = $this->_getLockFname($hash);
        file_put_contents($file, "$errFile:$errLine");
        @chmod($file, 0666);
        $this->_gc();
    }

    private function _gc()
    {
        if ($this->_gcExecuted || mt_rand(0, 10000) >= $this->_getGcProbability() * 10000) {
            return;
        }
        foreach (glob("{$this->_tmpPath}/*" . self::ERROR_FILE_SUFFIX) as $file) {
            if (filemtime($file) <= time() - $this->_period * 2) {
                @unlink($file);
            }
        }
        $this->_gcExecuted = true;
    }

    public function setNotifier($notifier)
    {
        $this->_notifier = $notifier;
    }

    public function getNotifier()
    {
        return $this->_notifier;
    }

    public function setPeriod($period)
    {
        $this->_period = $period ? $period : self::DEFAULT_PERIOD;
    }

    public function getPeriod()
    {
        return $this->_period;
    }

    public function setTmpPath($tmpPath)
    {
        $this->_tmpPath = $tmpPath ? $tmpPath : $this->_getDefaultTmpPath();
        if (!@is_dir($this->_tmpPath)) {
            if (!@mkdir($this->_tmpPath, 0777, true)) {
                $error = error_get_last();
                throw new Exception("Cannot create '{$this->_tmpPath}': {$error['message']}");
            }
        }
    }

    public function getTmpPath()
    {
        return $this->_tmpPath;
    }
}
