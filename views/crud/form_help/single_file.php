<?php if (!$this->form->get(Form::KEY_READONLY)) { ?>
    <label class="field prepend-icon append-button file">
        <span class="button btn-primary">選擇檔案</span>
        <input type="file" class="gui-file" select_id="<?= $unique ?>" name="<?= $field_name ?>" id="<?= $field_name ?>"/>
        <input type="text" data-text="<?= $unique ?>" class="gui-input" id="uploader1"
               placeholder="Please Select A File">
        <label class="field-icon">
            <i class="fa fa-upload"></i>
        </label>
    </label>
<?php } ?>
<?php if ($row) { ?>
    <p>
        <br >
        <button type="button" class="btn btn-primary btn-sm" onclick="window.open('<?= base_url($row->path) ?>')">
            <i class="fa fa-download"></i> Download
        </button>
    </p>
<?php } ?>
<script>
    $(function () {
        $('[select_id="<?=$unique?>"]').change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('[data-text="<?=$unique?>"]').val($('#<?=$field_name?>').val());
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>