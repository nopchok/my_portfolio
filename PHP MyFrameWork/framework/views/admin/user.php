
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"></h1>
                <div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <!-- <div class="card-header">
                        <h3 class="card-title">Role</h3>
                    </div> -->
                    <div class="card-body">
                        <div>
                            <table id="tblmain"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<div class="modal" id="mdAddUser" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
            <label class="label-control">User</label>
            <select class="form-control" name="ws_user"></select>
          </div>
          <div class="row">
            <label class="label-control">Role</label>
            <select class="form-control" name="role_id"></select>
          </div>
      </div>
      <div class="modal-footer">
          <div>
            <button type="button" class="btn btn-primary btnAddUser">Add</button>
          </div>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).on('show.bs.modal','#mdAddUser',function(e){
        ws_user = get_data_object('admin/user/get_ws_user', null);

        op =  '';
        ws_user.forEach( function(j){
            op += '<option value="'+j.value+'">'+j.text+'</option>';
        })
        $('[name="ws_user"]').html(op);
        convertSelectToAutoComplete( $('[name="ws_user"]') );
    });
    $(document).on('hide.bs.modal','#mdAddUser',function(e){
        $('#mdAddUser').find('[name="ws_user_text"]').val('');
    });
    $(document).on('click', '.btnAddUser', function(e){
        user_id = $('[name="ws_user"]').val();
        get_data_object('admin/user/insert_ws_user', {user_id: user_id, role_id: $('[name="role_id"]').val()}, function(res){
            if( res.result ){
                $('#mdAddUser').modal('hide');
                init_tbl_main();
            }else{
                swal('Add error', res.message);
            }
        });
        
    });
</script>










<script>






function init_listener(tblId, columns_data){
    let column_show = columns_data.map(function(j){return j.name});
    if( $._data(document, "events").click.map(function(j){return j.selector;}).includes(tblId + " .btnEdit") ) return;

    $(document).on("click",tblId + " .btnEdit",function(){
        let p = $(this).parent().parent().parent();
        
        let idx = p.attr('data-index');
        let d = $(tblId).DataTable().row(idx).data();

        // let columns_box = $(tblId).DataTable().settings()[0].aoColumns;
        let columns_box = $(tblId).data('columns_box');
        columns_box.forEach( function(j,i){
            p.find('[class*="edit-data"]').eq(i).html(j);
        })
        

        set_box_value(p, d, column_show);
        showEditBtn(p, false);
        showEditForm(p, false);
    });
    $(document).on("click",tblId + " .btnCancel",function(){
        let p = $(this).parent().parent().parent();
        
        p.find('[class*="edit-data"]').html('');

        showEditBtn(p, true);
        showEditForm(p, true);
    });
    $(document).on("click",[tblId + " .btnAdd", tblId + " .btnDelete", tblId + " .btnSave"].join(','),function(){
        tb = 'user';
        let p = $(this).parent().parent().parent();
        let data = get_box_value(p, column_show);

        data.id = $(this).parent().attr('data-id');

        if( $(this).hasClass('btnAdd') || data.id == '' || data.id == null ){
            data.action = 'insert_' + tb;
        }else if( $(this).hasClass('btnDelete') ){
            data.action = 'delete_' + tb;
        }else{
            data.action = 'update_' + tb;
        }
        
        swal({
            title: "Are you sure?",
            text: data.action,
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        },
        function(isConfirm){
            if( !isConfirm ) return;

            get_data_object('admin/user/save_' + tb, data, function(res){
                if(res.result){
                    init_tbl_main();
                    swal('Done', '', 'success');
                }else{
                    swal(data.action + ' error', res.message);
                }
            });
        });
    });
}





function get_ddl_role_id(){
    let role = get_data_object('dev/role/get_role', null);
    
    $('[name="role_id"]').html('');
    role.forEach( function(j){
        op = '<option value="'+j.id+'">'+j.role+'</option>';
        $('[name="role_id"]').append(op)
    });

    id = role.map( function(j){return j.id} );
    text = role.map( function(j){return j.role} );
    return [id, text];
}

function init_tbl_main(){
    const tblId = '#tblmain';
    
    // let columns_show = ['username', 'first_name', 'last_name', 'email', 'role_id', '#'];
    // let columns_title = {'username': 'Username', 'first_name': 'First name', 'last_name': 'Last name', 'email': 'Email', 'role_id':'Role'};
    // let columns_select = {
    //     'role_id': get_ddl_role_id(),
    // };
    // let columns_readonly = ['username'];
    
    // let main_data = get_data_object('admin/user/get_user', null);
    
    // init_listener(tblId, columns_show);
    // init_tbl(main_data, tblId, columns_show, columns_title, columns_select, columns_readonly);
    // if( columns_readonly.length > 0 ) $(tblId).find('tr').eq(1).addClass('d-none');

    
    let columns_data = [
        {
            name: 'username',
            title: 'Emp. Code',
            read_only: true,
        },
        {
            name: 'first_name',
            title: 'First Name',
        },
        {
            name: 'last_name',
            title: 'Last Name',
        },
        {
            name: 'email',
            title: 'Email',
        },
        {
            name: 'role_id',
            title: 'Role',
        },
        {
            name: 'id',
            title: '#',
            width: '150px',
            bSearchable: false,
            bSortable: false,
        },
    ];
    let columns_select = {
        'role_id': get_ddl_role_id(),
    };

    let main_data = {
        url: 'user/get_user_aadata',
        data: {},
        allowAddRow: true,
        btn_add_row: '<div><button class="btn btn-success" data-toggle="modal" data-target="#mdAddUser">Add User</button></div>',
        paging: true,
    };
    
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, []);
}

$(document).ready( function(){
    init_tbl_main();
});




</script>
