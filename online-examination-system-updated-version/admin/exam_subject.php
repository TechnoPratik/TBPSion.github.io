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
    header("location:".$object->base_url."admin");
}

$object->query = "
SELECT * FROM exam_soes 
WHERE exam_status = 'Pending' OR exam_status = 'Created' 
ORDER BY exam_title ASC
";

$result = $object->get_result();

include('header.php');
                
?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Exam Subject Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Exam Subject List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_exam_subject" id="add_exam_subject" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="exam_subject_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Subject</th>
                                            <th>Exam Datetime</th>
                                            <th>Total Question</th>
                                            <th>Right Answer Mark</th>
                                            <th>Wrong Answer Mark</th>
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

<div id="examsubjectModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="exam_subject_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Exam Subject Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <label>Exam Name</label>
                        <select name="exam_id" id="exam_id" class="form-control" required>
                            <option value="">Select Exam</option>
                            <?php
                            foreach($result as $row)
                            {
                                echo '
                                <option value="'.$row["exam_id"].'">'.$row["exam_title"].'</option>
                                ';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Exam Date & Time</label>
                        <input type="text" name="subject_exam_datetime" id="subject_exam_datetime" class="form-control datepicker" readonly required data-parsley-trigger="keyup" />
                    </div>
                    <div class="form-group">
                        <label>Total Question</label>
                        <select name="subject_total_question" id="subject_total_question" class="form-control" required>
                            <option value="">Select</option>
                            <option value="5">5 Question</option>
                            <option value="10">10 Question</option>
                            <option value="25">25 Question</option>
                            <option value="50">50 Question</option>
                            <option value="100">100 Question</option>
                            <option value="200">200 Question</option>
                            <option value="300">300 Question</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Marks for Correct Answer</label>
                        <select name="marks_per_right_answer" id="marks_per_right_answer" class="form-control">
                            <option value="">Select</option>
                            <option value="1">+1 Mark</option>
                            <option value="2">+2 Mark</option>
                            <option value="3">+3 Mark</option>
                            <option value="4">+4 Mark</option>
                            <option value="5">+5 Mark</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Marks for Wrong Answer</label>
                        <select name="marks_per_wrong_answer" id="marks_per_wrong_answer" class="form-control">
                            <option value="">Select</option>
                            <option value="1">-1 Mark</option>
                            <option value="1.25">-1.25 Mark</option>
                            <option value="1.50">-1.50 Mark</option>
                            <option value="2">-2 Mark</option>
                        </select>
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

<script>
$(document).ready(function(){

	var dataTable = $('#exam_subject_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"exam_subject_action.php",
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

    $('#exam_id').change(function(){
        var exam_id = $('#exam_id').val();
        if(exam_id != '')
        {
            $.ajax({
                url:"exam_subject_action.php",
                method:"POST",
                data:{action:'fetch_subject', exam_id:exam_id},
                success:function(data)
                {
                    $('#subject_id').html(data);
                }
            });
        }
    });

    var date = new Date();
    date.setDate(date.getDate());
    $("#subject_exam_datetime").datetimepicker({
        startDate: date,
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true
    });

	$('#add_exam_subject').click(function(){
		
		$('#exam_subject_form')[0].reset();

		$('#exam_subject_form').parsley().reset();

    	$('#modal_title').text('Add Exam Subject Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#examsubjectModal').modal('show');

    	$('#form_message').html('');

        $('#exam_id').attr('disabled', false);

        $('#subject_id').attr('disabled', false);

	});


	$('#exam_subject_form').parsley();

	$('#exam_subject_form').on('submit', function(event){

		event.preventDefault();
		if($('#examsubjectModal').parsley().isValid())
		{		
			
            $.ajax({
				url:"exam_subject_action.php",
				method:"POST",
                data:$(this).serialize(),
                // dataType:'json',
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
                    // alert("ready To Proces");
				},
				success:function(data)
				{   
                    // alert("Success Process");
					// $('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
                        // alert("Not Error");
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
                        // alert("Error");
						$('#examsubjectModal').modal('hide');

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

		var exam_subject_id = $(this).data('id');

		$('#exam_subject_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"exam_subject_action.php",

	      	method:"POST",

	      	data:{exam_subject_id:exam_subject_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                $('#subject_id').html(data.subject_select_box);

	        	$('#exam_id').val(data.exam_id);

                $('#subject_id').val(data.subject_id);

                $('#exam_id').attr('disabled', 'disabled');

                $('#subject_id').attr('disabled', 'disabled');

                $('#subject_total_question').val(data.subject_total_question);

                $('#marks_per_right_answer').val(data.marks_per_right_answer);

                $('#marks_per_wrong_answer').val(data.marks_per_wrong_answer);

                $('#subject_exam_datetime').val(data.subject_exam_datetime);

	        	$('#modal_title').text('Edit Exam Subject Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#examsubjectModal').modal('show');

	        	$('#hidden_id').val(exam_subject_id);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"exam_subject_action.php",

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

});
</script>