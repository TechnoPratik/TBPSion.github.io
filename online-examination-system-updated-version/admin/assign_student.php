<?php

//assign_student.php

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
SELECT * FROM student_soes 
WHERE student_status = 'Enable' 
ORDER BY student_name ASC
";

$student_data = $object->get_result();

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Assign Student Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Student List with Class Name</h6>
                            	</div>
                            	<div class="col" align="right">
                                    <button type="button" name="assign_student" id="assign_student" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="student_assign_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Roll No.</th>
                                            <th>Student Name</th>
                                            <th>Class Name</th>
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

<div id="studentassignModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="student_assign_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Assign Student to Class</h4>
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
		          		<label>Student Name</label>
		          		<select name="student_id" id="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                            <?php
                            foreach($student_data as $row)
                            {
                                echo '<option value="'.$row["student_id"].'">'.$row["student_name"].'</option>';
                            }
                            ?>
                        </select>
		          	</div>
                    <div class="form-group">
                        <label>Roll No.</label>
                        <input type="text" name="student_roll_no" id="student_roll_no" class="form-control" required />
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

	var dataTable = $('#student_assign_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"assign_student_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[4],
				"orderable":false,
			},
		],
	});

    $('#assign_student').click(function(){
        
        $('#student_assign_form')[0].reset();

        $('#student_assign_form').parsley().reset();

        $('#modal_title').text('Assign Class to Student');

        $('#action').val('Add');

        $('#submit_button').val('Add');

        $('#studentassignModal').modal('show');

        $('#form_message').html('');

    });
    
	$('#student_assign_form').parsley();

	$('#student_assign_form').on('submit', function(event){
		event.preventDefault();
		if($('#student_assign_form').parsley().isValid())
		{		
			$.ajax({
				url:"assign_student_action.php",
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
						$('#studentassignModal').modal('hide');
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

		var student_to_class_id = $(this).data('id');

		$('#student_assign_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"assign_student_action.php",

	      	method:"POST",

	      	data:{student_to_class_id:student_to_class_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                $('#class_id').val(data.class_id);

	        	$('#student_id').val(data.student_id);

	        	$('#modal_title').text('Edit Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#studentassignModal').modal('show');

	        	$('#hidden_id').val(student_to_class_id);

	      	}

	    })

	});

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"assign_student_action.php",

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