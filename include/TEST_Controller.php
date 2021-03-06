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

    protected function test_device($method, $url, $param = null)
    {
        $this->test($method, $url, false, $param);
    }


    protected function test($method, $url, $auth, $param = null, $test = true)
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

        $curl_header = [];
        if ($auth) {
            $curl_header = $this->user_mock->get_signature();
        }
        if ($param) {
            $curl_options[CURLOPT_POSTFIELDS] = is_array($param) ? http_build_query($param) : $param;
            $curl_header[] = 'Content-Type: application/x-www-form-urlencoded';
        }
        if (!empty($curl_header)) $curl_options[CURLOPT_HTTPHEADER] = $curl_header;
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $err = curl_error($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $data = json_decode($body, true);
        @$this->unit->run($data['error'], 'is_false', 'error = false');
        @$this->unit->run($data['error_msg'], 'is_null', 'error_msg = null');
        @$this->unit->run($info['http_code'], '200', 'http_code=200');
        @$this->unit->run($err, '', 'curl運行正確');

        $this->load->view('api/test', [
            'request_head' => $info['request_header'],
            'request_body' => $param,
            'response_head' => $header,
            'response_body' => substr($response, $header_size),
        ]);
        curl_close($curl);
    }
}