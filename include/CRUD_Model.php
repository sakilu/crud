<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class CRUD_Model_Interface extends CI_Model
{
    protected $_table = '';

    protected $_primary_key;

    public function __construct()
    {
        parent::__construct();
        $this->_primary_key = $this->_table . '_id';

    }

    abstract public function validate($action = '', $id = null);

    abstract public function get_error();


    public function insert($data)
    {
        if ($this->db->field_exists($this->_table . '_created_by', $this->_table)) {
            $data[$this->_table . '_created_by'] = json_encode($this->auth, JSON_UNESCAPED_UNICODE);
        }
        $this->db->insert($this->_table, $data);
        return $this->db->insert_id();
    }

    public function update($data, $id)
    {
        if ($this->db->field_exists($this->_table . '_updated_by', $this->_table)) {
            $data[$this->_table . '_updated_by'] = json_encode($this->auth, JSON_UNESCAPED_UNICODE);
        }
        $this->db->where($this->_primary_key, $id);
        $this->db->update($this->_table, $data);
        return $this;
    }

    public function get($id)
    {
        return $this->db->get_where($this->_table, [$this->_primary_key => $id])->row();
    }
}
