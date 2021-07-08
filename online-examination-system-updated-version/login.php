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
				      		<form method="post" class="form-horizontal" action="" id="login_form">
					      		<div class="card-header"><h3 class="text-center">Login</h3></div>
					      		<div class="card-body">
				      			
				      				<div class="row form-group">
				      					<label class="col-sm-4 col-form-label"><b>Email Address</b></label>
				      					<div class="col-sm-8">
					      					<input type="text" name="student_email_id" id="student_email_id" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
					      				</div>
				      				</div>
				      				<div class="row form-group">
				      					<label class="col-sm-4 col-form-label"><b>Password</b></label>
				      					<div class="col-sm-8">
					      					<input type="password" name="student_password" id="student_password" class="form-control" required />
					      				</div>
				      				</div>
				      			</div>
				      			<div class="card-footer text-center">
				      				<br />
				      				<input type="hidden" name="page" value="login" />
				      				<input type="hidden" name="action" value="check_login" />
				      				<p><input type="submit" name="submit" id="login_button" class="btn btn-primary" value="Login" /></p>

				      				<p><a href="forget_password.php">Forget Password</a></p>
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

	$('#login_form').parsley();

	$('#login_form').on('submit', function(event){
		event.preventDefault();
		if($('#login_form').parsley().isValid())
		{
			$.ajax({
				url:"ajax_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:"JSON",
				beforeSend:function()
                {
                    $('#login_button').attr('disabled', 'disabled');
                    $('#login_button').val('wait...');
                },
				success:function(data)
				{
					$('#login_button').attr('disabled', false);
                    if(data.error != '')
                    {
                        $('#error').html(data.error);
                        $('#login_button').val('Login');
                    }
                    else
                    {
                        window.location.href = data.url;
                    }
				}
			});
		}
	});

});

</script>