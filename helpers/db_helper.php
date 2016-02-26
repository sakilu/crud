<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_col_info')) {
    static $_get_col_info_table;
    function get_col_info($table, $col)
    {
        if (isset($_get_col_info_table[$table])) {
            return $_get_col_info_table[$table][$col];
        }
        $ci = &get_instance();
        $fields = $ci->db->field_data($table);
        foreach ($fields as $field) {
            $field_object = new Field_info($field->name, $field->type, $field->max_length, $field->primary_key);
            $_get_col_info_table[$table][$field->name] = $field_object;
        }
        return $_get_col_info_table[$table][$col];
    }

    function gender_enums($table, $field)
    {
        $ci = &get_instance();
        $row = $ci->db->query("SHOW COLUMNS FROM " . $table . " LIKE '$field'")->row()->Type;
        $regex = "/'(.*?)'/";
        preg_match_all($regex, $row, $enum_array);
        $enum_fields = $enum_array[1];
        foreach ($enum_fields as $key => $value) {
            $enums[$value] = $value;
        }
        return $enums;
    }
}

