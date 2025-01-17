<script>
    



    $(document).ready(function () {
        $('.detailUser').on('click', function(res){
            res.preventDefault();
            let id = $(this).data('id');
            
            $.ajax({
                type: "GET",
                url: "{{ url('user') }}/" + id,
                success: function (response) {
                    window
                    .FlowbiteInstances
                    .getInstance('Modal', 'detailUserModal')
                    ?.show();

                    $('#detail_id').val(response.data.id);
                    $('#detail_username').val(response.data.username);
                    $('#detail_phone').val(response.data.phone);
                    $('#detail_email').val(response.data.email);
                    $('#detail_saldo').val(response.data.saldo);
                    $('#detail_level').val(response.data.level);
                    $('#detail_status').val(response.data.status);
                }
            });
            
        }); 
    });
</script>