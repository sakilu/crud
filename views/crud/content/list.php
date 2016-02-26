<?php $rand = uniqid(); ?>
<div class="panel-body pn">
    <table class="table table-condensed table-hover" id="datatable<?= $rand ?>" cellspacing="0" width="100%">
        <thead>
        <tr>
            <?php foreach ($this->crud->get(Crud::KEY_COLUMNS) as $column) { ?>
                <th style="<?= $column->get_width() ?>"><?= $column->get(AbstractColumn::KEY_DISPLAY) ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
            <?= str_repeat("<th></th>", count($this->crud->get(Crud::KEY_COLUMNS))); ?>
        </tr>
        </tfoot>
    </table>
</div>
<script>
    $(function () {
        <?php $count = 0;?>
        var submit_table<?=$rand?> = $('#datatable<?=$rand?>').DataTable({
            "iDisplayLength": 5,
            "aLengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            "data": <?=json_encode($this->crud->get_list_data())?>,
            "formatNumber": function ( toFormat ) {
                return toFormat.toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g, "'"
                );
            },
            "sDom": '<"dt-panelmenu clearfix"Tlrtp>t<"dt-panelfooter clearfix"ip>',
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                $('td', nRow).each(function (iPosition) {
                    var sCellContent = $(this).html();
                    sCellContent = '<div class="crud_list" style="width:' + $('th').eq(iPosition).css('width') + ';">' + sCellContent + '</div>';
                    $(this).html(sCellContent);
                });
                return nRow;
            },
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
                // Send an Ajax request to the server with the state object
                $.ajax({
                    "url": "<?=base_url(sprintf('/%s/state_list_save', $this->crud->get_module_url()))?>",
                    "data": data,
                    "dataType": "json",
                    "type": "POST",
                    "success": function () {

                    }
                });
            },
            "stateLoadCallback": function (settings) {
                var o;
                $.ajax({
                    "url": "<?=base_url(sprintf('/%s/state_list_load', $this->crud->get_module_url()))?>",
                    "async": false,
                    "dataType": "json",
                    "success": function (json) {
                        o = json;
                    }
                });
                return o;
            }
        });
        <?php $count = 0;?>
        yadcf.init(submit_table<?=$rand?>, [
            <?php foreach($this->crud->get(Crud::KEY_COLUMNS) as $column){ ?>
            <?=$column->get_yadcf_setting($count++)?>
            <?php } ?>
        ], 'tfoot');
    });
</script>