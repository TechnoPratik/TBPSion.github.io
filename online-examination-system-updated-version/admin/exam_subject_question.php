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
SELECT * FROM exam_soes 
WHERE exam_status = 'Pending' OR exam_status = 'Created' 
ORDER BY exam_title ASC
";

$result = $object->get_result();

include('header.php');
                
?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Exam Subject Question Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Exam Subject Question List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_exam_subject_question" id="add_exam_subject_question" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="exam_subject_question_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Subject</th>
                                            <th>Question</th>
                                            <th>Option 1</th>
                                            <th>Option 2</th>
                                            <th>Option 3</th>
                                            <th>Option 4</th>
                                            <th>Answer</th>
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

<div id="examsubjectquestionModal" class="modal fade">
  	<div class="modal-dialog modal-lg">
    	<form method="post" id="exam_subject_question_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Exam Subject Question Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Exam Name</label>
                        <div class="col-sm-9">
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
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Subject</label>
                        <div class="col-sm-9">
                            <select name="exam_subject_id" id="exam_subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Question Title</label>
                        <div class="col-sm-9">
                            <input type="text" name="exam_subject_question_title" id="exam_subject_question_title" class="form-control datepicker" required data-parsley-trigger="keyup" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Option 1</label>
                        <div class="col-sm-9">
                            <input type="text" name="option_title_1" id="option_title_1" autocomplete="off" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Option 2</label>
                        <div class="col-sm-9">
                            <input type="text" name="option_title_2" id="option_title_2" autocomplete="off" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Option 3</label>
                        <div class="col-sm-9">
                            <input type="text" name="option_title_3" id="option_title_3" autocomplete="off" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Option 4</label>
                        <div class="col-sm-9">
                            <input type="text" name="option_title_4" id="option_title_4" autocomplete="off" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Answer</label>
                        <div class="col-sm-9">
                            <select name="exam_subject_question_answer" id="exam_subject_question_answer" class="form-control" required>
                                <option value="">Select</option>
                                <option value="1">1 Option</option>
                                <option value="2">2 Option</option>
                                <option value="3">3 Option</option>
                                <option value="4">4 Option</option>
                            </select>
                        </div>
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

	var dataTable = $('#exam_subject_question_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"exam_subject_question_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[3, 4, 5, 6, 7, 8],
				"orderable":false,
			},
		],
	});

    $('#exam_id').change(function(){
        var exam_id = $('#exam_id').val();
        if(exam_id != '')
        {
            $.ajax({
                url:"exam_subject_question_action.php",
                method:"POST",
                data:{action:'fetch_subject', exam_id:exam_id},
                success:function(data)
                {
                    $('#exam_subject_id').html(data);
                }
            });
        }
    });

	$('#add_exam_subject_question').click(function(){
		
		$('#exam_subject_question_form')[0].reset();

		$('#exam_subject_question_form').parsley().reset();

    	$('#modal_title').text('Add Exam Subject Question Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#examsubjectquestionModal').modal('show');

    	$('#form_message').html('');

        $('#exam_id').attr('disabled', false);

        $('#exam_subject_id').attr('disabled', false);

	});

	$('#exam_subject_question_form').parsley();

	$('#exam_subject_question_form').on('submit', function(event){
		event.preventDefault();
		if($('#exam_subject_question_form').parsley().isValid())
		{		
			$.ajax({
				url:"exam_subject_question_action.php",
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
						$('#examsubjectquestionModal').modal('hide');

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

		var exam_subject_question_id = $(this).data('id');

		$('#exam_subject_question_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"exam_subject_question_action.php",

	      	method:"POST",

	      	data:{exam_subject_question_id:exam_subject_question_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                $('#exam_id').val(data.exam_id);

                $('#exam_subject_id').html('<option value="">Select Subject</option><option value="'+data.exam_subject_id+'">'+data.subject_name+'</option>');

                $('#exam_subject_id').val(data.exam_subject_id);

	        	$('#exam_subject_question_title').val(data.exam_subject_question_title);

                $('#option_title_1').val(data.option_title_1);

                $('#option_title_2').val(data.option_title_2);

                $('#option_title_3').val(data.option_title_3);

                $('#option_title_4').val(data.option_title_4);

                $('#exam_subject_question_answer').val(data.exam_subject_question_answer);

	        	$('#modal_title').text('Edit Exam Subject Question Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#examsubjectquestionModal').modal('show');

	        	$('#hidden_id').val(exam_subject_question_id);

                $('#exam_id').attr('disabled', 'disabled');

                $('#exam_subject_id').attr('disabled', 'disabled');
	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"exam_subject_question_action.php",

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