<?php

namespace Debug\ErrorHook;
/**
 * Generic notifier wrapper. Converts notification
 * to a human-readable text representation.
 */

abstract class TextNotifier implements INotifier
{
    use OptionsAware;

    const LOG_SERVER = 1;
    const LOG_TRACE = 2;
    const LOG_COOKIE = 4;
    const LOG_GET = 8;
    const LOG_POST = 16;
    const LOG_SESSION = 32;
    const LOG_ALL = 65535;

    private $_whatToLog;

    private $_bodySuffix;

    public function __construct($options)
    {
        $this->setOptions($options);
    }

    public function setBodySuffixTest($text)
    {
        $this->_bodySuffix = $text;
    }

    public function notify($errNo, $errStr, $errFile, $errLine, $trace)
    {
        $body = array();
        $body[] = $this->_makeSection(
            "",
            join("\n", array(
                (@$_SERVER['GATEWAY_INTERFACE'] ? "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : ""),
                "$errNo: $errStr",
                "at $errFile on line $errLine",
            ))
        );
        if ($this->_whatToLog & self::LOG_TRACE && $trace) {
            $body[] = $this->_makeSection("TRACE", Util::backtraceToString($trace));
        }
        if ($this->_whatToLog & self::LOG_SERVER) {
            $body[] = $this->_makeSection("SERVER", Util::varExport($_SERVER));
        }
        if ($this->_whatToLog & self::LOG_COOKIE) {
            $body[] = $this->_makeSection("COOKIES", Util::varExport($_COOKIE));
        }
        if ($this->_whatToLog & self::LOG_GET) {
            $body[] = $this->_makeSection("GET", Util::varExport($_GET));
        }
        if ($this->_whatToLog & self::LOG_POST) {
            $body[] = $this->_makeSection("POST", Util::varExport($_POST));
        }
        if ($this->_whatToLog & self::LOG_SESSION) {
            $body[] = $this->_makeSection("SESSION", Util::varExport(@$_SESSION));
        }
        // Append body suffix?
        $suffix = $this->_bodySuffix && is_callable($this->_bodySuffix) ? call_user_func($this->_bodySuffix) : $this->_bodySuffix;
        if ($suffix) {
            $body[] = $this->_makeSection("ADDITIONAL INFO", $suffix);
        }
        // Remain only 1st line for subject.
        $errStr = preg_replace("/\r?\n.*/s", '', $errStr);
        $this->_notifyText("$errNo: $errStr at $errFile on line $errLine", join("\n", $body));
    }

    private function _makeSection($name, $body)
    {
        $body = rtrim($body);
        if ($name) $body = preg_replace('/^/m', '    ', $body);
        $body = preg_replace('/^([ \t\r]*\n)+/s', '', $body);
        return ($name ? $name . ":\n" : "") . $body . "\n";
    }

    public function setWhatToLog($whatToLog)
    {
        $this->_whatToLog = $whatToLog;
    }

    public function getWhatToLog()
    {
        return $this->_whatToLog;
    }

    abstract protected function _notifyText($subject, $body);
}
