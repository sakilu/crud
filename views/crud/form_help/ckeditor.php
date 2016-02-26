<textarea style="display: none" rows="12" id="<?= $unique ?>" name="<?= $field_name ?>"><?= $value ?></textarea>

<script>
    $(function () {
        // Turn off automatic editor initilization.
        // Used to prevent conflictions with multiple text
        // editors being displayed on the same page.
        CKEDITOR.disableAutoInline = true;

        // Init Ckeditor
        CKEDITOR.replace('<?= $unique ?>', <?=$json?>);
    });
</script>