<?php

//subject_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('subject_name', 'subject_status', 'subject_created_on');

		$output = array();

		$main_query = "
		SELECT * FROM subject_soes ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE subject_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY subject_id DESC ';
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
			//$sub_array[] = html_entity_decode($row["class_name"]);
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$sub_array[] = $row["subject_created_on"];
			$status = '';
			if($row["subject_status"] == 'Enable')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["subject_id"].'" data-status="'.$row["subject_status"].'">Enable</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["subject_id"].'" data-status="'.$row["subject_status"].'">Disable</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["subject_id"].'"><i class="fas fa-edit"></i></button>
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
			':subject_name'	=>	$_POST["subject_name"]
		);

		$object->query = "
		SELECT * FROM subject_soes 
		WHERE subject_name = :subject_name
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Subject Name Already Exists</div>';
		}
		else
		{
			$data = array(
				':subject_name'			=>	$object->clean_input($_POST["subject_name"]),
				':subject_status'		=>	'Enable',
				':subject_created_on'	=>	$object->now
			);

			$object->query = "
			INSERT INTO subject_soes 
			(subject_name, subject_status, subject_created_on) 
			VALUES (:subject_name, :subject_status, :subject_created_on)
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Subject Added</div>';
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
		SELECT * FROM subject_soes 
		WHERE subject_id = '".$_POST["subject_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['subject_name'] = html_entity_decode($row['subject_name']);
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':subject_name'		=>	$object->clean_input($_POST["subject_name"])
		);

		$object->query = "
		UPDATE subject_soes 
		SET subject_name = :subject_name 
		WHERE subject_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Subject Data Updated</div>';
		

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':subject_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE subject_soes 
		SET subject_status = :subject_status 
		WHERE subject_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Subject Status change to '.$_POST['next_status'].'</div>';
	}

}



?>