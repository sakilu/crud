<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Layout
{

    use Container;

    public $disable_toolbar = false;

    public $disable_add = false;

    public $disable_excel = false;

    public $title = '';

    public $content_path = '';


    /**
     * @var Crud
     */
    protected $_crud;

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
    }

    public function is_ajax_request()
    {
        return $this->input->is_ajax_request();
    }

    public function get_content_id()
    {
        return $this->crud->get_module_name() . '_content';
    }


    public function view($path, $array = [])
    {
        $this->content_path = $path;
        $this->load->view('crud/layout/default', $array);
    }

}