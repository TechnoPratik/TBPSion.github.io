<?php

//subject.php

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

include('header.php');

$object->query = "
SELECT * FROM class_soes 
WHERE class_status = 'Enable' 
ORDER BY class_name ASC";

$class_data = $object->get_result();

$object->query = "
SELECT * FROM subject_soes 
WHERE subject_status = 'Enable' 
ORDER BY subject_name ASC
";

$subject_data = $object->get_result();

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Assign Subject Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Class List with Subject</h6>
                            	</div>
                            	<div class="col" align="right">
                                    <button type="button" name="assign_subject" id="assign_subject" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="subject_assign_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Subject Name</th>
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

<div id="subjectassignModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="subject_assign_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Assign Subject to Class</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <label>Class Name</label>
                        <select name="class_id" id="class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            <?php
                            foreach($class_data as $row)
                            {
                                echo '<option value="'.$row["class_id"].'">'.$row["class_name"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
		          	<div class="form-group">
		          		<label>Subject Name</label>
		          		<select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">Select Subject</option>
                            <?php
                            foreach($subject_data as $row)
                            {
                                echo '<option value="'.$row["subject_id"].'">'.$row["subject_name"].'</option>';
                            }
                            ?>
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

	var dataTable = $('#subject_assign_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"assign_subject_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[3],
				"orderable":false,
			},
		],
	});

    //
    $('#assign_subject').click(function(){
        
        $('#subject_assign_form')[0].reset();

        $('#subject_assign_form').parsley().reset();

        $('#modal_title').text('Assign Subject to Class');

        $('#action').val('Add');

        $('#submit_button').val('Add');

        $('#subjectassignModal').modal('show');

        $('#form_message').html('');

    });
    //

	$('#subject_assign_form').parsley();

	$('#subject_assign_form').on('submit', function(event){
		event.preventDefault();
		if($('#subject_assign_form').parsley().isValid())
		{		
			$.ajax({
				url:"assign_subject_action.php",
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
						$('#subjectassignModal').modal('hide');
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

		var subject_to_class_id = $(this).data('id');

		$('#subject_assign_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"assign_subject_action.php",

	      	method:"POST",

	      	data:{subject_to_class_id:subject_to_class_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                $('#class_id').val(data.class_id);

	        	$('#subject_id').val(data.subject_id);

	        	$('#modal_title').text('Edit Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#subjectassignModal').modal('show');

	        	$('#hidden_id').val(subject_to_class_id);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"assign_subject_action.php",

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