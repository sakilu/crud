<select select_id="<?= $unique ?>" id="<?= $field_name ?>" name="<?= $field_name ?>"
        class="select2-single form-control"></select>
<script>
    $(function () {
        var _options<?= $unique ?> = <?= json_encode($options) ?>;
        $('#<?=$trigger_field?>').change(function () {
            var custom_array = _options<?= $unique ?>[$('#<?=$trigger_field?>').val()];
            if (!custom_array) {
                $('[select_id="<?=$unique?>"]').html('').change()
            } else {
                $('[select_id="<?=$unique?>"]').html(custom_array.map(function (option) {
                    return '<option value="' + option.id + '">' + option.text + '</option>';
                })).change()
            }
        });
        $('[select_id="<?=$unique?>"]').select2({
            'width': 'resolve',
            data: _options<?= $unique ?>[$('#<?=$trigger_field?>').val()]
        });
        <?php if ($this->form->get_primary_key()) { ?>
        $('#<?=$trigger_field?>').trigger("change");
        $('[select_id="<?=$unique?>"]').val(<?=$value?>).trigger("change");
        <?php } ?>
    });
</script>