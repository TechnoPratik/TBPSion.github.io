<?php

//exam.php

include('soes.php');

$object = new soes();

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if(!$object->is_master_user())
{
    header("location:".$object->base_url."admin/result.php");
}

$object->query = "
SELECT * FROM class_soes 
WHERE class_status = 'Enable' 
ORDER BY class_name ASC
";

$result = $object->get_result();

include('header.php');
                
?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Exam Management</h1>
                    <?php echo date('Y-m-d H:i:s'); ?>
                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Exam List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_exam" id="add_exam" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="exam_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Class Name</th>
                                            <th>Exam Duration</th>
                                            <th>Result Date & Time</th>
                                            <th>Status</th>
                                            <th>Created On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="examModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="exam_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Exam Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <label>Exam Name</label>
                        <input type="text" name="exam_title" id="exam_title" class="form-control" required data-parsley-pattern="/^[a-zA-Z0-9 \s]+$/" data-parsley-trigger="keyup" />
                    </div>
                    <div class="form-group">
                        <label>Class</label>
                        <select name="exam_class_id" id="exam_class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            <?php
                            foreach($result as $row)
                            {
                                echo '
                                <option value="'.$row["class_id"].'">'.$row["class_name"].'</option>
                                ';
                            }
                            ?>
                        </select>
                    </div>
		          	<!--<div class="form-group">
                        <label>Exam Date & Time</label>
                        <input type="text" name="exam_datetime" id="exam_datetime" class="form-control datepicker" readonly required data-parsley-trigger="keyup" />
                    </div>!-->
                    <div class="form-group">
                        <label>Exam Duration for Each Subject <span class="text-danger">*</span></label>
                        <select name="exam_duration" id="exam_duration" class="form-control" required>
                            <option value="">Select</option>
                            <option value="5">5 Minute</option>
                            <option value="30">30 Minute</option>
                            <option value="60">1 Hour</option>
                            <option value="120">2 Hour</option>
                            <option value="180">3 Hour</option>
                        </select>
                    </div>
                    <div class="form-group" id="ifedit">

                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<div id="publishresultModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="publish_result_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Publish Exam Result</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">                    
                    <div class="form-group">
                        <label>Exam Result Publish Date & Time</label>
                        <input type="text" name="exam_result_publish_datetime" id="exam_result_publish_datetime" class="form-control datepicker" readonly required data-parsley-trigger="keyup" />
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_exam_id" id="hidden_exam_id" />
                    <input type="hidden" name="action" id="action" value="Result Publish" />
                    <input type="submit" name="submit" id="result_publish_submit_button" class="btn btn-success" value="Publish" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
$(document).ready(function(){

	var dataTable = $('#exam_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"exam_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[6],
				"orderable":false,
			},
		],
	});

    var date = new Date();
    date.setDate(date.getDate());
    $("#exam_datetime").datetimepicker({
        startDate: date,
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true
    });

    $('#exam_result_publish_datetime').datetimepicker({
        startDate: date,
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true
    });

	$('#add_exam').click(function(){
		
		$('#exam_form')[0].reset();

		$('#exam_form').parsley().reset();

    	$('#modal_title').text('Add Exam Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#examModal').modal('show');

    	$('#form_message').html('');

        $('#ifedit').html('');

        $('#exam_class_id').attr('disabled', false);

	});

	$('#exam_form').parsley();

	$('#exam_form').on('submit', function(event){
		event.preventDefault();
		if($('#examModal').parsley().isValid())
		{		
			$.ajax({
				url:"exam_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#examModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){

		var exam_id = $(this).data('id');

		$('#exam_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"exam_action.php",

	      	method:"POST",

	      	data:{exam_id:exam_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#exam_class_id').val(data.exam_class_id);

                $('#exam_class_id').attr('disabled', 'disabled');

                $('#exam_title').val(data.exam_title);

                $('#exam_duration').val(data.exam_duration);

	        	$('#modal_title').text('Edit Exam Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#examModal').modal('show');

	        	$('#hidden_id').val(exam_id);

                var exam_status_html = '<label>Exam Status <span class="text-danger">*</span></label>';

                exam_status_html += '<select name="exam_status" id="exam_status" class="form-control">';

                exam_status_html += '<option value="Pending">Pending</option>';

                exam_status_html += '<option value="Created">Created</option></select><span class="text-muted"><small>If you have select Created status, then Student will able to view Exam details in their dashboard & you will not able to edit or delete this exam data.</small></span>';

                $('#ifedit').html(exam_status_html);

                $('#exam_status').val(data.exam_status);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"exam_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}

  	});

    $(document).on('click', '.publish_result', function(){
        var exam_id = $(this).data('exam_id');
        $.ajax({
            url:"exam_action.php",
            method:"POST",
            data:{exam_id:exam_id, action:"fetch_result_publish_data"},
            success:function(data)
            {
                if(data != '')
                {
                    $('#exam_result_publish_datetime').val(data);
                }
                $('#publishresultModal').modal('show');
                $('#hidden_exam_id').val(exam_id);
            }
        });
    });

    $('#publish_result_form').parsley();

    $('#publish_result_form').on('submit', function(event){
        event.preventDefault();
        if($('#publish_result_form').parsley().isValid())
        {       
            $.ajax({
                url:"exam_action.php",
                method:"POST",
                data:$(this).serialize(),
                dataType:'json',
                beforeSend:function()
                {
                    $('#result_publish_submit_button').attr('disabled', 'disabled');
                    $('#result_publish_submit_button').val('wait...');
                },
                success:function(data)
                {
                    $('#result_publish_submit_button').attr('disabled', false);
                    
                    $('#publishresultModal').modal('hide');
                    $('#message').html(data.success);
                    dataTable.ajax.reload();

                    setTimeout(function(){
                        $('#message').html('');
                    }, 5000);
                }
            })
        }
    });

});
</script>