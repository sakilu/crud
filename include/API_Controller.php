<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

abstract class API_Controller extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        libxml_disable_entity_loader(false);

        if (strpos($_SERVER["REQUEST_URI"], 'logs') === false) {
            $header = [];
            foreach (getallheaders() as $key => $value) {
                $header[] = $key . ' : ' . $value;
            }
            $content = file_get_contents("php://input");
            $post = [];
            if (!$content) {
                foreach ($_POST as $key => $value) {
                    $post[] = sprintf('%s - %s', $key, $value);
                }
                $content = implode('<br />', $post);
            }

            $this->db->insert('debug', [
                'url' => $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"],
                'datetime' => date('Y-m-d H:i:s'),
                'header' => implode(',', $header),
                'body' => $content,
                'timestamp' => time()
            ]);
            $this->db->where('timestamp <', time() - 7200);
            $this->db->delete('debug');
        }
    }

    protected function _response($data, $http_code = 200, $msg = null)
    {
        $response = [];
        $response['error'] = $http_code != 200;
        $response['error_msg'] = $msg;
        $response['body'] = $data;
        $this->response($response, $http_code);
    }

    protected function _check_signature()
    {
        $signature = $this->input->get_request_header('signature');
        $time = $this->input->get_request_header('time');
        $public_key = $this->input->get_request_header('public_key');
        $type = $this->input->get_request_header('type');
        $rand_str = $this->input->get_request_header('rand_str');
        $config = $this->config->item('login_type');

        if (empty($signature) || empty($time) || empty($public_key) || empty($type) || empty($rand_str)) {
            $this->_response(null, 401, '參數不足');
        }

        if (!in_array($type, [
            TYPE_GOOGLE,
            TYPE_FB,
            TYPE_MAIL
        ])
        ) {
            $this->_response(null, 401, 'type 錯誤');
        }

        if (time() - $time > 60000) {
            $this->_response(null, 408, 'Request Timeout');
        }

        $query = $this->db->get_where('user', array('user_' . $config[$type] => $public_key));
        if ($query->num_rows() != 1) {
            $this->_response(null, 401, '無此使用者');
        }

        $row = $query->row();
        $private_key = $row->user_login_token;
        $stringToSign = md5($public_key . $time . $private_key . $rand_str . $config[$type]);
        if (base64_encode(hash_hmac('sha1', $stringToSign, $private_key, true)) !== $signature) {
            $this->_response(null, 401, '檢核碼錯誤');
        }
        return User_model::filter($row);
    }

}
