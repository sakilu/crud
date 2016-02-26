<button type="button"
        onclick="ajax_load('<?= base_url() ?><?= $this->crud->get_module_url() ?>/readonly/<?= $primary_value ?>', '<?= $this->layout->get_content_id(); ?>');"
        class="btn btn-primary">
    <i class="fa fa-search-plus"></i>
</button>