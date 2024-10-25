<script src="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.contextMenu.min.js"></script>
<script src="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.ui.position.js"></script>
<link href="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.contextMenu.min.css" rel="stylesheet" type="text/css" />

<!-- <script src="<?php echo __ROOT__;?>dist/js/jquery-ui-1.11.0.js"></script>
<link href="<?php echo __ROOT__;?>dist/css/jquery-ui-1.11.0/jquery-ui.css" rel="stylesheet" type="text/css" /> -->





<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"></h1>
            </div>
        </div>
    </div>
</div>


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-0 col-xl-1"></div>
            <div class="col-lg-12 col-xl-10">
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
            <div class="col-lg-0 col-xl-1"></div>
        </div>
    </div>
</div>

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
        tb = 'project';
        if( tblId == '#tblpermission' ) tb = 'project_permission';

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

            get_data_object('admin/project/save_' + tb, data, function(res){
                if(res.result){
                    if( tblId == '#tblpermission' ){
                        init_tbl_permission();
                    }else{
                        init_tbl_main();
                    }
                    swal('Done', '', 'success');
                }else{
                    swal(data.action + ' error', res.message);
                }
            });
        });
    });
}






function init_tbl_main(){
    const tblId = '#tblmain';
    let columns_data = [
        {
            name: 'project_number',
            title: 'Project',
            width: '60px',
        },
        {
            name: 'project_desc',
            title: 'Name',
            width: '250px',
        },
        {
            name: 'id',
            title: '#',
            width: '20px',
            bSearchable: false,
            bSortable: false,
        },
    ]
    
    let columns_select = {};
    
    let defSort = [];

    let main_data = {
        url: 'project/get_project_aadata',
        data: {},
        allowAddRow: true,
        paging: false,
    };
    
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, defSort);
}


$(document).ready( function(){
    init_tbl_main();
});




</script>








<div class="modal" id="mdSetUser" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Project Permisssion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tblpermission"></table>
      </div>
    </div>
  </div>
</div>

<script>
    function init_tbl_permission(){
        project_id = $('#mdSetUser').attr('project_id');
        // pp = get_data_object('admin/project/get_project_permission', {project_id: project_id});


        const tblId = '#tblpermission';
        let columns_data = [
            {
                name: 'email',
                title: 'Email',
                width: '150px',
            },
            {
                name: 'id',
                title: '#',
                width: '50px',
                bSearchable: false,
                bSortable: false,
            },
        ]
        
        let columns_select = {};
        
        let defSort = [];
    
        let main_data = {
            url: 'project/get_project_permission_aadata',
            data: {
                project_id: project_id
            },
            allowAddRow: true,
            paging: false,
            btn_add_row: '<div><button class="btn btn-success" data-toggle="modal" data-target="#mdAddUser"><i class="fa fa-plus"></i></button></div>',
        };
        
        init_listener(tblId, columns_data);
        init_tbl(main_data, tblId, columns_data, columns_select, defSort);
    }
    $(document).on('show.bs.modal','#mdSetUser',function(e){
        init_tbl_permission();
    });

    $(document).on('hide.bs.modal','#mdSetUser',function(e){
        // $('#mdSetUser').find('[name="ws_user_text"]').val('');
    });


    function load_contextMenu(){
        $.contextMenu({
            selector: '#tblmain tr', 
            callback: function(key, options) {
                project_id = this.find('[data-id]').attr('data-id');
                if( project_id == undefined ) return;
                
                $('#mdSetUser').attr('project_id', project_id);
                $('#mdSetUser').modal('show');
            },
            items: {
                "setUser": {name: "Set User", icon: "addiso"},
            }
        });
    }
    load_contextMenu()
</script>



<style>
    li.context-menu-icon:before {
        content: "";
        width: 16px;
        height: 16px;
        position: absolute;
        left: 8px;
        top: 14px;
    }
    .context-menu-icon-addiso:before {
        background-image: url(../dist/img/page_white_add.png);
    }

    #tblpermission .btnEdit {
        display: none;
    }
</style>







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
        ws_user = get_data_object('admin/project/get_all_user', null);

        op =  '';
        ws_user.forEach( function(j){
            op += '<option value="'+j.value+'">'+j.text+'</option>';
        })
        $('[name="ws_user"]').html(op);
        convertSelectToAutoComplete( $('[name="ws_user"]') );
        $('[name="ws_user_text"]').val('');
    });
    $(document).on('hide.bs.modal','#mdAddUser',function(e){
        // $('#mdAddUser').find('[name="ws_user_text"]').val('');
    });
    $(document).on('click', '.btnAddUser', function(e){
        user_id = $('[name="ws_user"]').val();
        get_data_object('admin/project/save_project_permission', {action: 'insert_project_permission', user_id: user_id, project_id: $('#mdSetUser').attr('project_id')}, function(res){
            if( res.result ){
                $('#mdAddUser').modal('hide');
                init_tbl_permission();
            }else{
                swal('Add error', res.message);
            }
        });
    });
</script>