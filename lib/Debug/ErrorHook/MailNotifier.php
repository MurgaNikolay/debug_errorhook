<?php

namespace Debug\ErrorHook;
/**
 * Sends all notifications to a specified email.
 *
 * Consider using this class together with RemoveDupsWrapper
 * to avoid mail server flooding when a lot of errors arrives.
 */

class MailNotifier extends TextNotifier
{
    protected $_to;
    protected $_from;
    protected $_charset = "UTF-8";
    protected $_whatToSend;
    protected $_subjPrefix = "[ERROR]";

    public function __construct($options = [])
    {
        parent::__construct($options);
    }

    protected function _notifyText($subject, $body)
    {
        foreach($this->_to as $to) {
            $this->_mail(
                $to,
                $this->_encodeMailHeader($this->_subjPrefix . $subject),
                $body,
                join("\r\n", array(
                    "From: {$this->_from}",
                    "Content-Type: text/plain; charset={$this->_charset}"
                ))
            );
        }
    }

    protected function _mail()
    {
        $args = func_get_args();
        @call_user_func_array("mail", $args);
    }

    private function _encodeMailHeader($header)
    {
        return preg_replace_callback(
            '/((?:^|>)\s*)([^<>]*?[^\w\s.][^<>]*?)(\s*(?:<|$))/s',
            array(__CLASS__, '_encodeMailHeaderCallback'),
            $header
        );
    }

    private function _encodeMailHeaderCallback($p)
    {
        $encoding = $this->_charset;
        return $p[1] . "=?$encoding?B?" . base64_encode($p[2]) . "?=" . $p[3];
    }

    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }

    public function getCharset()
    {
        return $this->_charset;
    }

    public function setSubjPrefix($subjPrefix)
    {
        $this->_subjPrefix = $subjPrefix;
    }

    public function getSubjPrefix()
    {
        return $this->_subjPrefix;
    }

    public function setWhatToSend($whatToSend)
    {
        $this->_whatToSend = $whatToSend;
    }

    public function getWhatToSend()
    {
        return $this->_whatToSend;
    }

    public function setTo($to)
    {
        $this->_to = (array) $to;
    }

    public function getTo()
    {
        return $this->_to;
    }

    public function setFrom($from)
    {
        $this->_from = $from;
    }
}
