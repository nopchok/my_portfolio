
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
                        <h3 class="card-title">Section</h3>
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
                        <h3 class="card-title">Section User</h3>
                    </div>
                    <div class="card-body">
                        <div>
                            <table id="tblsection_user"></table>
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
        tb = tblId.includes('section_user') ? 'section_user' : 'section';
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

            get_data_object('admin/section/save_' + tb, data, function(res){
                if(res.result){
                    if( tb == 'section' ){
                        init_tbl_main();
                    }else{
                        init_tbl_section_user();
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

    // let columns_show = ['section', 'general_flag', '#'];
    // let columns_title = {'section': 'Section', 'general_flag': 'General (Y/N)'};
    // let columns_select = {};
    // let columns_readonly = [];
    
    // let main_data = get_data_object('admin/section/get_section', null);
    
    // init_listener(tblId, columns_show);
    // init_tbl(main_data, tblId, columns_show, columns_title, columns_select, columns_readonly);

    
    let columns_data = [
        {
            name: 'section',
            title: 'Section',
        },
        {
            name: 'general_flag',
            title: 'General (Y/N)',
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
        url: 'section/get_section_aadata',
        data: {},
        allowAddRow: true,
        paging: false,
    };
    
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, [])
    .then(function(res){
        init_tbl_section_user();
    });
}

function init_tbl_section_user(){
    const tblId = '#tblsection_user';
    // let columns_show = ['user_id', 'section_id', '#'];
    // let columns_title = {'user_id': 'User', 'section_id': 'Section'};

    ddl_section = get_data_object('admin/section/get_section', null);
    ddl_user = get_data_object('admin/user/get_user', null);

    columns_select = {
        'user_id': [
            ddl_user.map(function(j){return j.id;}),
            ddl_user.map(function(j){return j.email;})
        ],
        'section_id': [
            ddl_section.map(function(j){return j.id;}),
            ddl_section.map(function(j){return j.section;})
        ],
    };
    // let columns_readonly = ['user_id'];
    
    // let data = get_data_object('admin/section/get_section_user', null);

    // init_listener(tblId, columns_show);
    // init_tbl(data, tblId, columns_show, columns_title, columns_select, columns_readonly);
    // if( columns_readonly.length > 0 ) $(tblId).find('tr').eq(1).addClass('d-none');

    
    
    let columns_data = [
        {
            name: 'user_id',
            title: 'Email',
            read_only: true,
        },
        {
            name: 'section_id',
            title: 'Section',
        },
        {
            name: 'id',
            title: '#',
            width: '150px',
            bSearchable: false,
            bSortable: false,
        },
        {
            name: 'email',
            visible: false,
        },
        {
            name: 'section',
            visible: false,
        },
    ];

    let main_data = {
        url: 'section/get_section_user_aadata',
        data: {},
        allowAddRow: false,
        paging: true,
    };
    
    init_listener(tblId, columns_data);
    init_tbl(main_data, tblId, columns_data, columns_select, []);
}

$(document).ready( function(){
    init_tbl_main();
});




</script>
