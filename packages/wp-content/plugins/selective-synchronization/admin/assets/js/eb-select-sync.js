(function( $ ) {
	'use strict';

		$( window ).load(function() {
			/**
			 * Show moodle courses and associated categories using datatable
			 */
			
			var rows_selected = [];
			var table = $('#moodle_courses_table').DataTable({

					"oLanguage": {
				        "sZeroRecords": "No courses found."
			        },
			        "columnDefs": [
								    { "width": "30%", "targets": 1 }
								  ],
				  	"columns": [
							    { "searchable": false },
							    null,
							    null
				  			 ],
				  	'order': [1, 'asc'],
			      	'rowCallback': function(row, data, dataIndex){
					        // Get row ID
					        var rowId = data[0];

					        // If row ID is in the list of selected row IDs
					        if($.inArray(rowId, rows_selected) !== -1){
					            $(row).find('input[type="checkbox"]').prop('checked', true);
					        }
      					}
					});

			$('#moodle_courses_table').dataTable().columnFilter(
			{
				sPlaceHolder: "head:before",
             	aoColumns: [
                                null,
                                null,
                                {
                                    type: "select",
                                    values: admin_js_select_data.category_list
                                }
                           ]
       		});

       		// Handle click on checkbox
		   	$('#moodle_courses_table tbody').on('click', 'input[type="checkbox"]', function(e){
		      var $row = $(this).closest('tr');

		      // Get row data
		      var data = table.row($row).data();

		      // Get row ID
		      var rowId = data[0];

		      // Determine whether row ID is in the list of selected row IDs 
		      var index = $.inArray(rowId, rows_selected);

		      // If checkbox is checked and row ID is not in list of selected row IDs
		      if(this.checked && index === -1){
		         rows_selected.push(rowId);

		      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
		      } else if (!this.checked && index !== -1){
		         rows_selected.splice(index, 1);
		      }

		      // Update state of "Select all" control
		      updateDataTableSelectAllCtrl(table);

		      // Prevent click event from propagating to parent
		      e.stopPropagation();
		   	});

			// Handle click on table cells with checkboxes
		   	$('#moodle_courses_table').on('click', 'tbody td, thead th:first-child, tfoot th:first-child', function(e){
		      	$(this).parent().find('input[type="checkbox"]').trigger('click');
		   	});

		   // Handle click on "Select all" control
		   	$('#moodle_courses_table input[name="select_all_course"]').on('click', function(e){
			      if(this.checked){
			      	 $('#moodle_courses_table tfoot input[type="checkbox"]:not(:checked)').trigger('click');
		      		 $('#moodle_courses_table thead input[type="checkbox"]:not(:checked)').trigger('click');
			         $('#moodle_courses_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
			      } else {
			      	 $('#moodle_courses_table tfoot input[type="checkbox"]:checked').trigger('click');
			         $('#moodle_courses_table thead input[type="checkbox"]:checked').trigger('click');
			         $('#moodle_courses_table tbody input[type="checkbox"]:checked').trigger('click');
			      }

			      // Prevent click event from propagating to parent
			      e.stopPropagation();
		   	});

		    // Handle table draw event
		    table.on('draw', function(){
			    // Update state of "Select all" control
			    updateDataTableSelectAllCtrl(table);
		    }); 

		    $( ".eb-filter select" ).change(function() {

				var stable = $('#moodle_courses_table').dataTable();

				$("input:checked", stable.fnGetNodes()).each(function(){
					$(this).prop( "checked", false );
				});

				// Update state of "Select all" control
		      	updateDataTableSelectAllCtrl(table);
			});


			$('#eb_sync_selected_course_button').click(function(){

				$('.response-box').empty();

				//display loading animation
				$('.load-response').show();

				var stable = $('#moodle_courses_table').dataTable();

				var sids = new Array();

				$("input:checked", stable.fnGetNodes()).each(function(){

				    sids.push($(this).val());
				});

				var update_course = $('#eb_update_selected_courses').is(':checked')? 1: 0;

				if( sids.length <= 0 )
				{
					$('.load-response').hide();
					ohSnap( admin_js_select_data.chk_error , 'error', 0);
				}
				else
				{
					$.ajax({
						method		: "post",
						url			: admin_js_select_data.admin_ajax_path,
				        dataType	: "json",
				        data: {
				            'action'		   : 'selective_course_sync',
				            '_wpnonce_field'   : admin_js_select_data.nonce,
				            'selected_courses' : sids,
				            'update_course'	   : update_course,
				        },
				        success:function(response) {
				        	$('.load-response').hide();
				        	
				        	//prepare response for user
				        	if( response.connection_response == 1 ){
								if( response.course_success == 1 )
								{
									ohSnap(admin_js_select_data.select_success, 'success', 1);
								}
								else 
									ohSnap(response.course_response_message, 'error', 0);

							} else {
								ohSnap(admin_js_select_data.connect_error, 'error', 0);
							}				        	
				    	}
			    	});
				}
			});
	});

	function ohSnap(text, type, status) {
		  // text : message to show (HTML tag allowed)
		  // Available colors : red, green, blue, orange, yellow --- add your own!
		  
		  // Set some variables
		  var time = '10000';
		  var container = jQuery('.response-box');

		  // Generate the HTML
		  var html = '<div class="alert alert-' + type + '">' + text + '</div>';

		  // Append the label to the container
		  container.append(html);
	}

	// Updates "Select all" control in a data table
	function updateDataTableSelectAllCtrl(table){
		var $table                  = table.table().node();
		var $chkbox_all             = $('tbody input[type="checkbox"]', $table);
		var $chkbox_checked         = $('tbody input[type="checkbox"]:checked', $table);
		var chkbox_select_all       = $('thead input[name="select_all_course"]', $table).get(0);
		var chkbox_select_all_foot  = $('tfoot input[name="select_all_course"]', $table).get(0);

	   // If none of the checkboxes are checked
	   if($chkbox_checked.length === 0){
	      chkbox_select_all.checked = false;
	      chkbox_select_all_foot.checked = false;
	      if('indeterminate' in chkbox_select_all){
	         chkbox_select_all.indeterminate = false;
	         chkbox_select_all_foot.indeterminate = false;
	      }

	   // If all of the checkboxes are checked
	   } else if ($chkbox_checked.length === $chkbox_all.length){
	      chkbox_select_all.checked = true;
	      chkbox_select_all_foot.checked = true;
	      if('indeterminate' in chkbox_select_all){
	         chkbox_select_all.indeterminate = false;
	         chkbox_select_all_foot.indeterminate = false;
	      }

	   // If some of the checkboxes are checked
	   } else {
	      chkbox_select_all.checked = true;
	      chkbox_select_all_foot.checked = true;
	      if('indeterminate' in chkbox_select_all){
	         chkbox_select_all.indeterminate = true;
	         chkbox_select_all_foot.indeterminate = true;
	      }
	   }
	}

})( jQuery );