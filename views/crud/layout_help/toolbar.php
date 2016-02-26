<!-- Start: Topbar -->
<?php if (!$this->layout->disable_toolbar) { ?>
    <header id="topbar" class="ph10">
    <span class="topbar-left hidden-xs hidden-sm">
    <?php if ($this->router->fetch_method() == 'index' && !$this->layout->disable_add) { ?>
        <!-- 列表跳到至新增 -->
        <button type="button" onclick="ajax_load('<?= base_url(sprintf('%s/ajax_form/view',
            $this->crud->get_module_url())) ?>', '<?= $this->layout->get_content_id(); ?>');"
                class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> 新增
        </button>
    <?php } ?>
        <?php if ($this->router->fetch_method() == 'ajax_form') { ?>
            <button type="button" onclick="ajax_load('<?= base_url(sprintf('%s/index',
                $this->crud->get_module_url())) ?>', '<?= $this->layout->get_content_id(); ?>');"
                    class="btn btn-sm btn-info"><i class="fa fa-reply"></i>
            </button>
            <?php if (!$this->form->readonly()) { ?>
                <button type="button"
                        onclick="form_submit('<?= $this->form->get(Form::KEY_FORM_ID) ?>', 'alert_panel', '<?= $this->layout->get_content_id(); ?>','<?= $this->form->get_primary_key() ?
                            "更新成功" : "新增成功" ?>');"
                        class="btn btn-sm btn-primary"><i class="fa fa-save"></i> 儲存
                </button>
            <?php } ?>
        <?php } ?>
        <?php if ($this->router->fetch_method() == 'form' && !$this->form->readonly()) { ?>
            <button type="button"
                    onclick="form_submit('<?= $this->form->get(Form::KEY_FORM_ID) ?>', 'alert_panel', '<?= $this->layout->get_content_id(); ?>','儲存成功');"
                    class="btn btn-sm btn-primary"><i class="fa fa-save"></i> 儲存
            </button>
        <?php } ?>
        <?php if (strpos($this->router->fetch_method(), 'form') !== false) { ?>
            <button type="button"
                    onclick="ajax_load('<?= base_url(sprintf('%s/%s/%s/%d', $this->crud->get_module_url(),
                        $this->router->fetch_method(), $this->form->readonly() ? 'read' : 'view',
                        $this->form->get_primary_key())) ?>', '<?= $this->layout->get_content_id(); ?>');"
                    class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> 重整
            </button>
        <?php } else { ?>
            <button type="button"
                    onclick="ajax_load('<?= base_url(sprintf('%s/%s', $this->crud->get_module_url(),
                        $this->router->fetch_method())) ?>', '<?= $this->layout->get_content_id(); ?>');"
                    class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> 重整
            </button>
        <?php } ?>
    </span>
        <?php if ($this->router->fetch_method() == 'ajax_form') { ?>
            <span class="topbar-right hidden-xs hidden-sm">

            </span>
        <?php } ?>

    </header>
<?php } ?>