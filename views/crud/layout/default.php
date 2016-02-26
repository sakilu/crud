<?php if (!$this->layout->is_ajax_request()) {
    $this->load->view('crud/head');
    $this->load->view('crud/sidebar');
} ?>

<?php if (!$this->layout->is_ajax_request()) { ?>
    <section id="content_wrapper">
        <section id='<?= $this->layout->get_content_id(); ?>' class="table-layout animated fadeIn">
<?php } ?>
        <div class="panel">
            <?php $this->load->view('crud/layout_help/toolbar'); ?>
            <div style="display: none;margin-bottom: 0px;" class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <p id="alert_panel"></p>
            </div>
            <?php $this->load->view($this->layout->content_path) ?>
            <?php $this->load->view('crud/layout_help/toolbar'); ?>
        </div>
<?php if (!$this->layout->is_ajax_request()) { ?>
        </section>
    </section>
<?php } ?>
<?php if (!$this->layout->is_ajax_request()) { ?>
</div>
<!-- End: Main -->
<?php $this->load->view('crud/script'); ?>
<!-- END: PAGE SCRIPTS -->
</body>
</html>
<?php } ?>