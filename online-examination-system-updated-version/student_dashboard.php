<?php

//student_dashboard.php

include('admin/soes.php');

$object = new soes();

if(!$object->is_student_login())
{
	header("location:".$object->base_url."");
}

include('header.php');

?>

				
		    

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){

	

});

</script>