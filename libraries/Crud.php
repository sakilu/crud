<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crud
{
    use Container;

    /**
     * 存放 Crud 的處理欄位
     */
    const KEY_COLUMNS = 'key_columns';

    /**
     * 主Table
     */
    const KEY_TABLE = 'key_table';

    /**
     * 額外要 select 的欄位
     */
    const KEY_SELECT_COLUMNS = 'key_select_columns';

    /**
     *
     */
    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
        $this->set(self::KEY_SELECT_COLUMNS, []);
        $this->db = $this->load->database('default', true);
    }

    /**
     * @param MY_Controller $controller
     */
    public function set_controller($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * @return MY_Controller
     */
    public function get_controller()
    {
        return $this->_controller;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function get_module_name()
    {
        if (!$this->_controller) {
            throw new Exception('must be set controller');
        }
        return strtolower(get_class($this->_controller));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function get_module_url()
    {
        if (!$this->_controller) {
            throw new Exception('must be set controller');
        }
        $name = strtolower(get_class($this->_controller));
        return $this->_config['prefix'] . $name;
    }

    public function get_prefix()
    {
        return $this->_config['prefix'];
    }

    public function state_list_save()
    {
//        $this->logger->addDebug('state_list_save', $this->input->post());
        $state = $this->input->post();
        if (isset($state['length'])) $state['length'] = intval($state['length']);
        if (isset($state['start'])) $state['start'] = intval($state['start']);
        if (isset($state['time'])) $state['time'] = intval($state['time']);
        $this->session->set_userdata($this->get_module_name() . '_list', $state);
    }

    public function state_list_load()
    {
//        $this->logger->addDebug('state_list_load', $this->session->userdata($this->get_module_name() . '_list'));
        return $this->session->userdata($this->get_module_name() . '_list');
    }

    /**
     * @return CI_DB
     */
    public function get_db()
    {
        return $this->db;
    }

    public function get_columns_def()
    {
        $defs = [];
        $count = 0;
        foreach ($this->get(self::KEY_COLUMNS) as $column) {
            $defs[] = $column->get_def($count++);
        }
        return json_encode($defs);
    }

    public function get_list_data()
    {
        $db = $this->get_db();
        $db->query("SET time_zone='+8:00'");

        $table = $this->get(Crud::KEY_TABLE);
        $select = [];
        foreach ($this->get(self::KEY_COLUMNS) as $column) {
            if ($column instanceof AbstractColumn && $column->by_db) {
                $field = $column->get(AbstractColumn::KEY_FIELD);
                if ($db->field_exists($field, $column->get(AbstractColumn::KEY_TABLE))) {
                    $select[] = $column->get(AbstractColumn::KEY_TABLE) . '.' . $column->get(AbstractColumn::KEY_FIELD);
                } else {
                    $select[] = $column->get(AbstractColumn::KEY_FIELD);
                }
            }
        }
        foreach ($this->get(self::KEY_SELECT_COLUMNS) as $column_name) {
            $select[] = $column_name;
        }
        $primary_key = $this->get_primary_key_column_name();
        if ($primary_key && !in_array($primary_key, $select)) {
            $select[] = $this->get(self::KEY_TABLE) . '.' . $primary_key;
        }

        $db->select(implode(',', $select));
        if ($this->db->field_exists($table . '_trash', $table)) {
            $db->where($table . '_trash', 0);
        }
        $return_data = [];
        foreach ($db->get($table)->result() as $_db_row) {
            $_row = [];
            $i = 0;
            foreach ($this->get(self::KEY_COLUMNS) as $column) {
                if ($column instanceof AbstractColumn) {
                    $field = $column->get(AbstractColumn::KEY_FIELD);
                    if (isset($_db_row->{$field})) {
                        $origin_value = $_db_row->{$field};
                    } else {
                        $origin_value = '';
                    }
                    $_row[$i++] = $column->get_value($origin_value, $_db_row);
                }
            }
            $return_data[] = $_row;
        }
        return $return_data;
    }

    function get_primary_key_column_name()
    {
        $table = $this->get(self::KEY_TABLE);
        $fields = $this->get_db()->field_data($table);
        foreach ($fields as $field) {
            if ($field->primary_key) {
                return $field->name;
            }
        }
    }

    function single_delete($table, $id)
    {
        $fields = $this->get_db()->field_data($table);
        foreach ($fields as $field) {
            if ($field->primary_key) {
                $this->get_db()->where($field->name, $id);
                $this->get_db()->delete($table);
                return;
            }
        }
    }
}

abstract class AbstractColumn
{
    use Container;

    public $by_db = true;

    protected $_def = [];

    const KEY_INFO = 'key_info';

    const KEY_FIELD = 'key_field';

    const KEY_TABLE = 'key_table';

    const KEY_DISPLAY = 'key_display';

    const KEY_OPTIONS = 'key_options';

    public function __construct($_table, $_field, $_display, $width = null)
    {
        $this->set(self::KEY_FIELD, $_field);
        $this->set(self::KEY_DISPLAY, $_display);
        $this->set(self::KEY_TABLE, $_table);
        $this->set(self::KEY_INFO, get_col_info($_table, $_field));
        if (!is_null($width)) {
            $this->set_width($width);
        }
    }

    /**
     * @return null|string
     */
    public function get_def($key)
    {
        $this->_def['targets'] = $key;
        return empty($this->_def) ? null : $this->_def;
    }

    public function set_width($width)
    {
        $this->width = $width;
    }

    public function get_width()
    {
        $width = isset($this->width) ? $this->width : null;
        return empty($width) ? '' : sprintf("width:%s;", $width);
    }


    /**
     * @param array $def
     */
    public function set_def($def)
    {
        $this->_def = array_merge($this->_def, $def);
    }

    public function get_value($value, $row)
    {
        $key_info = $this->get(self::KEY_INFO);
        $type = ($key_info instanceof Field_info) ? $key_info->get_type() : false;
        if (strpos($type, 'datetime') !== false) {
            return strpos($value, '0000-00-00') !== false ? '' : substr($value, 0, 16);
        }
        if (strpos($type, 'timestamp') !== false) {
            return strpos($value, '0000-00-00') !== false ? '' : substr($value, 0, 16);
        }
        if (strpos($type, 'date') !== false) {
            if (!$value) return '';
            return strpos($value, '0000-00-00') !== false ? '' : substr($value, 0, 16);
        }
        if (strpos($type, 'int') !== false) {
            $options = $this->get(Column::KEY_OPTIONS);
            if (!empty($options) && isset($options[$value])) {
                return $options[$value];
            }
            return intval($value);
        }
        if (strpos($type, 'float') !== false || strpos($type, 'double') || strpos($type, 'decimal') !== false
        ) {
            return floatval($value);
        }

        if (strpos($type, 'enum') !== false) {
            $options = $this->get(Column::KEY_OPTIONS);
            if (!empty($options) && isset($options[$value])) {
                return $options[$value];
            }
            return $value;
        }
        if (strpos($type, 'text') !== false) {
            return mb_substr($value, 0, 20, 'UTF-8');
        }

        $options = $this->get(Column::KEY_OPTIONS);
        if (!empty($options)) {
            $return_array = [];
            foreach (explode(',', trim($value)) as $v) {
                if (empty($v)) continue;
                $return_array[] = $options[$v];
                sort($return_array);
            }
            return implode('<br />', $return_array);
        }
        return $value;
    }

    public function get_yadcf_setting($key)
    {
        $key_info = $this->get(self::KEY_INFO);
        $type = ($key_info instanceof Field_info) ? $key_info->get_type() : false;
        $options = $this->get(Column::KEY_OPTIONS);
        if (!empty($options)) {
            foreach ($options as $option) {
                $data[] = sprintf('"%s"', $option);
            }
            sort($data);
            return sprintf('{
                        column_number: %d,
                        filter_type: "select",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        data: [%s],
                        filter_default_label: ""
            },', $key, implode(',', $data));
        }


        if (strpos($type, 'char') !== false || strpos($type, 'text') !== false) {
            return sprintf('{
                        column_number: %d,
                        filter_type: "text",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        filter_default_label: ""
                    },', $key);
        } elseif (strpos($type, 'int') !== false || strpos($type, 'float') !== false || strpos($type, 'double') ||
            strpos($type, 'decimal') !== false
        ) {
            return sprintf('{
                        column_number: %d,
                        filter_type: "range_number",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        filter_default_label: ["",""]
                    },', $key);
        } elseif (strpos($type, 'enum') !== false) {
            $enums = gender_enums($this->get(self::KEY_TABLE), $this->get(self::KEY_FIELD));
            $data = [];
            foreach ($enums as $option) {
                $data[] = sprintf('"%s"', $option);
            }
            return sprintf('{
                        column_number: %d,
                        filter_type: "select",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        data: [%s],
                        filter_default_label: ""
                    },', $key, implode(',', $data));
        } elseif (strpos($type, 'date') !== false) {
            return sprintf('{
                        column_number: %d,
                        filter_type: "range_date",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        date_format: "yy-mm-dd",
                        filter_default_label: ["",""]
                    },', $key);
        } elseif (strpos($type, 'timestamp') !== false) {
            return sprintf('{
                        column_number: %d,
                        filter_type: "range_date",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        date_format: "yy-mm-dd",
                        filter_default_label: ["",""]
                    },', $key);
        } elseif (strpos($type, 'set') !== false) {
            $data = [];
            $options = $this->get(Column::KEY_OPTIONS);
            if (!empty($options)) {
                sort($options);
                foreach ($options as $option) {
                    $data[] = sprintf('"%s"', $option);
                }
                return sprintf('{
                        column_number: %d,
                        filter_type: "multi_select",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        data: [%s],
                        filter_default_label: ""
                    },', $key, implode(',', $data));
            }
            $enums = gender_enums($this->get(self::KEY_TABLE), $this->get(self::KEY_FIELD));
            sort($enums);
            $data = [];
            foreach ($enums as $option) {
                $data[] = sprintf('"%s"', $option);
            }
            return sprintf('{
                        column_number: %d,
                        filter_type: "multi_select",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        data: [%s],
                        filter_default_label: ""
                    },', $key, implode(',', $data));

        }
        return;
    }

}

class Column extends AbstractColumn
{

}

class Column_prototype extends AbstractColumn
{
    public $by_db = false;

    public function __construct($_table, $_field, $_display, $width = null)
    {
        $this->set(self::KEY_FIELD, $_field);
        $this->set(self::KEY_DISPLAY, $_display);
        $this->set(self::KEY_TABLE, $_table);
        if (!is_null($width)) {
            $this->set_width($width);
        }
    }

    public function get_value($value, $row)
    {
        return $value;
    }

    public function get_yadcf_setting($key)
    {
        return sprintf('{
                        column_number: %d,
                        filter_type: "text",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        filter_default_label: ""
                    },', $key);
    }

}


class Column_youtube extends AbstractColumn
{
    public function get_yadcf_setting($key)
    {
        return '';
    }

    public function get_value($value, $row)
    {
        return '';
    }
}

class Column_single_file extends AbstractColumn
{
    public function get_yadcf_setting($key)
    {
        return '';
    }

    public function get_value($value, $row)
    {
        if (empty($value)) return '';
        $data = $this->uploads->read($value);
        if (empty($data)) return '';
        return sprintf('<button class="btn btn-primary btm-sm" onclick="window.open(\'%s\')">開啟</button>',
            base_url($data[0]['path']));
    }
}

class Column_single_img extends AbstractColumn
{
    public $by_db = false;

    public function __construct($_table, $_field, $_display, $width = null)
    {
        $this->set(self::KEY_FIELD, $_field);
        $this->set(self::KEY_DISPLAY, $_display);
        $this->set(self::KEY_TABLE, $_table);
        if (!is_null($width)) {
            $this->set_width($width);
        }
    }

    public function get_yadcf_setting($key)
    {
        return '';
    }

    public function get_value($value, $row)
    {
        $path =
            $this->crud_model->file_view($this->get(self::KEY_TABLE), $row->{$this->get(self::KEY_FIELD)}, '', '100',
                $thumb = 'yes', $src = 'no', $multi = '', $multi_num = '', $ext = '.jpg');
        if ($path) {
            $source =
                $this->crud_model->file_view($this->get(self::KEY_TABLE), $row->{$this->get(self::KEY_FIELD)}, '', '',
                    $thumb = 'no', $src = 'yes', $multi = '', $multi_num = '', $ext = '.jpg');
            return sprintf('<a href="%s" target="_blank">%s</a>', $source, $path);
        } else {
            return $path;
        }
    }

}

class Column_url extends AbstractColumn
{
    public function get_yadcf_setting($key)
    {
        return '';
    }

    public function get_value($value, $row)
    {
        if ($value) {
            return sprintf('<button onclick="window.open(\'%s\');" type="button" class="btn btn-info mr10">Open</button>',
                $value);
        }
        return '';
    }

}

class Column_edit extends AbstractColumn
{
    public $by_db = false;

    public $edit_enable = true;

    public $trash_enable = true;

    public $view_enable = false;

    public function __construct($_table, $_field, $_display, $width = null)
    {
        $this->set(self::KEY_FIELD, $_field);
        $this->set(self::KEY_DISPLAY, $_display);
        $this->set(self::KEY_TABLE, $_table);
        if (!is_null($width)) {
            $this->set_width($width);
        } else {
            $this->set_width('80px');
        }
    }

    public function get_value($value, $row)
    {
        return $this->load->view('crud/list_help/edit', [
            'primary_value' => $row->{$this->get(self::KEY_FIELD)},
            'column_edit_trash' => $this->trash_enable,
            'column_edit_edit' => $this->edit_enable,
            'column_edit_view' => $this->view_enable,
        ], true);
    }

    public function get_yadcf_setting($key)
    {
        return '';
    }
}

class Column_edit_only extends AbstractColumn
{
    public $by_db = false;

    public function get_value($value, $row)
    {
        return $this->load->view('crud/list_help/edit_only', [
            'primary_value' => $row->{$this->get(self::KEY_FIELD)}
        ], true);
    }

    public function get_yadcf_setting($key)
    {
        return '';
    }
}

class Column_view extends AbstractColumn
{
    public $by_db = false;

    public function get_value($value, $row)
    {
        return $this->load->view('crud/list_help/view', [
            'primary_value' => $row->{$this->get(self::KEY_FIELD)}
        ], true);
    }

    public function get_yadcf_setting($key)
    {
        return '';
    }
}


class Column_custom extends AbstractColumn
{
    public $by_db = false;

    public function __construct($_table, $_field, $_display, $array)
    {
        $this->set(self::KEY_FIELD, $_field);
        $this->set(self::KEY_DISPLAY, $_display);
        $this->set(self::KEY_TABLE, $_table);
        $this->set('Column_custom', $array);
    }

    public function get_value($value, $row)
    {
        $callback = $this->get('Column_custom');
        return call_user_func($callback, $value, $row);
    }

    public function get_yadcf_setting($key)
    {
        return sprintf('{
                        column_number: %d,
                        filter_type: "text",
                        column_data_type: "html",
                        filter_reset_button_text: false,
                        style_class: "form-control",
                        filter_default_label: ""
                    },', $key);
    }
}

class Column_password extends AbstractColumn
{
    public function get_value($value, $row)
    {
        return '****';
    }

    public function get_yadcf_setting($key)
    {
        return '';
    }
}

class Field_info
{
    protected $_name;

    protected $_type;

    protected $_max_length;

    public $primary_key;

    public function __construct($_name, $_type, $_max_length, $_primary_key)
    {
        $this->_name = $_name;
        $this->_type = $_type;
        $this->_max_length = $_max_length;
        $this->primary_key = $_primary_key;
    }

    public function get_name()
    {
        return $this->_name;
    }

    public function get_type()
    {
        return $this->_type;
    }

    public function get_max_length()
    {
        return $this->_max_length;
    }
}