<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $this->title; ?></title>

    <!--link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/google/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"-->

    <link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/fontawesome-free/css/all.min.css" type="text/css" >

	<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/adminlte/css/adminlte.min.css?v=3.2.0" type="text/css" >


	<script src="<?php echo __ROOT__;?>dist/plugins/jquery/jquery.min.js"></script>

	<script src="<?php echo __ROOT__;?>dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

	<script src="<?php echo __ROOT__;?>dist/plugins/adminlte/js/adminlte.min.js?v=3.2.0"></script>
	
	<script src="<?php echo __ROOT__;?>dist/plugins/fullcalendar/dist/index.global.js"></script>


	
	<!-- script src="<?php echo __ROOT__;?>dist/plugins/DataTables/DataTables-1.13.6/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/DataTables/DataTables-1.13.6/css/jquery.dataTables.min.css" type="text/css" / -->
	<script src="<?php echo __ROOT__;?>dist/plugins/DataTables-2.0.0/dataTables.min.js"></script>
	<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/DataTables-2.0.0/dataTables.min.css" type="text/css" />


	
	<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/bootstrap-sweetalert/1.0.1/sweetalert.css" integrity="sha512-f8gN/IhfI+0E9Fc/LKtjVq4ywfhYAVeMGKsECzDUHcFJ5teVwvKTqizm+5a84FINhfrgdvjX8hEJbem2io1iTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="<?php echo __ROOT__;?>dist/plugins/bootstrap-sweetalert/1.0.1/sweetalert.js" integrity="sha512-XVz1P4Cymt04puwm5OITPm5gylyyj5vkahvf64T8xlt/ybeTpz4oHqJVIeDtDoF5kSrXMOUmdYewE4JS/4RWAA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- script src="<?php echo __ROOT__;?>dist/plugins/sweetalert.min.js"></script -->

	<script src="<?php echo __ROOT__;?>dist/plugins/bootstrap-autocomplete/bootstrap-autocomplete.min.js"></script>




		
	<script src="<?php echo __ROOT__;?>dist/plugins/vis-timeline/standalone/umd/vis-timeline-graph2d.min.js"></script>
	<link href="<?php echo __ROOT__;?>dist/plugins/vis-timeline/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />


	<script type="text/javascript" src="<?php echo __ROOT__;?>dist/plugins/moment.js/2.29.0/moment.min.js"></script>
	<script src="<?php echo __ROOT__;?>dist/plugins/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="<?php echo __ROOT__;?>dist/plugins/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />


	<style>
		.bootstrap-autocomplete.show .dropdown-item:not(.dropdown-item:has(> b)) {
			display: none;
		}
	</style>


	<style>
		.vis-item.vis-dot {
			border-radius: 10px;
			border-width: 10px;
			border-color: red;
			/* border-color: #007bff; */
		}

		.vis-draft {
			border-color: #6c757d!important;
		}
		.vis-request {
			border-color: #007bff!important;
		}
		.vis-action {
			border-color: #ffc107!important;
		}
		.vis-return {
			border-color: #17a2b8!important;
		}
		.vis-close {
			border-color: #28a745!important;
		}
	</style>



	<script>
		const ROOT = "<?php echo __ROOT__; ?>";



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

		
		$(document).on("click",".btnCancelAdd",function(){
			let p = $(this).parent().parent();
			p.find('input').val('');
			p.addClass('d-none')

			p.parent().parent().parent().parent().parent().find('.btnAddRow').removeClass('d-none')
		});
		function init_tbl(main_data, _tblId, _columns_data, __columns_select, defSort){
			$(_tblId).data('columns_select', __columns_select);
			$(_tblId).data('columns_action', main_data.columns_action == undefined ? ['#'] : main_data.columns_action);
			$(_tblId).data('columns_file', main_data.columns_file == undefined ? [] : main_data.columns_file);
			$(_tblId).data('post_data', main_data.data);

			let _columns_select = get_option(__columns_select);
			let input_txt = '<input class="text-center form-control" readonly disabled/>';

			let btnEdit = '<button class="ml-1 mr-1 btn btn-info btnEdit"><i class="fa-regular fa-pen-to-square"></i></button>';
			let btnDelete = '<button class="ml-1 mr-1 btn btn-danger btnDelete"><i class="fa-regular fa-trash-alt"></i></button>';
			let btnSave = '<button class="ml-1 mr-1 btn btn-primary btnSave d-none"><i class="fa-regular fa-save"></i></button>';
			let btnCancel = '<button class="ml-1 mr-1 btn btn-secondary btnCancel d-none"><i class="fa fa-ban"></i></button>';
			let btnAdd = '<button class="ml-1 mr-1 btn btn-success btnAdd"><i class="fa fa-plus"></i></button>';
			
			btnEdit = '<a role="button" class="ml-3 mr-3 text-info btnEdit"><i class="fa-regular fa-pen-to-square"></i></a>';
			btnDelete = '<a role="button" class="ml-3 mr-3 text-danger btnDelete"><i class="fa-regular fa-trash-alt"></i></a>';
			// btnSave = '<a role="button" class="ml-2 mr-2 text-primary btnSave d-none"><i class="fa-regular fa-save"></i></a>';
			// btnCancel = '<a role="button" class="ml-2 mr-2 text-secondary btnCancel d-none"><i class="fa fa-ban"></i></a>';

			let btnAll = btnEdit + btnDelete + btnSave + btnCancel;
			$(_tblId).data('btnAll', btnAll);

			let btnCancelAdd = '<button class="ml-1 mr-1 btn btn-secondary btnCancelAdd d-none"><i class="fa fa-ban"></i></button>';

			var _aoColumns = [];
			let tmpAdd = '';

			let columns_box = [];
			_columns_data.forEach( function(j,i){
				let is_action = j.title == '#';
				let d = is_action ? null : j.name;
				let visible = j.visible == undefined ? true : j.visible;
				let title = j.title == undefined ? j.name : j.title;

				let column_box = _columns_select[j.name] == undefined ? input_txt : _columns_select[j.name] ;
				if( is_action ) column_box = btnAdd + btnCancelAdd;
				
				if( !j.read_only ) column_box = column_box.replace('readonly disabled', '')
				columns_box.push( column_box );

				if( visible ) tmpAdd += '<td class="text-center">'+column_box+'</td>'

				// https://legacy.datatables.net/usage/columns
				var temp_ = {
					"sTitle": title ,
					// 'columns_box': column_box,
					'bSortable': j.bSortable == undefined ? true : j.bSortable,
					'bSearchable': j.bSearchable == undefined ? true : j.bSearchable,
					'visible': visible,
					"mData": d,
					'className': 'text-center',
					'width': j.width == undefined ? null : j.width,
					'mRender': main_data.mRender != undefined ? eval(main_data.mRender) : function ( data, type, row, dt) {
						let __tblId = '#' + dt.settings.sTableId;
			
						let col_name = dt.settings.aoColumns[dt.col].mData;
						let col_title = dt.settings.aoColumns[dt.col].ariaTitle;
						
						let c_action = $(__tblId).data('columns_action');
						
						let res = '';
			
						if( c_action.includes(col_title) ){
							res = '<div data-id="'+(row.id==null?'':row.id)+'">'+ $(__tblId).data('btnAll') +'</div>'
						}else{
							res += '<div class="edit-data d-none"></div>';
							let _d = '';
							let c_sel = $(__tblId).data('columns_select');
							let c_file = $(__tblId).data('columns_file');
			
							if( c_sel[col_name]!=undefined ){
								if( c_sel[col_name][0].includes(row[col_name]) ){
									_d = c_sel[col_name][1][ c_sel[col_name][0].indexOf(row[col_name]) ];
								}
							}else if( c_file.includes(col_name) ){
								_d = row[col_name] == null || row[col_name] == '' ? '' : '<a href="<?php echo __ROOT__;?>'+row[col_name]+'" target="_BLANK">File</a>';
							}else{
								_d = (row[col_name]==null?'':row[col_name])
							}
							res += '<div class="ori-data">'+ _d +'</div>';
						}
						return res;
					},
				};
				_aoColumns.push(temp_);
			});
			$(_tblId).data('columns_box', columns_box);

			if( $.fn.dataTable.isDataTable(_tblId) ){
				$(_tblId).DataTable().ajax.reload();
				if( main_data.allowAddRow ) $(_tblId).find('.addRow').html( tmpAdd.replace(/d-none/g,'') );
				return new Promise(function(resolve, reject){ resolve(true); });
			}

			// console.log( _aoColumns );

			// https://datatables.net/reference/option/
			
			//if( $.fn.dataTable.isDataTable(_tblId) ) $(_tblId).DataTable().destroy();
			//$(_tblId).html('');

			$(_tblId).addClass('display w-100');

			return new Promise(function(resolve, reject){
				$(_tblId).DataTable({
					// data: main_data,
			
					layout: {
						topStart: function () {
							let toolbar = document.createElement('div');
							toolbar.innerHTML = '<button class="ml-1 mr-1 btn btn-success btnAddRow" onclick="'+ "$(this).addClass('d-none'); $(this).parent().parent().parent().parent().find('.addRow').removeClass('d-none');" +'"><i class="fa fa-plus"></i></button>';
							if( main_data.btn_add_row != undefined ){
								toolbar.innerHTML = main_data.btn_add_row;
							}
							if( !main_data.allowAddRow ) toolbar = '';
							return toolbar;
						},
						// top2Start: 'pageLength',
						// top2End: 'search',
						// topStart: 'info',
						// topEnd: 'paging',
						bottomStart: 'pageLength',
						bottom2: 'info',
					},

					"processing": true,
					"serverSide": main_data.paging == undefined ? true : main_data.paging,
					//"bDestroy": true,
					"bJQueryUI": true,
					"ajax": {
						'type': 'POST',
						'url': main_data.url,
						'data': function( d ){
							d.post_data = $(_tblId).data('post_data');
						},
					},
					paging: main_data.paging == undefined ? true : main_data.paging,
					aoColumns: _aoColumns,
					bAutoWidth: false,
					aaSorting: defSort,
					bSortCellsTop :true,
					deferRender: true,
					columnDefs: [
						{"targets": "_all", "className": "text-center"}, //not working
					],
					footerCallback: main_data.footerCallback || null,
					rowCallback: function(row,data,index){
						// $(row).attr("data-id",data["id"]);
					},
					createdRow: function( row, data, dataIndex ) {
						// $(row).attr("data-id",data["id"]);
						$(row).attr("data-index",dataIndex);
					},
					initComplete: function(settings, json) {
						if( main_data.allowAddRow ) $(_tblId).find('thead').append( '<tr class="d-none addRow">'+tmpAdd.replace(/d-none/g,'')+'</tr>' );
						resolve(true);
					},
				});
			});
		}


		function uploadFormFiles(frmdata, url){
			return new Promise( function(resolve, reject){
				$.ajax({
					url: url,
					type: 'POST',
					
					dataType:'json',
					data: frmdata,
					
					cache: false,
					contentType: false,
					processData: false,
					
					success:function(output){
						resolve(output);
					},
					error: function(x, t, e){
						reject(t);
					},

					xhr: function () {
					var myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload) {
						myXhr.upload.addEventListener('progress', function (e) {
						if (e.lengthComputable) {
							//
						}
						}, false);
					}
					return myXhr;
					}
				});
			});
		}
		

		function genAutoComplete(el, dd){
			el.autoComplete({
				resolver: 'custom',
				minLength: 1,
				events: {
					search: function (query, callback) {
						callback(dd);
					}
				}
			});
		}
		function convertSelectToAutoComplete(el){
			let dd = Array.from( el.find('option') ).map(function(j){ return { value: $(j).attr('value'), text: $(j).text() }; });

			el.autoComplete({
				resolver: 'custom',
				minLength: 1,
				events: {
					search: function (query, callback) {
						callback(dd);
					}
				}
			});
			// $('.xx').data().autoComplete._selectedItem;
		}

		function get_data_object(_fnName,_params){
			var _callback = typeof arguments[2] == 'function' ? arguments[2] : false;
			var _resultArray = [];
			$.ajax({
				url: ROOT+_fnName,
				type:'POST',
				dataType:'json',
				data:_params,
				async: _callback ? true : false,
				success:function(output){
					_resultArray = output;
					if( _callback ) _callback(output);
				},
				error: function (XMLHttpRequest, textStatus, errorTh_rown) {
					swal("Error", "get_data_object error for function "+_fnName, "warning");
				}
			});
			return _resultArray;
		}
		function get_a_menu(j){
			return '<a class="dropdown-item" href="'+(j.url==''||j.url==null?'#':ROOT + j.url)+'">'+j.text+'</a>'
		}
		function get_li_menu(j){
			// j.url = j.url == undefined ? '#' : j.url;
			j.icon_class = j.icon_class == '' || j.icon_class == null ? '' : j.icon_class;
			return `
				<li class="nav-item" menu-id="`+j.id+`">
					<a href="`+ (j.url==''||j.url==null?'#':ROOT + j.url) +`" class="nav-link">
						<i class="nav-icon `+ j.icon_class +`"></i>
						`+ j.text + `
					</a>
				</li>`;
		}
		function init_sidebar(menu, sub_menu, active_menu_id){
			return;

			menu.forEach( function(j){
				let m = get_li_menu(j, sub_menu);
				$('.sidebar').find('ul').eq(0).append(m);
			});
			sub_menu.forEach( function(j){
				let ul = $('[menu-id="'+j.parent_id+'"]').find('ul').eq(0);
				if( ul.length == 0 ){
					$('[menu-id="'+j.parent_id+'"]').append( '<ul class="nav nav-treeview"></ul>' );
					$('[menu-id="'+j.parent_id+'"]').find('p').eq(0).append( '<i class="right fas fa-angle-left"></i>' );
					ul = $('[menu-id="'+j.parent_id+'"]').find('ul').eq(0);
				}
				ul.append( get_li_menu(j) );
			});
			$('[menu-id="'+active_menu_id+'"]').find('a').eq(0).addClass('active');
			if( $('[menu-id="'+active_menu_id+'"]').parent().hasClass('nav-treeview') ){
				let ul_p = $('[menu-id="'+active_menu_id+'"]').parent().parent();
				ul_p.addClass('menu-open');
				ul_p.find('a').eq(0).addClass('active');
			}
		}
		function init_navbar(menu, sub_menu, active_menu_id){
			menu.forEach( function(j){
				let m = get_li_menu(j, sub_menu);
				$('.mynav').append(m);
			});
			
			sub_menu.forEach( function(j){
				let e = $('[menu-id="'+j.parent_id+'"]');
				if( !e.hasClass('dropdown') ){
					e.addClass('dropdown');
					let a = e.find('a');
					a.eq(0).addClass('dropdown-toggle');
					a.attr('id','dropdown_'+j.parent_id);
					a.attr('role','button');
					a.attr('data-toggle','dropdown');
					a.attr('aria-haspopup','true');
					a.attr('aria-expanded','false');

					e.append('<div class="dropdown-menu" aria-labelledby="'+'dropdown_'+j.parent_id+'" style="left: 0px; right: inherit;"></div>');
				}
				e.find('[class="dropdown-menu"]').append( get_a_menu(j) );
			});
		}
	</script>
	
	<style>
		.brand-link {
			transition: none !important;
		}
	</style>

</head>

	<!-- <body class="hold-transition sidebar-mini sidebar-collapse"> -->
	<body class="hold-transition layout-top-nav layout-fixed layout-navbar-fixed">
		<div class="wrapper">


			<!-- http://material-dashboard-lite.creativeit.io/ -->


			<nav class="main-header navbar navbar-expand navbar-white navbar-light">

				<ul class="navbar-nav mynav">
<!-- 
					<li class="nav-item">
						<a href="index3.html" class="nav-link"><i class="mr-1 fa fa-user"></i>Home</a>
					</li>
					<li class="nav-item">
						<a href="#" class="nav-link">Contact</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Help
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown" style="left: 0px; right: inherit;">
							<a class="dropdown-item" href="#">FAQ</a>
							<a class="dropdown-item" href="#">Support</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="#">Contact</a>
						</div>
					</li> -->

					<!-- <li class="nav-item">
						<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>
					<li class="nav-item"> -->
						<!-- <a href="dashboard" class="nav-link">Dashboard</a> -->
						<!-- <div class="h-100 d-flex align-items-center"><?php echo $this->title; ?></div>
					</li> -->
				</ul>

				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a href="<?php echo __ROOT__;?>signout" class="nav-link"><i class="mr-1 fa fa-power-off"></i></a>
					</li>
					<!-- <li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="far fa-comments"></i>
							<span class="badge badge-danger navbar-badge">3</span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<a href="#" class="dropdown-item">

								<div class="media">
									<img src="<?php echo __ROOT__;?>dist/plugins/adminlte/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											Brad Diesel
											<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
										</h3>
										<p class="text-sm">Call me whenever you can...</p>
										<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>

							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">

								<div class="media">
									<img src="<?php echo __ROOT__;?>dist/plugins/adminlte/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											John Pierce
											<span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
										</h3>
										<p class="text-sm">I got your message bro</p>
										<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>

							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">

								<div class="media">
									<img src="<?php echo __ROOT__;?>dist/plugins/adminlte/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											Nora Silvester
											<span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
										</h3>
										<p class="text-sm">The subject goes here</p>
										<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>

							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
						</div>
					</li>

					<li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="far fa-bell"></i>
							<span class="badge badge-warning navbar-badge">15</span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<span class="dropdown-header">15 Notifications</span>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-envelope mr-2"></i> 4 new messages
								<span class="float-right text-muted text-sm">3 mins</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-users mr-2"></i> 8 friend requests
								<span class="float-right text-muted text-sm">12 hours</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fas fa-file mr-2"></i> 3 new reports
								<span class="float-right text-muted text-sm">2 days</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
						</div>
					</li> -->
				</ul>
			</nav>






			
			



			<aside class="d-none main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">

				<a href="<?php echo __ROOT__;?>dashboard" class="brand-link">
					<img src="<?php echo __ROOT__;?>dist/plugins/adminlte/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
					<span class="brand-text font-weight-light">AdminLTE 3</span>
				</a>

				<div class="sidebar">

					<nav class="mt-2">
						<ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">

							<!-- <li class="nav-item menu-open">
								<a href="#" class="nav-link active">
									<i class="nav-icon fas fa-tachometer-alt"></i>
									<p>
										Starter Pages
										<i class="right fas fa-angle-left"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<li class="nav-item">
										<a href="#" class="nav-link active">
											<i class="far fa-circle nav-icon"></i>
											<p>Active Page</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="#" class="nav-link">
											<i class="far fa-circle nav-icon"></i>
											<p>Inactive Page</p>
										</a>
									</li>
								</ul>
							</li>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-th"></i>
									<p>
										Simple Link
										<span class="right badge badge-danger">New</span>
									</p>
								</a>
							</li> -->
							
						</ul>
					</nav>

				</div>

				
				<div class="sidebar mt-0" style="border-top: solid 1px gray;">
					<nav>
						<ul class="nav nav-pills nav-sidebar flex-column mt-1">
							<li class="nav-item mt-2">
								<a href="<?php echo __ROOT__;?>signout" class="nav-link"><i class="nav-icon fas fa-sign-out"></i><p>Signout</p></a>
							</li>
						</ul>
					</nav>
				</div>

			</aside>
			


<!-- sidebar -->
<script>
	function init_side_bar(master_menu){
		let menu = master_menu.filter(function(j){ return j.parent_id==''; });;
		let sub_menu = master_menu.filter(function(j){ return j.parent_id!=''; });;
		
		let current_page = menu.concat(sub_menu).filter(function(j){ return ROOT+j.url==location.origin + location.pathname; });
		let active_menu_id = 1;
		if( current_page.length == 1 ) active_menu_id = current_page[0].id;

		// init_sidebar(menu, sub_menu, active_menu_id);
		init_navbar(menu, sub_menu, active_menu_id);

		$('.content-wrapper').removeClass('d-none');
	}
		
	get_data_object('index/get_menu', null, init_side_bar);
</script>
<!-- sidebar -->


<style>
	.table-bordered.tbcard {
		border: 0 !important;
	}
	.tbcard thead {
		display: none;
	}

	.tbcard tbody tr {
		float: left;
		width: 25em;
		margin: 0.5em;
		border: 1px solid #bfbfbf;
		border-radius: 0.5em;
		background-color: transparent !important;
		box-shadow: 0.25rem 0.25rem 0.5rem rgba(0, 0, 0, 0.25);
	}
	.tbcard tbody tr td {
		display: block;
		border: 0 !important;
	}
</style>
			
			<div class="content-wrapper d-none">