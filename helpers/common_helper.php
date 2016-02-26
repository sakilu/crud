<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_site_config')) {
    function get_site_config($k, $config_file = 'site')
    {
        $ci = &get_instance();
        $v = $ci->config->item($k);
        if(!empty($v)){
            return $v;
        }

        $ci->config->load($config_file, true);
        return $ci->config->item($k, $config_file);
    }

    function debug($message, $context)
    {
        $ci = &get_instance();
        $ci->logger->addDebug($message, $context);
    }

    function img_exists($type, $id, $width = '100', $height = '100', $thumb = 'no', $src = 'no', $multi = '',
                            $multi_num = '', $ext = '.jpg')
    {
        if ($multi == '') {
            if (file_exists('uploads/' . $type . '_image/' . $type . '_' . $id . $ext)) {
                return true;
            }
        }
        return false;
    }
}

