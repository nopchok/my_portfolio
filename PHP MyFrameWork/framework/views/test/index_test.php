

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content">
					
					
                <div class="container-fluid">




                    <div class="row">
                        <div class="col-lg-6">


							<div class="card">
								<div class="card-header">
									<h3 class="card-title">TEST</h3>
								</div>

								<div class="card-body">
									<div id="" style=""><?php print_r($_SESSION); ?></div>
								</div>
							</div>

							
							<div class="card">
								<div class="card-header">
									<h3 class="card-title">TEST</h3>
								</div>

								<div class="card-body">
									<table id="myTable"></table>
								</div>

								<script>
									$('#myTable').DataTable( {
										data: [
											[
												"Tiger Nixon",
												"System Architect",
												"Edinburgh",
												"5421",
												"2011/04/25",
												"$3,120"
											],
											[
												"Garrett Winters",
												"Director",
												"Edinburgh",
												"8422",
												"2011/07/25",
												"$5,300"
											]
										],
										columns: [
											{ title: 'Name' },
											{ title: 'Position' },
											{ title: 'Office' },
											{ title: 'Extn.' },
											{ title: 'Start date' },
											{ title: 'Salary' }
										],
									});
								</script>
							</div>

	
							


							<div class="card">
								<div class="card-header">
									<h3 class="card-title">Default Card Example</h3>
								</div>

								<div class="card-body">
									
									<p>
										<form id="upload-form" class="dropzone" action="xxx"></form>
									</p>
									<button class="btn bg-primary" type="submit">Upload files</button>

									<style>
										.dz-progress { display: none; }
									</style>
									<script>
										
									Dropzone.options.uploadForm = { // The camelized version of the ID of the form element
										
										// The configuration we've talked about above
										autoProcessQueue: false,
										uploadMultiple: true,
										parallelUploads: 100,
										maxFiles: 100,
										addRemoveLinks: true,
										
										// The setting up of the dropzone
										init: function() {
											var myDropzone = this;
										
											// First change the button to actually tell Dropzone to process the queue.
											this.element.parentNode.querySelector("button[type=submit]").addEventListener("click", function(e) {
												// Make sure that the form isn't actually being sent.
												e.preventDefault();
												e.stopPropagation();
												myDropzone.processQueue();
											});
										
											// Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
											// of the sending event because uploadMultiple is set to true.
											this.on("sendingmultiple", function() {
											// Gets triggered when the form is actually being sent.
											// Hide the success button or the complete form.
											});
											this.on("successmultiple", function(files, response) {
												console.log(files);
												files.forEach( function(file){
													console.log($(file._removeLink).addClass('d-none'));
												})
											// Gets triggered when the files have successfully been sent.
											// Redirect user or notify of success.
											});
											this.on("errormultiple", function(files, response) {
											// Gets triggered when there was an error sending the files.
											// Maybe show form again, and notify user of error
											});
											this.on("addedfile", function(file){
												console.log(99);
											});
										}
										
									}
									</script>
								</div>
							</div>






							<div class="card">
								<div class="card-header">
									<h3 class="card-title">Default Card Example</h3>
								</div>

								<div class="card-body pt-0">

<div id="external-events">
	<div class="external-event bg-success ui-draggable ui-draggable-handle" style="position: relative;">Lunch</div>
	<div class="external-event bg-warning ui-draggable ui-draggable-handle" style="position: relative;">Go home</div>
	<div class="external-event bg-info ui-draggable ui-draggable-handle" style="position: relative;">Do homework</div>
	<div class="external-event bg-primary ui-draggable ui-draggable-handle" style="position: relative;">Work on UI design</div>
	<div class="external-event bg-danger ui-draggable ui-draggable-handle" style="position: relative;">Sleep tight</div>
</div>
<script>
		const containerEl = document.getElementById('external-events');
		new FullCalendar.Draggable(containerEl, {
			itemSelector: '.external-event',
			eventData: function(eventEl) {
				return {
					title: eventEl.innerText,
					backgroundColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
					borderColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
					textColor: window.getComputedStyle( eventEl ,null).getPropertyValue('color'),
				};
			}
		});
</script>


									<div id="calendar"></div>
								</div>
								
								<script>
								let calendar;
								document.addEventListener('DOMContentLoaded', function() {
									const calendarEl = document.getElementById('calendar')
									calendar = new FullCalendar.Calendar(calendarEl, {
										lazyFetching: true,
										events: {
											url: ROOT + 'taskfeed',
											method: 'POST',
											extraParams: {
												custom_param1: 'something',
												custom_param2: 'somethingelse'
											},
											failure: function() {
												console.log('there was an error while fetching events!');
											},
										},
										// events: [
										// 	{
										// 		title: 'BCH237',
										// 		start: '2023-08-01',
										// 		end: '2023-08-03',
										// 		extendedProps: {
										// 			department: 'BioChemistry'
										// 		},
										// 	}
										// ],
										eventClick: function(info) {
											console.log(info.event);
											// change the border color just for fun
											// info.el.style.borderColor = 'red';
										},
										eventChange: function(info){
											console.log(info.event.extendedProps);
										},
										eventAdd: function(info){
											alert('ADD');
										},



										weekends: false,
										initialView: 'dayGridMonth',
										headerToolbar: {
											left  : 'prev,next today',
											center: 'saveEventButton',
											right : 'dayGridMonth,listMonth'
											// right : 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
										},
										footerToolbar: {
											center: 'title',
										},
										editable: !0,
										selectable: true,

										droppable : true, // this allows things to be dropped onto the calendar !!!
										drop      : function(info) {
											console.log(999);
											let accept = confirm('999');
											if( accept ){
												info.draggedEl.parentNode.removeChild(info.draggedEl);
											}else{
												// info.jsEvent.preventDefault();
												console.log(info);
												throw 'Cancel drop';
											}
										},
										eventDrop: function(info){
											console.log(info.event);
										},
										eventLeave: function(info){
											console.log(info.event);
										},

										select: function(info) {
											console.log('selected ' + info.startStr + ' to ' + info.endStr);
										},
										customButtons: {
											saveEventButton: { //https://fullcalendar.io/docs/Calendar-addEvent-demo
												text: 'Save',
												click: function() {
													var dateStr = prompt('Enter a date in YYYY-MM-DD format');
													var date = new Date(dateStr + 'T00:00:00'); // will be in local time

													if (!isNaN(date.valueOf())) { // valid?
														calendar.addEvent({
															title: 'dynamic event',
															start: date,
															allDay: true
														});
														alert('Great. Now, update your database...');
													} else {
														alert('Invalid date.');
													}
												}
											}
										}
									})
									calendar.render()
								})

								</script>
							</div>



                            <div class="card card-danger card-outline">
                                <div class="card-body">
                                    <h5 class="card-title">Card title</h5>
                                    <p class="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the card's
                                        content.
                                    </p>
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <h5 class="card-title">Card title</h5>
                                    <p class="card-text">
                                        Some quick example text to build on the card title and make up the bulk of the card's
                                        content.
                                    </p>
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
