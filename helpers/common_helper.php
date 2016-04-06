<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_site_config')) {
    function get_site_config($k, $config_file = 'site')
    {
        $ci = &get_instance();
        $v = $ci->config->item($k);
        if (!empty($v)) {
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

    function get_options($table, $name)
    {
        $ci = &get_instance();
        if ($ci->db->field_exists($table . '_trash', $table)) {
            $ci->db->where($table . '_trash', 0);
        }
        $ci->db->select($name);
        $ci->db->order_by($name);
        $rows = $ci->db->get($table)->result();
        $return = [];
        foreach ($rows as $row) {
            $return[$row->{$table . '_id'}] = $row->{$name};
        }
        return $return;
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

