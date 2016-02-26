<button type="button"
        onclick="form_submit('<?= $this->form->get(Form::KEY_FORM_ID) ?>', 'alert_panel', '<?= $this->layout->get_content_id(); ?>','<?= $this->form->get_success_msg() ?>');"
        class="btn btn-primary"><i class="fa fa-save"></i> 儲存
</button>
<button type="button"
        onclick="ajax_load('<?= $this->form->get_reload_url(); ?>', '<?= $this->layout->get_content_id(); ?>');"
        class="btn btn-success"><i class="fa fa-refresh"></i> 重整
</button>