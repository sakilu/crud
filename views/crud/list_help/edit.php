<?php if ($column_edit_edit) { ?>
    <button type="button"
            onclick="ajax_load('<?= base_url() ?><?= $this->crud->get_module_url() ?>/ajax_form/view/<?= $primary_value ?>', '<?= $this->layout->get_content_id(); ?>');"
            class="btn btn-sm btn-primary">
        <i class="fa fa-pencil"></i>
    </button>
<?php } ?>
<?php if ($column_edit_view) { ?>
    <button type="button"
            onclick="ajax_load('<?= base_url() ?><?= $this->crud->get_module_url() ?>/ajax_form/read/<?= $primary_value ?>', '<?= $this->layout->get_content_id(); ?>');"
            class="btn btn-sm btn-success">
        <i class="fa fa-search"></i>
    </button>
<?php } ?>
<?php if ($column_edit_trash) { ?>
    <button type="button"
            onclick="ajax_remove('<?= base_url(sprintf('%s',
                $this->crud->get_module_url())) ?>', '<?= $this->layout->get_content_id(); ?>', '<?= base_url(sprintf('%s/ajax_remove/%d',
                $this->crud->get_module_url(), $primary_value)) ?>', '刪除成功');"
            class="btn btn-sm btn-danger">
        <i class="fa fa-trash-o"></i>
    </button>
<?php } ?>
