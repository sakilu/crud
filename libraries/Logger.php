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
