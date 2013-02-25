<?php

namespace Debug\ErrorHook;

trait OptionsAware {
    public function setOptions($options)
    {
        foreach($options as $key=>$value) {
            $this->setOption($key, $value);
        }
    }

    public function setOption($key, $value) {
        $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        if (!method_exists($this, $setter)) {
            return;
        }
        $this->{$setter}($value);
    }

    public function getOption($key)
    {
        $getter = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        if (!method_exists($this, $getter)) {
            return false;
        }
        return $this->{$getter}();
    }
}
