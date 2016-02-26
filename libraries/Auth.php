<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class AbstractAuth implements JsonSerializable
{
    /**
     * @var Crud
     */
    protected $_crud;

    /**
     * @param Crud $crud
     */
    public function set_crud($crud)
    {
        $this->_crud = $crud;
    }

    /**
     * @return bool
     */
    abstract public function login($account, $password);

    abstract public function logout();

    /**
     * @param string $account
     * @param string $password
     * @return bool
     */
    abstract public function check();

    abstract public function redirect_login_page();

    abstract public function redirect_unauthorized_page();

    abstract public function get();

    abstract public function get_name();

    abstract public function get_id();

    abstract public function get_roles();

    abstract public function google_login($object);

    abstract public function fb_login($object);

}

class Auth extends AbstractAuth
{

    use Container;

    protected $_user = null;

    public function login($account, $password)
    {
        $pass = hash_pwd($password);
        $this->db->where($this->config('db_column_user'), $account);
        $this->db->where($this->config('db_column_password'), $pass);
        $query = $this->db->get($this->config('table'));
        if (!$query->num_rows()) return false;
        $admin_id = $query->row()->{$this->config('db_column_key')};
        $this->session->set_userdata($this->config('session_key'), $admin_id);
        return true;
    }

    public function fb_login($object)
    {
        return false;
    }


    public function google_login($object)
    {
        $mail = isset($object['email']) ? $object['email'] : '';
        $this->db->where($this->config('db_column_user'), $mail);
        $query = $this->db->get($this->config('table'));
        if (!$query->num_rows()) return false;
        $admin_id = $query->row()->{$this->config('db_column_key')};
        $this->session->set_userdata($this->config('session_key'), $admin_id);
        $this->db->where($this->config('db_column_user'), $mail);
        $this->db->update('admin', [$this->config('db_column_google') => $object['id']]);
        return true;
    }

    public function logout()
    {
        $this->session->unset_userdata($this->config('session_key'));
        redirect(base_url(sprintf('%slogin', $this->crud->get_prefix())));
    }

    public function get()
    {
        if (!$this->_user) {
            $admin_id = $this->session->userdata($this->config('session_key'));
            $this->_user =
                $this->db->get_where($this->config('table'), [$this->config('db_column_key') => $admin_id])->row();
        }
        return $this->_user;
    }

    public function get_name()
    {
        return $this->get()->{$this->config('db_column_name')};
    }

    public function get_roles()
    {
        return explode(',', $this->get()->{$this->config('db_column_role')});
    }

    public function get_id()
    {
        return $this->session->userdata($this->config('session_key'));
    }

    public function redirect_unauthorized_page()
    {
        echo $this->load->view('crud/layout/500', [], true);
        exit;
    }

    public function check($module = null)
    {
        if (is_null($module)) {
            return (bool)$this->session->userdata($this->config('session_key'));
        }
        if (!$this->session->userdata($this->config('session_key'))) return false;
        return in_array($module, $this->get_roles());
    }

    public function redirect_login_page()
    {
        redirect(base_url(sprintf('%slogin', $this->crud->get_prefix())));
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'admin',
            'id' => intval($this->get_id()),
            'name' => $this->get_name()
        ];
    }
}