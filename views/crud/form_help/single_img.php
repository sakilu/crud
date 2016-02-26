<?php if (!$this->form->get(Form::KEY_READONLY)) { ?>
    <label class="field prepend-icon append-button file">
        <span class="button btn-primary">Choose File</span>
        <input type="file" accept="image/*" class="gui-file" select_id="<?= $unique ?>" accept="image"
               name="<?= $field_name ?>" id="<?= $field_name ?>"/>
        <input type="text" data-text="<?= $unique ?>" class="gui-input" id="uploader1"
               placeholder="Please Select A File">
        <label class="field-icon">
            <i class="fa fa-upload"></i>
        </label>
        <?php if ($url == base_url('assets/no-photo.gif')) { ?>
            <a href="<?= $url ?>" target="_blank"><img style="width: 100px" id="<?= $unique ?>" src="<?= $url ?>"
                                                       class="image" alt=""></a>

        <?php } ?>
    </label>
<?php } ?>
<?php if ($url != base_url('assets/no-photo.gif')) { ?>
    <a href="<?= $url ?>" target="_blank"><img style="width: 100px" id="<?= $unique ?>" src="<?= $url ?>" class="image"
                                               alt=""></a>
<?php } ?>

<script>
    $(function () {
        $('[select_id="<?=$unique?>"]').change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#<?=$unique?>').attr('src', e.target.result);
                    $('[data-text="<?=$unique?>"]').val($('#<?=$field_name?>').val());
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>