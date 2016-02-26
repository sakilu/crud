<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Monolog\Handler\ChromePHPHandler;

class Logger
{
    use Container;

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
        $this->logger = new \Monolog\Logger('logger');
        $this->logger->pushHandler(new ChromePHPHandler());
    }

    public function addDebug($message, $context)
    {
        if (!is_array($context)) $context = (array)$context;
        $this->logger->addDebug($message, $context);
    }
}

trait Container
{
    protected $_data;

    protected $_config;

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
        if (!$this->layout->is_ajax_request()) {
            $this->logger->addDebug(get_class($this) . ' Init', $this->_config);
        }
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