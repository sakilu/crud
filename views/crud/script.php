<!-- BlockUI -->
<script src="<?= base_url() ?>crud/js/jquery.blockUI.js"></script>

<!-- Datatables -->

<script src="<?= base_url() ?>vendor/plugins/datatables/media/js/jquery.dataTables.js"></script>

<!-- Datatables Tabletools addon -->
<!--<script src="--><?//= base_url() ?><!--vendor/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"-->
<!--        chartset="utf8"></script>-->

<!-- Datatables ColReorder addon -->
<script src="<?= base_url() ?>vendor/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>

<!-- Datatables Bootstrap Modifications  -->
<script src="<?= base_url() ?>vendor/plugins/datatables/media/js/dataTables.bootstrap.js"></script>

<script src="<?= base_url() ?>vendor/plugins/highcharts/highcharts.js"></script>

<!-- JvectorMap Plugin + US Map (more maps in plugin/assets folder) -->
<script src="<?= base_url() ?>vendor/plugins/jvectormap/jquery.jvectormap.min.js"></script>
<script src="<?= base_url() ?>vendor/plugins/jvectormap/assets/jquery-jvectormap-us-lcc-en.js"></script>

<!-- FullCalendar Plugin + moment Dependency -->
<script src="<?= base_url() ?>vendor/plugins/fullcalendar/lib/moment.min.js"></script>
<script src="<?= base_url() ?>vendor/plugins/fullcalendar/fullcalendar.min.js"></script>

<!-- Time/Date Plugin Dependencies -->
<script src="<?= base_url() ?>vendor/plugins/globalize/globalize.min.js"></script>
<script src="<?= base_url() ?>vendor/plugins/moment/moment.min.js"></script>

<!-- BS Dual Listbox Plugin -->
<script src="<?= base_url() ?>vendor/plugins/duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

<!-- Bootstrap Maxlength plugin -->
<script src="<?= base_url() ?>vendor/plugins/maxlength/bootstrap-maxlength.min.js"></script>

<!-- Select2 Plugin Plugin -->
<script src="<?= base_url() ?>vendor/plugins/select2/select2.min.js"></script>

<!-- Typeahead Plugin -->
<script src="<?= base_url() ?>vendor/plugins/typeahead/typeahead.bundle.min.js"></script>

<!-- TagManager Plugin -->
<script src="<?= base_url() ?>vendor/plugins/tagmanager/tagmanager.js"></script>

<!-- DateRange Plugin -->
<script src="<?= base_url() ?>vendor/plugins/daterange/daterangepicker.min.js"></script>

<!-- DateTime Plugin -->
<script src="<?= base_url() ?>vendor/plugins/datepicker/js/bootstrap-datetimepicker.min.js"></script>

<!-- BS Colorpicker Plugin -->
<script src="<?= base_url() ?>vendor/plugins/colorpicker/js/bootstrap-colorpicker.min.js"></script>

<!-- MaskedInput Plugin -->
<script src="<?= base_url() ?>vendor/plugins/jquerymask/jquery.maskedinput.min.js"></script>

<script src="<?= base_url() ?>vendor/plugins/pnotify/pnotify.js"></script>

<script src="<?= base_url() ?>vendor/plugins/magnific/jquery.magnific-popup.js"></script>

<!-- Theme Javascript -->
<script src="<?= base_url() ?>assets/js/utility/utility.js"></script>
<!--<script src="--><? //=base_url()?><!--assets/js/demo/demo.js"></script>-->
<script src="<?= base_url() ?>assets/js/main.js"></script>

<!-- Widget Javascript -->
<script src="<?= base_url() ?>assets/js/demo/widgets.js"></script>

<!-- Summernote Plugin -->
<script src="<?= base_url() ?>vendor/plugins/summernote/summernote.min.js"></script>

<!-- Ckeditor JS -->
<script src="<?= base_url() ?>vendor/plugins/ckeditor/ckeditor.js"></script>

<!-- Adup Javascript -->
<script src="<?= base_url() ?>crud/js/jquery.dataTables.yadcf.js"></script>
<script src="<?= base_url() ?>crud/js/backend.js"></script>


<script>
    jQuery(document).ready(function () {
        "use strict";
        // Init Theme Core
        Core.init();

        <?php if($this->auth->get_id()){ ?>
        setInterval(session_check, 6000);
        function session_check() {
            $.ajax({
                url: '<?=base_url(sprintf('%s/login/is_logged/', $this->crud->get_prefix()));?>',
                success: function (data) {
                    if (data == 'yah!good') {
                    }
                    else if (data == 'nope!bad') {
                        location.replace('<?=base_url(sprintf('%s/login', $this->crud->get_prefix()));?>');
                    }
                },
                error: function (e) {
                    console.log(e)
                }
            });
        }
        <?php } ?>
    });
</script>
<div id="prompt-form" class="popup-basic bg-none mfp-with-anim mfp-zoomIn mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span id="prompt-form-title" class="panel-title"></span>
        </div>
        <!-- end .panel-heading section -->

        <form method="post" class="admin-form">
            <div class="panel-body p25">
                <div class="form-group">
                    <textarea cols="40" rows="3" id="prompt-form-text" class="form-control"></textarea>
                    <span id="prompt-form-help" class="mt5"></span>
                </div>
                <!-- end section -->
            </div>
            <!-- end .form-body section -->

            <div class="panel-footer text-right">
                <button type="button" onclick="prompt_cancel()" id="prompt-form-cancel" class="btn btn-default">取消</button>
                <button type="button" id="prompt-form-submit" class="btn btn-primary">送出</button>
            </div>
            <!-- end .form-footer section -->
        </form>
    </div>
    <!-- end: .panel -->
</div>