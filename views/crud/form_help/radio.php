<?php foreach ($options as $radio_value => $caption) { ?>
    <label class="radio-inline mr10">
        <input type="radio" <?php if ($radio_value == $value) echo 'checked'; ?> name="<?= $field_name ?>"
            <?php foreach ($attr as $k => $v) {
                echo sprintf('%s="%s"', $k, $v);
            } ?>
               id="<?= $field_name ?>_<?= @++$i ?>" value="<?= $radio_value ?>"/>
        <?= $caption ?>
    </label>
<?php } ?>