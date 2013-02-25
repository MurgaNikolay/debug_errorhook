<?php

namespace Debug\ErrorHook;

class ErrorHookFactory
{
    public function create($options)
    {
        $errorListener = new Listener();
        foreach ($options['notifiers'] as $notifierOptions) {
            $notifier = $this->_createNotifier($notifierOptions);
            if ($notifier) {
                print 'dsfsdfsd';
                $errorListener->addNotifier($notifier);
            }
        }
        return $errorListener;
    }

    /**
     * @param $notifierOptions
     * @return null | INotifier
     */
    protected function _createNotifier($notifierOptions)
    {
        $class = $notifierOptions['class'];
        if (class_exists($class)) {
            $notifier = new $class($notifierOptions['options']);
            if (isset($notifierOptions['decorators'])) {
                $notifier = $this->_decorateNotifier($notifier, $notifierOptions['decorators']);
            }
            return $notifier;
        }
        return null;
    }

    /**
     * @param $notifier
     * @param $decorators
     * @return bool | INotifier
     */
    protected function _decorateNotifier($notifier, $decorators)
    {
        foreach ($decorators as $decorator) {
            $class = $decorator['class'];
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                if ($reflection->implementsInterface('Debug\ErrorHook\INotifier')) {
                    $notifier = new $class($notifier, $decorator['options']);
                }
            }
        }
        return $notifier;
    }

}
