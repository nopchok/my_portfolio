
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
        tb = 'menu';
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

            get_data_object('dev/menu/save_' + tb, data, function(res){
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






function get_ddl_parent_id(){
    let menu = get_data_object('dev/menu/get_menu', null);
    let parent_menu = menu.filter( function(j){return j.parent_id == '';} );
    parent_menu_id = [''].concat(parent_menu.map( function(j){return j.id.toString()} ));
    parent_menu_text = [''].concat(parent_menu.map( function(j){return j.text} ));
    return [parent_menu_id, parent_menu_text];
}

function init_tbl_main(){
    const tblId = '#tblmain';
    let columns_data = [
        {
            name: 'text',
            title: 'Menu',
            width: '150px',
        },
        {
            name: 'parent_id',
            title: 'Parent Menu',
            width: '150px',
        },
        {
            name: 'url',
            title: 'URL',
        },
        {
            name: 'icon_class',
            title: 'fa Icon Class',
        },
        {
            name: 'sequence',
            title: 'Sequence',
            width: '50px',
        },
        {
            name: 'id',
            title: '#',
            width: '150px',
            bSearchable: false,
            bSortable: false,
        },
    ]
    // let columns_title = {'text': 'Menu', 'parent_id':'Parent Menu', 'url': 'Url', 'icon_class': 'fa Icon Class', 'sequence': 'Sequence'};
    let columns_select = {
        'parent_id': get_ddl_parent_id(),
    };
    
    let defSort = [ [4,'asc'] ];

    let main_data = {
        url: 'menu/get_menu_aaData',
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
