<script src="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.contextMenu.min.js"></script>
<script src="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.ui.position.js"></script>
<link href="<?php echo __ROOT__;?>dist/plugins/jQuery-contextMenu-2.9.2/dist/jquery.contextMenu.min.css" rel="stylesheet" type="text/css" />

<script src="<?php echo __ROOT__;?>dist/js/jquery-ui-1.11.0.js"></script>
<link href="<?php echo __ROOT__;?>dist/css/jquery-ui-1.11.0/jquery-ui.css" rel="stylesheet" type="text/css" />



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
        <div class="col-lg-12">
                

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">DOCUMENT</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <table id="tblmain">
                            
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center">SUMMARY</th>		
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                </tr>
                            </tfoot>
                            
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="card iso-card d-none">
                <div class="card-header d-flex justify-content-center">
                    <h3 class="card-title document-title">ISO</h3>
                    <!-- <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div> -->
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center tab-content">
                        <div class="tab-pane div_iso_add">
                            <div id="div_iso_add"></div>
                        </div>
                        <div class="tab-pane div_iso_update">
                            <div id="div_iso_update"></div>
                        </div>
                        <div class="tab-pane div_iso_issue">
                            <div id="div_iso_lot" class="pb-3">

                                <div class="form-group">
                                    <button class="mr-3 btn btn-success" onclick="create_iso_lot()">Create New Lot</button>
                                </div>
                                <div class="form-inline iso_lot">
                                    <div class="form-group">
                                        <select id="select_iso_lot" class="form-control" style="min-width: 200px"></select>
                                    </div>
                                    <div class="form-group">
                                        <label class="mr-2 ml-4">Issue Date :</label>
                                        <input readonly="readonly" type="text" class="form-control keyInDate" id="iso_lot_date_issue" placeholder="Date">
                                    </div>
                                    <div class="form-group">
                                        <label class="mr-2 ml-4">Title :</label>
                                        <input type="text" class="form-control" id="iso_lot_title" placeholder="Lot Title" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <button class="ml-3 btn btn-primary" onclick="update_iso_lot()">Update Lot Title & Date</button>
                                        <button class="ml-3 btn btn-danger" onclick="delete_iso_lot()">Delete Lasted Lot</button>
                                    </div>
                                    <!-- <div class="form-group">
                                        <button class="ml-3 btn btn-primary">Export</button>
                                    </div> -->
                                </div>
                            
                            </div>
                            <div id="div_iso_issue" class="pt-3"></div>
                        </div>
                        <div class="tab-pane div_iso_export"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>


<style>
    input[readonly="readonly"][class*="hasDatepicker"] {
        background-color: white !important;
    }
    [id="ui-datepicker-div"] {
        z-index: 9999 !important;
    }
</style>

<script>



function init_listener(tblId, column_show){
if( $._data(document, "events").click.map(function(j){return j.selector;}).includes(tblId + " .btnEdit") ) return;

$(document).on("click",tblId + " .btnEdit",function(){
    $('#mdEditDocument').find('[name="id"]').val( $(this).parent().data('id') );
    
    $("#mdEditDocument").modal().show();
});
$(document).on("click",tblId + " .btnCancel",function(){
    let p = $(this).parent().parent().parent();
    
    p.find('[class*="edit-data"]').html('');

    showEditBtn(p, true);
    showEditForm(p, true);
});
$(document).on("click",[tblId + " .btnAdd", tblId + " .btnDelete", tblId + " .btnSave"].join(','),function(){
    tb = 'document';
    let p = $(this).parent().parent().parent();
    let data = get_box_value(p, column_show);

    data.id = $(this).parent().attr('data-id');
    data.project_id = $('#selProject').find(":selected").val();

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
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        get_data_object('app/iso/save_' + tb, data, function(res){
            if(res.result){
                init_tbl_main();
                swal("Done", '', "success");
            }else{
                swal(data.action + ' error', res.message, 'error');
            }
        });
    });
});
}






function init_tbl_main(){
    const tblId = '#tblmain';


    let columns_data = [
        {
            name: 'document_number',
            title: 'Doc. Number',
        },
        {
            name: 'document_desc',
            title: 'Doc. Description',
        },
        {
            name: 'iso',
            title: 'Total Line',
        },
        {
            name: 'iso_issued',
            title: 'Line Issued',
        },
        {
            name: 'iso_issued_pct',
            title: 'PCT',
            width: '150px',
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

    c_id =  {project_id: $('#selProject').find(":selected").val()};
    if( c_id.project_id == '' ) c_id = {};

    let main_data = {
        url: 'iso/get_document_aadata',
        data: c_id,
        columns_file: [],
        allowAddRow: true,
        paging: false,
        footerCallback: function( row, data, start, end, display ){
            let api = this.api();

            let intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            let lineTotal = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            let lineIssuedTotal = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            if( lineTotal == 0 ) lineTotal = '';
            if( lineIssuedTotal == 0 ) lineIssuedTotal = '';

            let pct = '';
            if( lineTotal != '' && lineTotal != '' ) pct = Math.round(lineIssuedTotal*100/lineTotal) + '%';

            $( api.column( 2 ).footer() ).html( lineTotal );
            $( api.column( 3 ).footer() ).html( lineIssuedTotal );
            $( api.column( 4 ).footer() ).html( pct );
        },
        btn_add_row: '<div class="d-inline-flex mr-3"><select id="selProject" style="width:350px" class="form-control">'
                    + '<option value="">---- Select Project ----</option>'
                    + '</select>'
                    + '</div>'
                    + '<div class="d-inline-flex">'
                    + '<button class="btn btn-primary d-none mr-3" data-toggle="modal" data-target="#mdEditRev">Set ISO Revision</button>'
                    + '<button class="btn btn-success d-none" data-toggle="modal" data-target="#mdEditDocument"><i class="fa fa-plus"></i> Add Document</button>'
                    + '</div>',
    };

    init_listener(tblId, columns_data);
    return new Promise( function(resolve,reject){
        init_tbl(main_data, tblId, columns_data, columns_select, [])
        .then( function(res){
            resolve(1);
        });
    })
}




$(document).ready( function(){

    init_tbl_main()
    .then( function(res){
        let projects = get_data_object('app/iso/get_project', null);
        
        projects.forEach( function(j){
            proj = '<option value="'+j.id+'">' + j.project_number + ' : ' + j.project_desc +'</option>';
            $('#selProject').append(proj);
        })
    });

    $('#selProject').on( 'change', function(){
        $('.iso-card').addClass('d-none')
    })
});





</script>






<div class="modal" id="mdEditRev" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ISO Revision</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row mb-3">
                    <label class="col-md-4 col-form-label text-right" for="inputdocument_number">1st</label>
                    <div class="col-md-4">
                        <input name="rev_name|1" class="form-control" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-4 col-form-label text-right" for="inputdocument_number">2nd</label>
                    <div class="col-md-4">
                        <input name="rev_name|2" class="form-control" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-4 col-form-label text-right" for="inputdocument_number">3rd</label>
                    <div class="col-md-4">
                        <input name="rev_name|3" class="form-control" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-4 col-form-label text-right" for="inputdocument_number">4th</label>
                    <div class="col-md-4">
                        <input name="rev_name|4" class="form-control" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-4 col-form-label text-right" for="inputdocument_number">5th</label>
                    <div class="col-md-4">
                        <input name="rev_name|5" class="form-control" />
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <div>
                    <button type="button" class="btn btn-primary btnSaveISORevision">Save ISO Revision</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mdEditDocument" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formDocument" enctype="multipart/form-data">
                    <input id="inputid" name="id" class="d-none form-control" style="display:none"/>
                    <input id="inputproject_id" name="project_id" class="d-none form-control" style="display:none"/>

                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label text-right" for="inputdocument_number">Number</label>
                        <div class="col-md-10">
                            <input id="inputdocument_number" name="document_number" class="form-control" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label text-right" for="inputdocument_desc">Description</label>
                        <div class="col-md-10">
                            <!--textarea id="inputdocument_desc" name="document_desc" class="form-control" style="height: 150px;"></textarea-->
                            <input id="inputdocument_desc" name="document_desc" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div>
                    <button type="button" class="btn btn-primary btnSaveDocument">Save Document</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>



$(document).on("click", ".deleteFile",function(){
    $(this).parent().addClass('d-none');
    $(this).parent().parent().find('input').removeClass('d-none');
});
$(document).on("change", "#selProject",function(){
    if( $(this).val() == '' ){
        $('[data-target="#mdEditRev"]').addClass('d-none');
        $('[data-target="#mdEditDocument"]').addClass('d-none');
    }else{
        $('[data-target="#mdEditRev"]').removeClass('d-none');
        $('[data-target="#mdEditDocument"]').removeClass('d-none');
    }
    init_tbl_main();
});

function load_iso_revision(){
    data = get_data_object('app/iso/get_iso_revision', {project_id: $('#selProject').find(":selected").val()});
    data.forEach( function(j){
        $('[name="rev_name|'+j.SEQUENCE+'"]').val(j.NAME);
        $('[name="rev_name|'+j.SEQUENCE+'"]').attr('data-id', j.id);
    });
}
$(document).on('show.bs.modal','#mdEditRev',function(e){
    load_iso_revision();
});
$(document).on('hide.bs.modal','#mdEditRev',function(e){
});

$(document).on('show.bs.modal','#mdEditDocument',function(e){
    $('#mdEditDocument').find('.modal-title').text( $('#selProject').find(":selected").text() );

    $('#mdEditDocument').find('[name="project_id"]').val( $('#selProject').find(":selected").val() );
    setMdDocument();
});
$(document).on('hide.bs.modal','#mdEditDocument',function(e){
    $('#mdEditDocument').find('input').val('');

    $('#mdEditDocument').find('input').removeClass('d-none');
});



$(document).on('click', '.btnSaveISORevision', function(e){
    swal({
        title: "Are you sure?",
        text: 'Update ISO Revision',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        data = $('#mdEditRev').find('input').map( function(i,j){
            return {id: $(j).attr('data-id'), name: $(j).val() };
        }).toArray();
        
        $.ajax({
            url: 'iso/save_iso_revision',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType:'json',
            async:false,
            success: function (output) {
                if( output["result"] ){
                    load_iso_revision();
                    swal('Done', '', 'success');
                }else{
                    swal('Save error', '', 'error');
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                swal('Save error', '', 'error');
            }
        });
    });
});


$(document).on('click', '.btnSaveDocument', function(e){
    data = {
        document_number: $('#mdEditDocument').find('#inputdocument_number').val(),
        document_desc: $('#mdEditDocument').find('#inputdocument_desc').val(),
        project_id: $('#mdEditDocument').find('#inputproject_id').val(),
        id: $('#mdEditDocument').find('#inputid').val(),
    }
    data.action = data.id == '' || data.id == null ? 'insert_document' : 'update_document';

    swal({
        title: "Are you sure?",
        text: data.action,
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        get_data_object('app/iso/save_document', data, function(res){
            if(res.result){
                if( data.action.includes('insert') ){
                    $('#mdEditDocument').find('#inputid').val(res.id);
                    data.id = res.id;
                }else{
                }
                swal('Done', '', 'success');
                init_tbl_main();
            }else{
                swal(data.action + ' error', res.message, 'error');
            }
        });
    });
});

function setMdDocument(){
    document_id = $('#mdEditDocument').find('[name="id"]').val();

    if( document_id == '' || document_id == null ){
    }else{
        doc = get_data_object('app/iso/get_document', {id: document_id});
        if( doc.length == 1 ){
            doc = doc[0];
            
            $('#mdEditDocument').find('#inputdocument_number').val(doc.document_number);
            $('#mdEditDocument').find('#inputdocument_desc').val(doc.document_desc);
        }
    }
}










function downloadFile(url) {
    swal({
        title: "Are you sure?",
        text: '',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        $('<a>', {
            href: url,
            download: ''
        }).get(0).click();
    });
}
function load_contextMenu(){
    $.contextMenu({
        selector: '#tblmain tr', 
        callback: function(key, options) {
            document_id = this.find('[data-id]').attr('data-id');
            if( document_id == undefined ){
                swal('No data', '', 'error');
                return;
            }

            if( key == 'download' ){
                downloadFile('<?php echo __ROOT__;?>uploads/Template_ISO_List.xlsm');
            }else{
                action_div_iso(key, document_id);
            }
        },
        items: {
            "add": {name: "Add ISO", icon: "addiso", disabled: function(){return is_action_div_iso;}},
            "update": {name: "Update ISO", icon: "updateiso", disabled: function(){return is_action_div_iso;}},
            "issue": {name: "Issue ISO", icon: "issueiso", disabled: function(){return is_action_div_iso;}},
            "download": {name: "Download Excel Template", icon: "exportiso", disabled: function(){return is_action_div_iso;}},
        }
    });
}

let is_action_div_iso = false;
load_contextMenu();


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
    .context-menu-icon-updateiso:before {
        background-image: url(../dist/img/page_white_edit.png);
    }
    .context-menu-icon-issueiso:before {
        background-image: url(../dist/img/save.png);
    }
    .context-menu-icon-exportiso:before {
        background-image: url(../dist/img/excel.png);
    }
</style>


<script src="<?php echo __ROOT__;?>dist/plugins/jexcel/jexcel.js"></script>
<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/jexcel/jexcel.css" type="text/css" />

<script src="<?php echo __ROOT__;?>dist/plugins/jsuites/jsuites.js"></script>
<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/jsuites/jsuites.css" type="text/css" />


<script>
    
var original_data = {};
var update_data = {};
function validate_update_data(key, newObj, y){
    if( original_data[key] == undefined ) return;

    var oldObj = original_data[key].filter(function(j){ return j.id == newObj.id });
    if( key == 'div_iso_issue' ){
        if( newObj.id == '' ){
            oldObj = original_data[key].filter(function(j){ return j.iso_dwg_id == newObj.iso_dwg_id && j.sheet == newObj.sheet });
        }else{
            oldObj = original_data[key].filter(function(j){ return j.id == newObj.id });
        }
    }
    if( oldObj.length != 1 ) return;

    oldObj = oldObj[0];
    if( key == 'div_iso_issue' ){
        isRevise = false;
        kk = newObj.iso_dwg_id + '_' + newObj.sheet;
        ['issue_remark', 'is_issue'].forEach( function(k){
            if( !isRevise ){
                isRevise = oldObj[k] != newObj[k] || (oldObj[k] == null && newObj[k] != '');
            }
        });
        if( isRevise ){
            update_data[key][kk] = newObj;
        }else{
            delete(update_data[key][kk]);
        }
    }else{
        if(JSON.stringify(oldObj) == JSON.stringify(newObj)){
            delete(update_data[key][newObj.id]);
        }else{
            if( newObj.id != '' && newObj.id != null ){
                update_data[key][newObj.id] = newObj;
            }
        }
    }
}

function delete_iso_lot(){
    swal({
        title: "Are you sure?",
        text: 'Delete Lasted Lot',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        get_data_object('app/iso/save_iso_lot', {
            action: 'delete_iso_lot',
            id: $('[id="select_iso_lot"]').val(),
        }, function(){
            load_iso_lot();
            action_div_iso('issue', $('.tab-content').attr('document_id'));
            swal("Done", '', "success");
        });
    });
}
function create_iso_lot(){
    swal({
        title: "Are you sure?",
        text: 'Create New Lot',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        get_data_object('app/iso/save_iso_lot', {
            action: 'insert_iso_lot',
            document_id: $('.tab-content').attr('document_id'),
            title: 'New Lot',
            date_issue: $.datepicker.formatDate('yy-mm-dd', new Date()),
        }, function(){
            action_div_iso('issue', $('.tab-content').attr('document_id'));
            swal("Done", '', "success");
        });
    });
}
function update_iso_lot(){
    swal({
        title: "Are you sure?",
        text: 'Update data',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        get_data_object('app/iso/save_iso_lot', {
            action: 'update_iso_lot',
            id: $('[id="select_iso_lot"]').val(),
            title: $('#iso_lot_title').val(),
            date_issue: $.datepicker.formatDate('yy-mm-dd',$( "#iso_lot_date_issue").datepicker('getDate')),
        }, function(){
            load_iso_lot();
        });
    });
}
function load_iso_lot(){
    document_id = $('.tab-content').attr('document_id');
    iso_lot = get_data_object('app/iso/get_iso_lot', {document_id: document_id});
    iso_lot_length = iso_lot.length
    if( iso_lot_length == 0 ){
        $('.iso_lot').addClass('d-none');
        $('#div_iso_issue').addClass('d-none');
        $('[onclick="delete_iso_lot()"]').addClass('d-none');
    }else{
        $('.iso_lot').removeClass('d-none');
        $('#div_iso_issue').removeClass('d-none');
        $('[onclick="delete_iso_lot()"]').removeClass('d-none');
        $('#select_iso_lot').html('');
        iso_lot_length = iso_lot_length - 1
        iso_lot.forEach( function(j, i){
            disabled = i == iso_lot_length ? '' : 'disabled'
            lot = '<option value="'+j.id+'" '+disabled+'>' + j.date_issue + ' - ' + j.title +'</option>';
            $('#select_iso_lot').append(lot);
        });
        $('#iso_lot_title').val( iso_lot[iso_lot_length].title );
        $("#iso_lot_date_issue").datepicker({ dateFormat : "dd-M-y"}).datepicker('setDate', new Date(iso_lot[iso_lot_length].date_issue) );
    }
}
function action_div_iso(method, document_id){
    jSuites.loading.show();
    is_action_div_iso = true;
    $('.iso-card').removeClass('d-none')
    $('.tab-content').attr('document_id', '');

    Object.keys(COLUMNS).forEach( function(k){
        $('.' + k ).removeClass('active');
    });

    div = $('.div_iso_' + method);
    div.addClass('active');


    $('.tab-content').attr('document_id', document_id);

    td = $('#tblmain').find('[data-id="'+document_id+'"]').parent().parent().find('td')
    text = td.eq(0).text() + ' - ' + td.eq(1).text();
    $('.document-title').text(text)

    let iso_dwg = [];
    if( method == 'add' ){
        is_action_div_iso = false;
        jSuites.loading.hide();
    }else if( method == 'update' ){
        // if( JSPREADSHEET['div_iso_' + method] != undefined ) JSPREADSHEET['div_iso_' + method].setData( iso_dwg );
        get_data_object('app/iso/get_iso_dwg', {document_id: document_id}, function(iso_dwg){
            original_data.div_iso_update = JSON.parse( JSON.stringify( iso_dwg ) );
            update_data.div_iso_update = {};
            if( JSPREADSHEET['div_iso_' + method] != undefined ) JSPREADSHEET['div_iso_' + method].setData( iso_dwg );
            
            is_action_div_iso = false;
            jSuites.loading.hide();
        });
    }else if( method == 'issue' ){
        // if( JSPREADSHEET['div_iso_' + method] != undefined ) JSPREADSHEET['div_iso_' + method].setData( iso_dwg );
        load_iso_lot();
        get_data_object('app/iso/get_iso_dwg_issue', {project_id: $('#selProject').find(":selected").val(), document_id: document_id, iso_lot_id: $('[id="select_iso_lot"]').val() }, function(iso_dwg){
            original_data.div_iso_issue = JSON.parse( JSON.stringify( iso_dwg ) );
            update_data.div_iso_issue = {};
            if( JSPREADSHEET['div_iso_' + method] != undefined ) JSPREADSHEET['div_iso_' + method].setData( iso_dwg );

            is_action_div_iso = false;
            jSuites.loading.hide();
        });
    }
}

$(document).ready( function(){
    for( let j in COLUMNS ){
        JSPREADSHEET[j] = jspreadsheet(document.getElementById(j), {
            data: [],
            columns: COLUMNS[j].columns,
            contextMenu: COLUMNS[j].contextMenu,

            updateTable: COLUMNS[j].updateTable || null,

            onload: function(instance){
                instance.jexcel.undo = function(){}
                instance.jexcel.redo = function(){}
            },

            minDimensions: [COLUMNS[j].columns.length, COLUMNS[j].minDimensions || 0 ],

            loadingSpin: true,
            paginationOptions: [100, 200, 500, 1000],
            pagination: 100,
            search: true,
            editable: true,
            columnDrag: false,
            allowExport: false,
            allowRenameColumn: false,
            allowDeleteColumn: false,
            allowInsertColumn: false,
            allowManualInsertColumn: false,

            allowDeleteRow: COLUMNS[j].allowDeleteRow || false,
            allowManualInsertRow: COLUMNS[j].allowManualInsertRow || false,
            allowInsertRow: COLUMNS[j].allowInsertRow || false,
            
            onchange:function(instance, cell, c, r, value) {
                if( j=='div_iso_issue' ){
                    if( c == 12 ){
                        instance.jspreadsheet.setReadOnly([13,r], value != 'Y');
                        if( value != 'Y' ){
                            instance.jspreadsheet.updateCell(13, r, '', true);
                        }
                    }
                }
                validate_update_data(j, instance.jspreadsheet.getJson()[r], r);
            },
            onbeforepaste: function(instance, data, x, y) {
                return data.replace(/\"/g, '″');
            }
        });
    }

});

function save_iso(opt){
    let cls = $('.tab-pane.active').attr('class');
    let method = '';
    let action = '';
    let document_id = $('.tab-content').attr('document_id');
    let params = {document_id: document_id};

    if( cls.includes('div_iso_add') ){
        method = 'add';
        
        params.data = JSPREADSHEET['div_iso_add'].getJson().filter( function(j){
            return Object.values(j).join('') != '';
        });
        params.action = 'insert_iso_dwg'
    }else if( cls.includes('div_iso_update') ){
        method = 'update';

        params.action = 'update_iso_dwg';
        params.data = update_data['div_iso_update']
    }else if( cls.includes('div_iso_issue') ){
        method = 'issue';

        params.action = 'update_iso_issue'
        params.iso_lot_id = $('[id="select_iso_lot"]').val();
        params.data = Object.values(update_data['div_iso_issue']);
    }
    
    if( opt ){
        params.action = opt.action;
        params.data = opt.data;
    }


    if( params.data.length == 0 || Object.keys(params.data).length == 0 ){
        swal( ('No data '+method+'ed').replace('ee','e'), '', 'error');
        return;
    }
    
    
    swal({
        title: "Are you sure?",
        text: '',
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false,
    },
    function(isConfirm){
        if( !isConfirm ) return;

        $.ajax({
            url: 'iso/save_iso_dwg',
            type: 'POST',
            data: JSON.stringify(params),
            contentType: "application/json; charset=utf-8",
            dataType:'json',
            async:false,
            success: function (output) {
                if( output["result"] ){
                    init_tbl_main();
                    action_div_iso(method, document_id);
                    swal("Done", '', "success");
                }else{
                    swal('Save error', '', 'error');
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                swal('Save error', '', 'error');
            }
        });
    });
}


let JSPREADSHEET = {};
const COLUMNS = {
    'div_iso_add': {
        columns: [
			{ type: 'hidden', width:'50', title:'ID', readOnly: true },
            { type: 'text', title:'Line ID', width: 300 },
            { type: 'text', title:'Line No.', width: 300 },
            { type: 'text', title:'P&ID', width: 200 },
            { type: 'text', title:'Area', width: 200 },
            { type: 'numeric', title:'Total Sheet', width: 100 },
        ],
        contextMenu: function(obj, x, y, e){
            items = []
            items.push({
                title: "Add ISO",
                onclick: function(){ save_iso(); },
            });
            return items;
        },
        allowInsertRow: true,
        allowManualInsertRow: true,
        minDimensions: 100,
    },
    'div_iso_update': {
        columns: [
			{ name: 'id', type: 'hidden', width:'50', title:'ID', readOnly: true },
            { name: 'line_id', type: 'text', title:'Line ID', width: 300 },
            { name: 'line_num', type: 'text', title:'Line No.', width: 300 },
            { name: 'pid', type: 'text', title:'P&ID', width: 200 },
            { name: 'area', type: 'text', title:'Area', width: 200 },
            { name: 'total_sheet', type: 'numeric', title:'Total Sheet', width: 100 },
        ],
        contextMenu: function(obj, x, y, e){
            items = []
            items.push({
                title: "Save",
                onclick: function(){ save_iso(); },
            });
            items.push({
                title: "Issue",
                onclick: function(){
                    action_div_iso('issue', $('.tab-content').attr('document_id'));
                },
            });
            return items;
        },
    },
    'div_iso_issue': {
        columns: [
			{ type: 'hidden', name: 'id', width:'50', title:'id', readOnly: true },
			{ type: 'hidden', name: 'iso_dwg_id', width:'50', title:'iso_dwg_id', readOnly: true },
			{ type: 'hidden', name: 'lasted_iso_lot_id', width:'50', title:'lasted_iso_lot_id', readOnly: true },
			{ type: 'hidden', name: 'lasted_iso_revision_id', width:'50', title:'lasted_iso_revision_id', readOnly: true },
			{ type: 'hidden', name: 'next_iso_revision_id', width:'50', title:'next_iso_revision_id', readOnly: true },
			{ type: 'hidden', name: 'area', width:'50', title:'area', readOnly: true },
			{ type: 'hidden', name: 'pid', width:'50', title:'pid', readOnly: true },
			{ type: 'hidden', name: 'total_sheet', width:'50', title:'pid', readOnly: true },
            { readOnly: true, name: 'line_id', type: 'text', title:'Line ID', width: 300 },
            { readOnly: true, name: 'line_num', type: 'text', title:'Line No.', width: 300 },
            { readOnly: true, name: 'sheet', type: 'text', title:'Sheet', width: 100 },
            { readOnly: true, name: 'CURRENT_ISO_REVISION', type: 'text', title:'Current Rev.', width: 100 },
            { type: 'dropdown', name: 'is_issue', title:'Issue', width: 100, source:['', 'Y'] },
            { type: 'text', name: 'issue_remark', title:'Remark', width: 300 },
        ],
        contextMenu: function(obj, x, y, e){
            items = []
            items.push({
                title: "Reload",
                onclick: function(){
                    swal({
                        title: "Are you sure?",
                        type: "warning",
                        showCancelButton: true,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: false,
                    },
                    function(isConfirm){
                        if( !isConfirm ) return;

                        action_div_iso('issue', $('.tab-content').attr('document_id'));
                    });
                },
            });
            items.push({
                title: "Save",
                onclick: function(){ save_iso(); },
            });
            
            if( x == 11 ){
                row = obj.getJson()[y];
                if( row.is_issue == '' ){
                    items.push({
                        title: "Add Blank Revision",
                        onclick: function(){
                            opt = {
                                action: 'add_blank_revision',
                                data: [{
                                    iso_dwg_id: row['iso_dwg_id'],
                                    next_iso_revision_id: row['next_iso_revision_id'],
                                    sheet: row['sheet'],
                                    total_sheet: row['total_sheet']
                                }]
                            }
                            save_iso(opt)
                        },
                    });
                }

                if( row.lasted_iso_lot_id == 0 && row.CURRENT_ISO_REVISION != '' && row.is_issue == '' ){
                    items.push({
                        title: "Delete Blank Revision",
                        onclick: function(){
                            opt = {
                                action: 'delete_blank_revision',
                                data: [{
                                    iso_dwg_id: row['iso_dwg_id'],
                                    lasted_iso_revision_id: row['lasted_iso_revision_id'],
                                    sheet: row['sheet'],
                                    total_sheet: row['total_sheet']
                                }]
                            }
                            save_iso(opt)
                        },
                    });
                }
            }

            return items;
        },
        updateTable: function(instance, cell, col, row, val, label, cellName) {
            // if (col <= 11) {
            //     cell.style.backgroundColor = '#f3f3f3';
            // }
            if( col == 12 ){
                if( val == '' ){
                    $(cell.nextElementSibling).addClass('readonly');
                }
            }
            if( col == 11 ){
                row = instance.jspreadsheet.getJson()[row];
                if( row.lasted_iso_lot_id === 0 ){
                    cell.style.backgroundColor = '#c3c3c3c3';
                }else{
                    cell.style.backgroundColor = '#f3f3f3';
                }
            }
        },
    },
}




function set_keyInDate(){
    $(".keyInDate").datepicker({
        dateFormat:'dd-M-y'
        ,showButtonPanel:true
        ,onClose:function(e){
            var ev = window.event;
            if(ev.srcElement.innerHTML=="Clear"){
                this.value = "";
            }
        }
        ,closeText:'Clear'
    });
}
set_keyInDate();
</script>

<style>
.jexcel > tbody > tr > td.readonly {
    color: rgb(0 0 0 / 70%) !important;
    background-color: rgb(243, 243, 243) !important;
}

.jexcel_filter {
	display: flex;
    justify-content: space-between;
    margin-bottom: 0px !important;
    border-top: 1px solid #ccc !important;
    border-left: 1px solid #ccc !important;
    border-right: 1px solid #ccc !important;
    border-bottom: 1px solid #ccc !important;
    margin-right: 3px;
    margin-left: 1px;
    background-color: #f3f3f3;
}

.jloading::after {
    border-color: #5c5c5c !important;
    border-top-color: transparent !important;
}
.jloading {
    background-color: rgb(255 255 255 / 0%) !important;
}
</style>

