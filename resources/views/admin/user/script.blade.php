<script src="{{ URL::asset('js/ajax.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.detailUser').on('click', function(res){
            res.preventDefault();
            let id = $(this).data('id');
            getAjaxById("user" + "/" + id, "detailUserModal",function(result){
                $('#modal_title').html("Create User");
                $('#detail_id').val(result.data.id);
                $('#detail_username').val(result.data.username);
                $('#detail_phone').val(result.data.phone);
                $('#detail_email').val(result.data.email);
                $('#detail_saldo').val(result.data.saldo);
                $('#detail_level').val(result.data.level);
                $('#detail_status').val(result.data.status);
            })
        }); 
        
        $('.editUser').on('click', function(res){
            res.preventDefault();
            let id = $(this).data('id');
            getAjaxById("user" + "/" + id, "userModal",function(result){
                $('#modal_title').html("Edit User");
                $('#id').val(result.data.id);
                $('#username').val(result.data.username);
                $('#phone').val(result.data.phone);
                $('#email').val(result.data.email);
                $('#saldo').val(result.data.saldo);
                $('#level').val(result.data.level);
                $('#status').val(result.data.status);
                $('#modal-btn').html("Update");

               
                $('#modal-btn').click(function (e)  { 
                    e.preventDefault()
                    console.log(result.data)
                    if(!$(this).val()){
                        alert(result.data.password);
                        $("#password").val(result.data.password);
                    }
                });
            });

            $("<input>").attr({ 
                name: "_method", 
                type: "hidden", 
                class: "hidden_input_form",
                value: "PUT" 
            }).appendTo("form"); 
            
            $("<input>").attr({ 
                name: "id", 
                type: "hidden", 
                class: "id",
                value: id 
            }).appendTo("form"); 

            $('#password_confirmation_col').hide();
            
            $("<span>").attr({
                class: "password_optional text-red-500"
            }).text('(Optional)').appendTo($('.password-label'))

            window.FlowbiteInstances.getInstance("Modal", "userModal")?.updateOnHide(function(){
                $('#username').val('');
                $('#phone').val('');
                $('#email').val('');
                $('#saldo').val('0');
                $('#level').val('basic');
                $('#status').val('active');
                
                $('.hidden_input_form').remove();
                $('.id').remove();
                $('.password_optional').remove();
                $('#modal-btn').html("Add User");
                $('#modal_title').html("Create User");
                $('#password_confirmation_col').show();
                
            });

        }); 
        
    });
</script>