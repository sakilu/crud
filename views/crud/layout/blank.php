<?php if (!$this->layout->is_ajax_request()) {
    $this->load->view('crud/head');
    $this->load->view('crud/sidebar');
} ?>

<?php if (!$this->layout->is_ajax_request()) { ?>
    <section id="content_wrapper">
    <section id='<?= $this->layout->get_content_id(); ?>' class="table-layout animated fadeIn">
<?php } ?>
<?php
if (!empty($content)) {
    echo $content;
}
?>
<?php if (!$this->layout->is_ajax_request()) { ?>
    </section>
    </section>
<?php } ?>
<!-- End: Content-Wrapper -->

<?php if (!$this->layout->is_ajax_request()) { ?>
</div>
<?php $this->load->view('crud/script'); ?>
</body>
</html>
<?php } ?>