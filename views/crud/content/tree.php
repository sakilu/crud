<div class="panel-body">
    <div class="row">
        <div class="col-xs-7">
            <div class="row">
                <div class="col-xs-8">
                    <input class="form-control" name="search" placeholder="搜尋">
                </div>
                <div class="col-xs-4">
                    <button class="btn btn-primary mr10 ph20" id="btnResetSearch" disabled="disabled">清除
                        <i class="fa fa-remove pl10"></i>
                    </button>
                    <span id="matches"></span>
                </div>
            </div>
            <hr class="short alt mv15">
            <table id="columnview">
                <colgroup>
                    <col width="33%">
                    <col width="33%">
                    <col width="33%">
                </colgroup>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-5">

        </div>
    </div>
</div>

<script>
    $(function () {
        $("#columnview").fancytree({
            extensions: ["columnview", "filter"],
            filter: {
                mode: "hide",
                autoApply: true
            },
            checkbox: true,
            source: <?=json_encode($tree)?>
        });
        var tree = $("#columnview").fancytree("getTree");
        $("input[name=search]").keyup(function (e) {
            var n, match = $(this).val();

            if (e && e.which === $.ui.keyCode.ESCAPE || $.trim(match) === "") {
                $("button#btnResetSearch").click();
                return;
            }
            n = tree.filterNodes(match, false);
            $("button#btnResetSearch").attr("disabled", false);
//            $("span#matches").text("(" + n + " matches)");
        });

        $("button#btnResetSearch").click(function (e) {
            $("input[name=search]").val("");
//            $("span#matches").text("");
            tree.clearFilter();
        }).attr("disabled", true);

        tree.options.filter.mode = "hide";
        tree.clearFilter();
    });
</script>