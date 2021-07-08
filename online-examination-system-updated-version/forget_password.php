<?php

//index.php

include('admin/soes.php');

$object = new soes();

if($object->is_student_login())
{
	header("location:".$object->base_url."student_dashboard.php");
}

include('header.php');

?>

				<div class="row justify-content-md-center">
					<div class="col-sm-6">
						<span id="error"></span>
				      	<div class="card">
				      		<form method="post" class="form-horizontal" action="" id="forget_password_form">
					      		<div class="card-header"><h3 class="text-center">Forget Password</h3></div>
					      		<div class="card-body">
				      			
				      				<div class="row form-group">
				      					<label class="col-sm-4 col-form-label"><b>Email Address</b></label>
				      					<div class="col-sm-8">
					      					<input type="text" name="student_email_id" id="student_email_id" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
					      				</div>
				      				</div>
				      			</div>
				      			<div class="card-footer text-center">
				      				<br />
				      				<input type="hidden" name="page" value="forget_password" />
				      				<input type="hidden" name="action" value="get_password" />
				      				<p><input type="submit" name="submit" id="forget_password_button" class="btn btn-primary" value="Send" /></p>

				      				<p><a href="login.php">Login</a></p>
				      			</div>
				      		</form>
				      	</div>
				    </div>
				</div>
		    

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){

	$('#forget_password_form').parsley();

	$('#forget_password_form').on('submit', function(event){
		event.preventDefault();
		if($('#forget_password_form').parsley().isValid())
		{
			$.ajax({
				url:"ajax_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:"JSON",
				beforeSend:function()
                {
                    $('#forget_password_button').attr('disabled', 'disabled');
                    $('#forget_password_button').val('wait...');
                },
				success:function(data)
				{
					$('#forget_password_button').attr('disabled', false);
                    $('#error').html(data.error);
                    $('#forget_password_button').val('Send');
				}
			});
		}
	});

});

</script>