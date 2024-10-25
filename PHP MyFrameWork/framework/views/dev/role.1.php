
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
            <div class="col-lg-5">
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
            <div class="col-lg-7">
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
function set_box_value(p, d, columns_show){
    columns_show.forEach( function(j,i){
        v = d[j+'_id'] != undefined ? d[j+'_id'] : d[j]
        p.find('td').eq(i).find('input,select').val(v);
    });
}
function reset_box_value(p){
    p.find('input,select').val('');
}
function get_box_value(p, columns_show){
    let res = {}
    columns_show.forEach( function(j,i){
        res[j] = p.find('td').eq(i).find('input,select').val();
        res[j] = res[j] == '' ? '' : (isNaN( Number(res[j]) ) ? res[j] : Number(res[j]));
    });
    return res;
}
function showEditForm(r, a){
    r.find('td').each( function(i,p){
        p = $(p);
        if( p.find('[class*="edit-data"]').length == 0 ) return;
        if( a ){
            p.find('[class*="edit-data"]').addClass('d-none');
    
            p.find('[class*="ori-data"]').removeClass('d-none');
        }else{
            p.find('[class*="edit-data"]').removeClass('d-none');
    
            p.find('[class*="ori-data"]').addClass('d-none');
        }
    });
}
function showEditBtn(p, a){
    if( a ){
        p.find('[class*="btnSave"]').addClass('d-none');
        p.find('[class*="btnCancel"]').addClass('d-none');

        p.find('[class*="btnEdit"]').removeClass('d-none');
        p.find('[class*="btnDelete"]').removeClass('d-none');
    }else{
        p.find('[class*="btnSave"]').removeClass('d-none');
        p.find('[class*="btnCancel"]').removeClass('d-none');

        p.find('[class*="btnEdit"]').addClass('d-none');
        p.find('[class*="btnDelete"]').addClass('d-none');
    }
}
function get_option(dd){
    let res = {}
    for( let k in dd ){
        display = dd[k][1]
        value = dd[k][0];

        let parent_option = '';
        parent_option = '<select class="text-center form-control" readonly disabled>';
        display.forEach( function(j, i){
            parent_option += '<option value="'+value[i]+'">'+j+'</option>';
        })
        parent_option += '</select>';
        res[k] = parent_option;
    }
    return res;
}


function init_tbl(main_data, _tblId, _columns_show, _columns_title, __columns_select, _columns_readonly){
    let _columns_select = get_option(__columns_select);
    let input_txt = '<input class="text-center form-control" readonly disabled/>';

    let btnEdit = '<button class="ml-1 mr-1 btn btn-info btnEdit"><i class="fa-regular fa-pen-to-square"></i></button>';
    let btnDelete = '<button class="ml-1 mr-1 btn btn-danger btnDelete"><i class="fa-regular fa-trash-alt"></i></button>';
    let btnSave = '<button class="ml-1 mr-1 btn btn-primary btnSave d-none"><i class="fa-regular fa-save"></i></button>';
    let btnCancel = '<button class="ml-1 mr-1 btn btn-secondary btnCancel d-none"><i class="fa fa-ban"></i></button>';
    let btnAdd = '<button class="ml-1 mr-1 btn btn-success btnAdd"><i class="fa fa-plus"></i></button>';
    
    // btnEdit = '<a class="ml-1 mr-1 btn btn-warning btnEdit"><i class="fas fa-pencil"></i></a>';
    // btnDelete = '<a class="ml-1 mr-1 btn btn-danger btnDelete"><i class="fas fa-trash-alt"></i></a>';

    let btnAll = btnEdit + btnDelete + btnSave + btnCancel;

    let columns_box = {}
    _columns_show.forEach( function(j){
        columns_box[j] = _columns_select[j] == undefined ? input_txt : _columns_select[j] ;
        if( j == '#' ){
            columns_box[j] = btnAdd;
        }
        if( !_columns_readonly.includes(j) ) columns_box[j] = columns_box[j].replace('readonly disabled', '')
    });

    var _aoColumns = [];
    let tmpAdd = '';
    _columns_show.forEach( function(j,i){
        let is_action = j == '#';

        tmpAdd += '<td class="dt-center">'+columns_box[j]+'</td>'

        let d = is_action ? null : j;
        let title = _columns_title[j] == undefined ? j : _columns_title[j];
        // https://legacy.datatables.net/usage/columns
        var temp_ = {
            "sTitle": title ,
            'columns_box': columns_box[j],
            'bSortable': !is_action,
            'bSearchable': !is_action,
            "mData": d ,
            'width': is_action ? '150px': 'auto',
            'mRender': function ( data, type, row ) {
                let res = '';
                if( is_action ){
                    res = '<div data-id="'+(row.id==null?'':row.id)+'">'+btnAll+'</div>'
                }else{
                    res += '<div class="edit-data d-none"></div>';
                    let _d = '';
                    if( __columns_select[j]==null || __columns_select[j]==undefined ){
                        _d = (row[j]==null?'':row[j])
                    }else if( __columns_select[j][0].includes(row[j]) ){
                        _d = __columns_select[j][1][ __columns_select[j][0].indexOf(row[j]) ];
                    }else{ }
                    res += '<div class="ori-data">'+ _d +'</div>';
                }
                return res;
            }
        };
        _aoColumns.push(temp_);
    });

    // https://datatables.net/reference/option/
    if( $.fn.dataTable.isDataTable(_tblId) ) $(_tblId).DataTable().destroy();
    $(_tblId).html('');
    $(_tblId).DataTable({
        data: main_data,
        aoColumns: _aoColumns,
        bAutoWidth: false,
        aaSorting: [],
        bSortCellsTop :true,
        processing: true,
        deferRender: true,
        columnDefs: [
            {"className": "dt-center", "targets": "_all"}
        ],
        rowCallback: function(row,data,index){
            // $(row).attr("data-id",data["id"]);
        },
        createdRow: function( row, data, dataIndex ) {
            // $(row).attr("data-id",data["id"]);
            $(row).attr("data-index",dataIndex);
        },
        initComplete: function(settings, json) {
            $(_tblId).find('thead').append( '<tr>'+tmpAdd.replace(/d-none/g,'')+'</tr>' );
        },
    });

    $(_tblId).addClass('display w-100');
}



function init_listener(tblId, column_show){
    if( $._data(document, "events").click.map(function(j){return j.selector;}).includes(tblId + " .btnEdit") ) return;

    $(document).on("click",tblId + " .btnEdit",function(){
        let p = $(this).parent().parent().parent();
        
        let idx = p.attr('data-index');
        let d = $(tblId).DataTable().row(idx).data();

        let columns_box = $(tblId).DataTable().settings()[0].aoColumns;
        columns_box.forEach( function(j,i){
            p.find('[class*="edit-data"]').eq(i).html(j.columns_box);
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

            get_data_object('admin/role/save_' + tb, data, function(res){
                if(res.result){
                    if( data.action.includes('insert') ){
                        // reset_box_value(p);
                        // $(tblId).DataTable().row.add( data ).draw();
                        if( tb == 'role' ){
                            init_tbl_main();
                            init_tbl_permission();
                        }else{
                            init_tbl_permission();
                        }
                    }else if( data.action.includes('delete') ){
                        p.addClass('d-none');
                        // $(tblId).DataTable().row(p.attr('data-index')).remove().draw();
                    }else{
                        o = $(tblId).DataTable().row(p.attr('data-index'));
                        ori = o.data();
                        o.data( $.extend({}, ori, data) ).draw();
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

    let columns_show = ['role', '#'];
    let columns_title = {'role': 'Role'};
    let columns_select = {};
    let columns_readonly = [];
    
    let main_data = get_data_object('admin/role/get_role', null);
    
    init_listener(tblId, columns_show);
    init_tbl(main_data, tblId, columns_show, columns_title, columns_select, columns_readonly);
}

function init_tbl_permission(){
    const tblId = '#tblpermission';
    let columns_show = ['menu_id', 'role_id', 'permission_id', '#'];
    let columns_title = {'menu_id': 'Menu', 'role_id': 'Role', 'permission_id': 'Permission'};

    ddl_role = $('#tblmain').DataTable().data().toArray()
    ddl_menu = get_data_object('admin/role/get_menu', null);
    // let columns_select = {
    //     'permission_id': get_option(['Edit','View','None'], ['E','V','N']),
    //     'menu_id': get_option( ddl_menu.map(function(j){return (j.parent==null?'':j.parent+' / ') + j.text;}), ddl_menu.map(function(j){return j.id}) ),
    //     'role_id': get_option( ddl_role.map(function(j){return j.role;}), ddl_role.map(function(j){return j.id;}) ),
    // };
    columns_select = {
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
    let columns_readonly = ['menu_id', 'role_id'];
    
    let data = get_data_object('admin/role/get_permission', null);

    init_listener(tblId, columns_show);
    init_tbl(data, tblId, columns_show, columns_title, columns_select, columns_readonly);
    $(tblId).find('tr').eq(1).addClass('d-none');
}

$(document).ready( function(){
    init_tbl_main();
    init_tbl_permission();
});




</script>
