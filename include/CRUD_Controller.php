<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once BASEPATH.'core/Model.php';
require_once FCPATH . 'crud/include/CRUD_Model.php';


class CRUD_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('layout');
        $this->load->library('crud');
        $this->load->library('form');
        $this->load->library('auth');
        $this->load->library('sidebar');

        $this->_get_crud()->set_controller($this);
        if ($this->_get_crud()->get_module_name() !== 'login' && !$this->_get_auth()->check()) {
            $this->_get_auth()->redirect_login_page();
        }
        setcookie("a", 1, time() + 3600, '/'); // for ckfinder
        $this->_get_crud()->set(Crud::KEY_TABLE, strtolower(get_class($this)));
    }

    /**
     * @return CRUD_Model_Interface
     */
    public function get_model()
    {
        $config = $this->config->item('crud');
        $model_name = get_class($this) . '_model';
        $model_path = $config['prefix'] . $model_name;
        $this->load->model($model_path);
        return $this->$model_name;
    }

    /**
     * @param $id
     */
    public function ajax_remove($id)
    {
        $table = $this->_get_crud()->get(Crud::KEY_TABLE);
        if ($this->db->field_exists($table . '_trash', $table)) {
            $this->db->where($this->_get_crud()->get_primary_key_column_name(), $id);
            $this->db->update($table, [$table . '_trash' => 1]);
            if ($this->db->field_exists($table . '_updated_by', $table)) {
                $this->db->where($this->_get_crud()->get_primary_key_column_name(), $id);
                $this->db->update($table, [$table . '_updated_by' => json_encode($this->_get_auth())]);
            }
            return;
        }
        $this->_get_crud()->single_delete($table, $id);
        $this->crud_model->file_dlt($table, $id);
    }

    public function state_list_save()
    {
        $this->_get_crud()->state_list_save();
    }

    public function state_list_load()
    {
        echo json_encode($this->_get_crud()->state_list_load());
    }

    public function is_logged()
    {
        echo $this->_get_auth()->get_id() > 0 ? 'yah!good' : 'nope!bad';
    }

    /**
     * @return Logger
     */
    public function _get_logger()
    {
        return $this->logger;
    }

    public function logout()
    {
        $this->_get_auth()->logout();
    }

    /**
     * @return Sidebar
     */
    protected function _get_sidebar()
    {
        return $this->sidebar;
    }

    /**
     * @return AbstractAuth
     */
    protected function _get_auth()
    {
        return $this->auth;
    }

    /**
     * @return Crud
     */
    protected function _get_crud()
    {
        return $this->crud;
    }

    /**
     * @return Layout
     */
    protected function _get_layout()
    {
        return $this->layout;
    }

    /**
     * @return Form
     */
    protected function _get_form()
    {
        return $this->form;
    }

}
