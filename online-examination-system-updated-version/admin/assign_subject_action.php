<?php

//subject_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('class_soes.class_name', 'subject_soes.subject_name', 'subject_to_class_soes.added_on');

		$output = array();

		$main_query = "
		SELECT * FROM subject_to_class_soes 
		INNER JOIN class_soes 
		ON class_soes.class_id = subject_to_class_soes.class_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_to_class_soes.subject_id 
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE subject_soes.subject_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR class_soes.class_name LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY subject_to_class_soes.subject_to_class_id DESC ';
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
			$sub_array[] = html_entity_decode($row["class_name"]);
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$sub_array[] = $row["added_on"];
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["subject_to_class_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["subject_to_class_id"].'"><i class="fas fa-times"></i></button>
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
			':class_id'		=>	$_POST["class_id"],
			':subject_id'	=>	$_POST["subject_id"]
		);

		$object->query = "
		SELECT * FROM subject_to_class_soes 
		WHERE class_id = :class_id 
		AND subject_id = :subject_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Subject Name Already Assign to <b>'.$object->Get_class_name($_POST["class_id"]).'</b> class</div>';
		}
		else
		{
			$data = array(
				':class_id'				=>	$_POST["class_id"],
				':subject_id'			=>	$_POST["subject_id"],
				':added_on'				=>	$object->now
			);

			$object->query = "
			INSERT INTO subject_to_class_soes 
			(class_id, subject_id, added_on) 
			VALUES (:class_id, :subject_id, :added_on)
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Subject Assign to <b>'.$object->Get_class_name($_POST["class_id"]).'</b> class</div>';
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
		SELECT * FROM subject_to_class_soes 
		WHERE subject_to_class_id = '".$_POST["subject_to_class_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['class_id'] = $row['class_id'];
			$data['subject_id'] = $row['subject_id'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':class_id'			=>	$_POST["class_id"],
			':subject_id'		=>	$_POST["subject_id"]
		);

		$object->query = "
		SELECT * FROM subject_to_class_soes 
		WHERE class_id = :class_id 
		AND subject_id = :subject_id 
		AND subject_to_class_id != '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Subject Name Already Assign to <b>'.$object->Get_class_name($_POST["class_id"]).'</b> class</div>';
		}
		else
		{

			$object->query = "
			UPDATE subject_to_class_soes 
			SET class_id = :class_id, subject_id = :subject_id 
			WHERE subject_to_class_id = '".$_POST['hidden_id']."'
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Subject Data Updated</div>';
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM subject_to_class_soes 
		WHERE subject_to_class_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Subject Data Deleted</div>';
	}

}



?>