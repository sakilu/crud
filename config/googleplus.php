<?php

$CI=& get_instance();
$CI->load->database();

$config['googleplus']['client_id'] 	= $CI->db->get_where('general_settings',array('type'=>'google_id'))->row()->value;
$config['googleplus']['client_secret'] = $CI->db->get_where('general_settings',array('type'=>'google_key'))->row()->value;

?>
