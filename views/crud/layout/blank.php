<?php $this->load->view('crud/head') ?>
<!-- End: Header -->

<?php $this->load->view('crud/sidebar') ?>

<!-- Start: Content-Wrapper -->
<section id="content_wrapper">
    <div style="display: none" class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p id="list_alert_panel"></p>
    </div>
    <section id='<?= $this->layout->get_content_id(); ?>' class="table-layout animated fadeIn">
    <?php
        if(!empty($content)){
            echo $content;
        }
    ?>
    </section>
</section>
<!-- End: Content-Wrapper -->

</div>
<!-- End: Main -->
<?php $this->load->view('crud/script'); ?>
<!-- END: PAGE SCRIPTS -->
</body>
</html>