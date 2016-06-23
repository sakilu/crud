<?php
/**
 * Created by PhpStorm.
 * User: sakilu
 * Date: 16/4/21
 * Time: 上午10:06
 */
trait Container
{
    protected $_data;

    protected $_config;

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
    }

    public function config($k)
    {
        return isset($this->_config[$k]) ? $this->_config[$k] : false;
    }

    public function set_config($k, $v)
    {
        $this->_config[$k] = $v;
    }

    public function set($k, $v)
    {
        $this->_data[$k] = $v;
        return $this;
    }

    public function get($k)
    {
        return isset($this->_data[$k]) ? $this->_data[$k] : null;
    }

    public function __set($k, $v)
    {
        $this->_data[$k] = $v;
    }

    public function __get($k)
    {
        if (isset($this->_data[$k])) {
            return $this->_data[$k];
        }
        return get_instance()->{$k};
    }

    public function __isset($k)
    {
        return isset($this->_data[$k]);
    }
}