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
				      		<form method="post" class="form-horizontal" action="" id="resend_form">
					      		<div class="card-header"><h3 class="text-center">Resend Verificaton Email</h3></div>
					      		<div class="card-body">
				      			
				      				<div class="row form-group">
				      					<label class="col-sm-4 col-form-label"><b>Email Address</b></label>
				      					<div class="col-sm-8">
					      					<input type="text" name="student_email_id" id="student_email_id" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
					      				</div>
				      				</div>
				      			</div>
				      			<div class="card-footer text-center">
				      				<input type="hidden" name="page" value="resend_email_verificaiton" />
				      				<input type="hidden" name="action" value="send_verificaton_email" />
				      				<input type="submit" name="submit" id="send_button" class="btn btn-primary" value="Send" />
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

	$('#resend_form').parsley();

	$('#resend_form').on('submit', function(event){
		event.preventDefault();
		if($('#resend_form').parsley().isValid())
		{
			$.ajax({
				url:"ajax_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:"JSON",
				beforeSend:function()
                {
                    $('#send_button').attr('disabled', 'disabled');
                    $('#send_button').val('wait...');
                },
				success:function(data)
				{
					$('#send_button').attr('disabled', false);
                    $('#error').html(data.error);
                    $('#send_button').val('Send');
				}
			});
		}
	});

});

</script>