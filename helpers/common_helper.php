<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_site_config')) {

    function generate_key()
    {
        $key = "";

        $key_part = 7;
        $key_chunk = 6;
        $key_div = "-";
        $num_range_low = 48;
        $num_range_high = 57;
        $chr_range_low = 65;
        $chr_range_high = 90;

        for ($i = 0; $i != $key_part; $i++) {
            for ($x = 0; $x != $key_chunk; $x++) {
                $key .= (mt_rand() & 1 == 1 ? chr(mt_rand($num_range_low, $num_range_high)) :
                    chr(mt_rand($chr_range_low, $chr_range_high)));
            }
            $key .= $key_div;
        }
        return trim($key, $key_div);
    }

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
        $ci->db->select($table . "_id, $name");
        $ci->db->order_by($name);
        $rows = $ci->db->get($table)->result();
        $return = [];
        foreach ($rows as $row) {
            $return[$row->{$table . '_id'}] = $row->{$name};
        }
        return $return;
    }

    function get_options_by_group($table, $name, $group_by)
    {
        $ci = &get_instance();
        if ($ci->db->field_exists($table . '_trash', $table)) {
            $ci->db->where($table . '_trash', 0);
        }
        $ci->db->select($table . "_id, $name, $group_by");
        $ci->db->order_by($name);
        $rows = $ci->db->get($table)->result();
        $return = [];
        foreach ($rows as $row) {
            $return[$row->{$group_by}][] = [
                'id' => $row->{$table . '_id'},
                'text' => $row->{$name}
            ];
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

