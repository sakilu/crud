<script>
    $(function(){
        $('[select_id="<?=$unique?>"]').multiselect({
            includeSelectAllOption: true,
            allSelectedText: '全選',
            nonSelectedText: '尚未選擇',
            selectAllText: '全選'
        });
    });
</script>