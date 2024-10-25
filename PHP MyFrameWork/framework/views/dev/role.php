
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
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role</h3>
                    </div>
                    <div class="card-body">
                        <div>
                            <table id="tblmain"></table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Menu Permission</h3>
                    </div>
                    <div class="card-body">
                        <div>
                            <table id="tblpermission"></table>
                        </div>
                    </div>
                </div>
            </div>
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
        tb = tblId.includes('permission') ? 'permission' : 'role';
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

            get_data_object('dev/role/save_' + tb, data, function(res){
                if(res.result){
                    if( tb == 'role' ){
                        init_tbl_main();
                    }else{
                        init_tbl_permission();
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

    // let columns_show = ['role', '#'];
    // let columns_title = {'role': 'Role'};
    // let columns_select = {};
    // let columns_readonly = [];
    
    // let main_data = get_data_object('dev/role/get_role', null);
    
    // init_listener(tblId, columns_show);
    // init_tbl(main_data, tblId, columns_show, columns_title, columns_select, columns_readonly);

    let columns_data = [
        {
            name: 'role',
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
    let columns_select = {};

    let main_data = {
        url: 'role/get_role_aaData',
        data: {},
        allowAddRow: true,
        paging: false,
    };
    
    init_tbl_permission();
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, []);
    // .then(function(){
    //     init_tbl_permission();
    // });
}

function init_tbl_permission(){
    const tblId = '#tblpermission';
    
    let columns_data = [
        {
            name: 'menu_id',
            title: 'Menu',
            read_only: true,
        },
        {
            name: 'role_id',
            title: 'Role',
            read_only: true,
        },
        {
            name: 'permission_id',
            title: 'Permission',
        },
        {
            name: 'id',
            title: '#',
            width: '150px',
            bSearchable: false,
            bSortable: false,
        },
    ];
    
    ddl_role = get_data_object('dev/role/get_role', null);
    ddl_menu = get_data_object('dev/role/get_menu', null);

    let columns_select = {
        'permission_id': [['E','V','N'], ['Edit','View','None']],
        'menu_id': [
            ddl_menu.map(function(j){return j.id}),
            ddl_menu.map(function(j){return (j.parent==null?'':j.parent+' / ') + j.text;})
        ],
        'role_id': [
            ddl_role.map(function(j){return j.id;}),
            ddl_role.map(function(j){return j.role;})
        ],
    };

    let main_data = {
        url: 'role/get_permission_aadata',
        data: {},
        allowAddRow: false,
        paging: false,
    };
    
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, []);
}

$(document).ready( function(){
    init_tbl_main();
});




</script>
