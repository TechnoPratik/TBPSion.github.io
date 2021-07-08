<?php

//subject_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('student_name', 'student_address', 'student_email_id', 'student_password', 'student_gender', 'student_dob', 'student_added_on', 'student_status');

		$output = array();

		$main_query = "
		SELECT * FROM student_soes 
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE student_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_email_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_gender LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_dob LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_added_on LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY student_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = '<img src="'.$row["student_image"].'" class="img-fluid img-thumbnail" width="75" height="75" />';
			$sub_array[] = html_entity_decode($row["student_name"]);
			$sub_array[] = html_entity_decode($row["student_address"]);
			$sub_array[] = html_entity_decode($row["student_email_id"]);
			$sub_array[] = $row["student_password"];
			$sub_array[] = $row["student_gender"];
			$sub_array[] = $row["student_dob"];
			$sub_array[] = $row["student_added_by"];
			$sub_array[] = $row["student_added_on"];
			$status = '';
			if($row["student_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["student_id"].'" data-status="'.$row["student_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["student_id"].'" data-status="'.$row["student_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["student_id"].'"><i class="fas fa-edit"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);
	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$data = array(
			':student_email_id'	=>	$_POST["student_email_id"]
		);

		$object->query = "
		SELECT * FROM student_soes 
		WHERE student_email_id = :student_email_id 
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Student Data Already Exists</div>';
		}
		else
		{
			$student_image = '';

			if($_FILES["student_image"]["name"] != '')
			{
				$student_image = upload_image();
			}

			$data = array(
				':student_name'			=>	$object->clean_input($_POST["student_name"]),
				':student_address'		=>	$object->clean_input($_POST["student_address"]),
				':student_email_id'		=>	$_POST["student_email_id"],
				':student_password'		=>	$_POST["student_password"],
				':student_gender'		=>	$_POST["student_gender"],
				':student_dob'			=>	$_POST["student_dob"],
				':student_image'		=>	$student_image,
				':student_status'		=>	'Enable',
				':student_added_by'		=>	$object->Get_user_name($_SESSION['user_id']),
				':student_added_on'		=>	$object->now
			);

			$object->query = "
			INSERT INTO student_soes 
			(student_name, student_address, student_email_id, student_password, student_gender, student_dob, student_image, student_status, student_added_by, student_added_on) 
			VALUES (:student_name, :student_address, :student_email_id, :student_password, :student_gender, :student_dob, :student_image, :student_status, :student_added_by, :student_added_on)
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Student Data Added</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM student_soes 
		WHERE student_id = '".$_POST["student_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['student_name'] = $row['student_name'];
			$data['student_address'] = $row['student_address'];
			$data['student_email_id'] = $row['student_email_id'];
			$data['student_password'] = $row['student_password'];
			$data['student_gender'] = $row['student_gender'];
			$data['student_dob'] = $row['student_dob'];
			$data['student_image'] = $row['student_image'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':student_email_id'		=>	$_POST["student_email_id"],
			':student_id'			=>	$_POST['hidden_id']
		);

		$object->query = "
		SELECT * FROM student_soes 
		WHERE student_email_id = :student_email_id 
		AND student_id != :student_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Student Data Already Exists</div>';
		}
		else
		{
			$student_image = $_POST["hidden_student_image"];

			if($_FILES["student_image"]["name"] != '')
			{
				$student_image = upload_image();
			}

			$data = array(
				':student_name'			=>	$_POST["student_name"],
				':student_address'		=>	$_POST["student_address"],
				':student_email_id'		=>	$_POST["student_email_id"],
				':student_password'		=>	$_POST["student_password"],
				':student_gender'		=>	$_POST["student_gender"],
				':student_dob'			=>	$_POST["student_dob"],
				':student_image'		=>	$student_image,
				':student_id'			=>	$_POST['hidden_id']
			);

			$object->query = "
			UPDATE student_soes 
			SET student_name = :student_name, 
			student_address = :student_address, 
			student_email_id = :student_email_id, 
			student_password = :student_password, 
			student_gender = :student_gender, 
			student_dob = :student_dob, 
			student_image = :student_image 
			WHERE student_id = :student_id
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Student Data Updated</div>';
			
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':student_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE student_soes 
		SET student_status = :student_status 
		WHERE student_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Student Status change to '.$_POST['next_status'].'</div>';
	}

}

function upload_image()
{
	if(isset($_FILES["student_image"]))
	{
		$extension = explode('.', $_FILES['student_image']['name']);
		$new_name = rand() . '.' . $extension[1];
		$destination = '../images/' . $new_name;
		move_uploaded_file($_FILES['student_image']['tmp_name'], $destination);
		return $destination;
	}
}

?>