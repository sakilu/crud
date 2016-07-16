<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form
{
    use Container;

    const KEY_FORM_ID = 'key_id';

    const KEY_FORM_DATA = 'key_form_data';

    const KEY_COLUMN = 'key_column';

    const KEY_ACTION = 'key_action';

    const KEY_READONLY = 'key_readonly';

    public function __construct()
    {
        $this->_config = $this->config->item(strtolower(get_class($this)));
        $this->set(self::KEY_FORM_ID, uniqid());
        $this->set(self::KEY_READONLY, false);
        $this->set_primary_key(false);
    }

    public function set_primary_key($value)
    {
        $this->primary_key = $value;
        return $this;
    }

    public function readonly($bool = null)
    {
        if (!is_null($bool)) {
            $this->set(self::KEY_READONLY, $bool);
        } else {
            return $this->get(self::KEY_READONLY);
        }
    }

    public function get_primary_key()
    {
        return isset($this->primary_key) ? $this->primary_key : false;
    }

    public function form_open($attr = [])
    {
        if ($this->router->fetch_method() == 'form') {
            $action = 'submit';
        } else {
            $action = $this->get_primary_key() ? 'edit/' . intval($this->get_primary_key()) : 'add';
        }
        $url = base_url(sprintf('%s/%s/%s', $this->crud->get_module_url(), $this->router->fetch_method(), $action));
        return form_open($url, array_merge([
            'class' => 'form-horizontal',
            'id' => $this->_data[self::KEY_FORM_ID],
        ], $attr));
    }

    public function data()
    {
        return isset($this->_data[self::KEY_FORM_DATA]) ? $this->_data[self::KEY_FORM_DATA] : null;
    }


    public function input($field_name, $attr = [], $value = null)
    {
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? null : $data->{$field_name};
        }
        if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', $value);
        return form_input(array_merge([
            'name' => $field_name,
            'id' => $field_name,
            'class' => 'form-control'
        ], $attr), $value);
    }

    public function datepicker($field_name, $attr = [], $value = null)
    {
        $unique = uniqid();
        $script = $this->load->view('crud/form_help/datepicker', array('unique' => $unique), true);
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) || strpos($data->{$field_name}, '0000') !== false ? null :
                $data->{$field_name};
        }
        if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', $value);
        return form_input(array_merge([
            'name' => $field_name,
            'id' => $field_name,
            'class' => 'form-control',
            'select_id' => $unique
        ], $attr), $value) . $script;
    }

    public function password($field_name, $attr = [])
    {
        return form_password(array_merge([
            'name' => $field_name,
            'id' => $field_name,
            'class' => 'form-control',
            'autocomplete' => 'off'
        ], $attr), null);
    }

    public function textarea_grow($field_name, $attr = [], $value = null)
    {
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? null : $data->{$field_name};
        }
        if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', nl2br($value));
        return form_textarea(array_merge([
            'name' => $field_name,
            'id' => $field_name,
            'class' => 'form-control textarea-grow',
            'rows'
        ], $attr), $value);
    }

    public function textarea($field_name, $attr = [], $value = null)
    {
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? null : $data->{$field_name};
        }
        if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', nl2br($value));
        return form_textarea(array_merge([
            'name' => $field_name,
            'id' => $field_name,
            'class' => 'form-control',
            'rows' => 3
        ], $attr), $value);
    }

    public function radio($field_name, $attr = [], $value = null)
    {
        $column = $this->get_column($field_name);
        $type =
            ($column->get(Column::KEY_INFO) instanceof Field_info) ? $column->get(Column::KEY_INFO)->get_type() : false;
        $options = $column->get(Column::KEY_OPTIONS);
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? '' : $data->{$field_name};
        }
        if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', $value);
        $unique = uniqid();

        if (empty($options) && strpos($type, 'enum') !== false) {
            $options = gender_enums($column->get(Column::KEY_TABLE), $column->get(Column::KEY_FIELD));
        }
        $html = $this->load->view('crud/form_help/radio', [
            'unique' => $unique,
            'options' => $options,
            'value' => $value,
            'attr' => $attr,
            'field_name' => $field_name
        ], true);
        return $html;
    }

    public function trigger_select($field_name, $trigger_field, $value = null)
    {
        $column = $this->get_column($field_name);
        $options = $column->get(Column::KEY_OPTIONS);
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? array() : $data->{$field_name};
        }
        $unique = uniqid();
        $attr = [
            'unique' => $unique,
            'options' => $options,
            'value' => $value,
            'trigger_field' => $trigger_field,
            'field_name' => $field_name
        ];
        return $this->load->view('crud/form_help/trigger_select', $attr, true);
    }


    /**
     * html select
     * @param $field_name
     * @param array $attr
     * @return string
     */
    public function select($field_name, $attr = [], $value = null)
    {
        $column = $this->get_column($field_name);
        $type =
            ($column->get(Column::KEY_INFO) instanceof Field_info) ? $column->get(Column::KEY_INFO)->get_type() : false;
        $options = $column->get(Column::KEY_OPTIONS);
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? array() : $data->{$field_name};
        }
        $unique = uniqid();
        $script = $this->load->view('crud/form_help/select', array('unique' => $unique), true);
        if (!empty($options)) {
            if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>',
                $options[$value]);
            return form_dropdown($field_name, $options, $value, array_merge([
                'class' => 'select2-single form-control',
                'id' => $field_name,
                'select_id' => $unique
            ], $attr)) . $script;
        }
        if (strpos($type, 'enum') !== false) {
            $enums = gender_enums($column->get(Column::KEY_TABLE), $column->get(Column::KEY_FIELD));
            if ($this->get(self::KEY_READONLY)) return sprintf('<p class="form-control-static">%s</p>', $enums[$value]);
            return form_dropdown($field_name, $enums, $value, array_merge([
                'class' => 'select2-single form-control',
                'id' => $field_name,
                'select_id' => $unique
            ], $attr)) . $script;
        }
    }

    /**
     * 檢查有無圖片  有直接放圖片連結 ~ 無 放預設圖
     * @param $field_name
     * @param array $attr
     * @return string
     */
    public function single_img($field_name, $attr = [])
    {
        $column = $this->get_column($field_name);
        $table = $column->get(Column::KEY_TABLE);

        $data = $this->data();
        @$id = isset($data->{$column->get(Column::KEY_FIELD)}) ? $data->{$column->get(Column::KEY_FIELD)} : 0;
        if (!img_exists($table, $id)) {
            $url = base_url('assets/no-photo.gif');
        } else {
            $url =
                $this->crud_model->file_view($column->get(Column_single_img::KEY_TABLE), $id, '', '', 'no', 'src', '',
                    '');
        }

        $unique = uniqid();
        $view_data = [
            'unique' => $unique,
            'url' => $url,
            'field_name' => $field_name
        ];
        return $this->load->view('crud/form_help/single_img', $view_data, true);
    }

    public function single_file($field_name, $attr = [])
    {
        $data = $this->data();
        $id = $data->{$field_name};
        $unique = uniqid();
        $view_data = [
            'unique' => $unique,
            'field_name' => $field_name,
            'row' => $this->db->get_where('files', ['id' => $id])->row()
        ];
        return $this->load->view('crud/form_help/single_file', $view_data, true);
    }

    public function multiselect($field_name, $attr = [], $value = array())
    {
        $column = $this->get_column($field_name);
        $type = $column->get(Column::KEY_INFO)->get_type();

        if (empty($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? array() : explode(',', $data->{$field_name});
        }
        $unique = uniqid();
        $script = $this->load->view('crud/form_help/multiselect', array('unique' => $unique), true);
        $options = $column->get(Column::KEY_OPTIONS);

        if (!empty($options)) {
            return form_multiselect($field_name . '[]', $options, $value, array_merge([
                'class' => 'form-control',
                'select_id' => $unique,
            ], $attr)) . $script;
        }
        if (strpos($type, 'set') !== false) {
            $enums = gender_enums($column->get(Column::KEY_TABLE), $column->get(Column::KEY_FIELD));
            return form_multiselect($field_name . '[]', $enums, $value, array_merge([
                'class' => 'form-control',
                'select_id' => $unique,
            ], $attr)) . $script;
        }
    }

    public function ckeditor($field_name, $attr = [], $value = null)
    {
        if (is_null($value)) {
            $data = $this->data();
            $value = empty($data->{$field_name}) ? '' : $data->{$field_name};
        }
        $unique = uniqid();
        $json = json_encode(array_merge($this->config->item('ckeditor')));
        $script = $this->load->view('crud/form_help/ckeditor', [
            'unique' => $unique,
            'value' => $value,
            'field_name' => $field_name,
            'json' => $json
        ], true);
        return $script;
    }

    public function label($field_name, $attr = [])
    {
        $required = !empty($attr['required']);
        unset($attr['required']);

        $column = $this->get_column($field_name);
        if (!$column) {
            throw new Exception('Form 欄位錯誤: ' . $field_name);
        }
        $display = $required && !$this->readonly() ? '*' . $column->get(Column::KEY_DISPLAY) :
            $column->get(Column::KEY_DISPLAY);
        return form_label($display, $field_name, array_merge([
            'class' => 'col-lg-3 control-label'
        ], $attr));
    }

    /**
     * @param $field_name
     * @return AbstractColumn
     */
    protected function get_column($field_name)
    {
        foreach ($this->get(self::KEY_COLUMN) as $key => $column) {
            if ($key == $field_name) {
                return $column;
            }
        }
    }

}