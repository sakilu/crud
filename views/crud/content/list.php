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
    var submit_table<?=$rand?>;
    $(function () {
        <?php $count = 0;?>

        submit_table<?=$rand?> = $('#datatable<?=$rand?>').DataTable({
            "iDisplayLength": 5,
            "aLengthMenu": [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            "data": <?=json_encode($this->crud->get_list_data())?>,
            "formatNumber": function (toFormat) {
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
    var excel<?=$rand?> = function () {
        var table = $('#datatable<?= $rand ?>')[0];
        var str = '';
        var line = '';

        for (var i = 0; i < table.rows[0].cells.length - 1; i++) {
            var value = table.rows[0].cells[i].innerText + "";
            line += '"' + value.replace(/"/g, '""') + '",';
        }
        line = line.slice(0, -1);
        str += line + '\r\n';

        var data = submit_table<?=$rand?>.rows({filter: 'applied'}).data();
        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            line = "";
            for (var j = 0; j < row.length - 1; j++) {
                var value = row[j] + "";
                value = value.replace(/\r\n|\n/g, "");
                line += '"' + value.replace(/"/g, '""') + '",';
            }
            line = line.slice(0, -1);
            str += line + '\r\n';
        }
        window.open("data:text/csv;charset=utf-8,%EF%BB%BF" + encodeURI(str))
    }
</script>