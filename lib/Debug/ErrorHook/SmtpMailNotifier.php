<?php
namespace Debug\ErrorHook;


class SmtpMailNotifier extends MailNotifier
{
    /**
     * @var Smtp;
     */
    protected $smtp;

    protected $smtpOptions;

    public function __construct($options = [])
    {
        if (isset($options['smtp'])) {
            $this->setSmtpOptions($options['smtp']);
            unset($options['smtp']);
        }
        parent::__construct($options);
    }

    public function getTransport() {
        if (!$this->smtp) {
            $this->smtp = new Smtp($this->getSmtpOptions());
        }

        return $this->smtp;
    }


    protected function _notifyText($subject, $body)
    {
        $smtp = $this->getTransport();
        $smtp->setText($body);
        $smtp->setCharset($this->_charset);
        $smtp->setSubject($this->_subjPrefix.$subject);
        $smtp->sendText();
    }

    public function setSmtpOptions($smtpOptions)
    {
        $this->smtpOptions = $smtpOptions;
    }

    public function getSmtpOptions()
    {
        return $this->smtpOptions;
    }

    public function setTo($to) {
        parent::setTo($to);
        $this->getTransport()->setTo((array) $to);
    }

    public function setFrom($from) {
        parent::setFrom($from);
        $this->getTransport()->setFrom($from);
    }
}