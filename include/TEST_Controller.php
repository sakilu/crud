<?php

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class TEST_Controller extends CI_Controller
{

    protected $_public_key = 117724679400831485029;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('mocks/user_mock');
    }

    protected function test($method, $url, $auth, $param = null, $no_debug = false)
    {
        $curl_options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_HEADER => 1,
            CURLOPT_VERBOSE => 1
        ];
        if ($auth) {
            $curl_options[CURLOPT_HTTPHEADER] = $this->user_mock->get_signature();
        }
        if ($param) {
            $curl_options[CURLOPT_POSTFIELDS] = $param;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $err = curl_error($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $data = json_decode($body, true);
        if ($no_debug) $data = $data['body'];

        if (!$no_debug) {
            @$this->unit->run($data['error'], 'is_false', 'error = false');
            @$this->unit->run($data['error_msg'], 'is_null', 'error_msg = null');
            @$this->unit->run($info['http_code'], '200', 'http_code=200');
            @$this->unit->run($err, '', 'curl運行正確');
        } else {
            echo json_encode($data);
            exit;
        }

        $this->response($header, $data ? $data : $body, $info['request_header'], $param);
        curl_close($curl);
        echo $this->unit->report();

    }

    protected function response($res_header, $res_body, $req_header, $req_body = null)
    {
        echo nl2br($req_header);
        if ($req_body) var_dump($req_body);
        echo '<hr >';
        echo nl2br($res_header);
        if (!is_array($res_body) && !is_object($res_body)) {
            echo $res_body;
        } else {
            if (is_array($res_body)) {
                echo json_encode($res_body);
            } else {
                var_dump($res_body);
            }
        }
    }

}