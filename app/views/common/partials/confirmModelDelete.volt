<script>
    function confirmModelDelete(id) {
        ret = confirm('Are you sure you want to delete entry # ' + id + '?');
        if(ret) {
            document.getElementById('modelForm_' + id).submit();
        }
    }
</script>