<?php

//exam_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('exam_soes.exam_title', 'class_soes.class_name', 'exam_soes.exam_duration', 'exam_soes.exam_result_datetime', 'exam_soes.exam_status');

		$output = array();

		$main_query = "
		SELECT * FROM exam_soes 
		INNER JOIN class_soes 
		ON class_soes.class_id = exam_soes.exam_class_id 
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE class_soes.class_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_soes.exam_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_soes.exam_duration LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_soes.exam_result_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_soes.exam_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY exam_soes.exam_id DESC ';
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
			$sub_array[] = html_entity_decode($row["exam_title"]);
			$sub_array[] = html_entity_decode($row["class_name"]);
			$sub_array[] = $row["exam_duration"] . ' Minute';

			$exam_result_datetime = '';

			if($row['exam_result_datetime'] == '0000-00-00 00:00:00')
			{
				$exam_result_datetime = 'Not Publish';
			}
			else
			{
				$exam_result_datetime = $row['exam_result_datetime'];
			}

			$sub_array[] = $exam_result_datetime;

			$status = '';
			$action_button = '';

			$object->query = "
			SELECT * FROM subject_wise_exam_detail 
			WHERE exam_id = '".$row["exam_id"]."'
			";

			$object->execute();
			$total_subject = $object->row_count();
			if($total_subject > 0)
			{
				$subject_exam_result = $object->statement_result();
				$first_subject_datetime = '';
				$last_subject_datetime = '';
				$subject_count = 1;
				foreach($subject_exam_result as $subject_row)
				{
					if($subject_count == 1)
					{
						$first_subject_datetime = $subject_row["subject_exam_datetime"];
					}
					if($total_subject == $subject_count)
					{
						$last_subject_datetime = $subject_row["subject_exam_datetime"];
					}
					$subject_count++;
				}

				$exam_last_subject_end_datetime = strtotime($last_subject_datetime . '+' . $row["exam_duration"] . ' Minute');

				if(time() >= strtotime($first_subject_datetime) && time() <= $exam_last_subject_end_datetime)
				{
					$tmp_data = array(
						':exam_status'  		=>  'Started',
                        ':exam_id'      		=>  $row["exam_id"]
                    );

                    $object->query = "
                    UPDATE exam_soes 
                    SET exam_status = :exam_status 
                    WHERE exam_id = :exam_id
                    ";

                    $object->execute($tmp_data);

                    $status = '<span class="badge badge-primary">Started</span>';
                    $action_button = '';
                }
                else
                {
                	if(time() > $exam_last_subject_end_datetime)
                	{
                		$tmp_data = array(
                			':exam_status'  		=>  'Completed',
                			':exam_id'      		=>  $row["exam_id"]
                		);

                		$object->query = "
	                    UPDATE exam_soes 
	                    SET exam_status = :exam_status 
	                    WHERE exam_id = :exam_id
	                    ";

	                    $object->execute($tmp_data);

	                    $status = '<span class="badge badge-dark">Completed</span>';

	                    $action_button = '<button type="button" name="publish_result" class="btn btn-primary btn-sm publish_result" data-exam_id="'.$row["exam_id"].'">Publish Result</button>&nbsp;&nbsp;<a href="exam_result.php?ec='.$row["exam_code"].'" class="btn btn-secondary btn-sm">View Result</a>';
	                }
	                else
	                {
	                	if($row['exam_status'] == 'Pending')
						{
							$status = '<span class="badge badge-warning">Pending</span>';
							$action_button = '
							<div align="center">
							<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["exam_id"].'"><i class="fas fa-edit"></i></button>
							&nbsp;
							<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["exam_id"].'"><i class="fas fa-times"></i></button>
							</div>
							';
						}

						if($row['exam_status'] == 'Created')
						{
							$status = '<span class="badge badge-success">Created</span>';
							$action_button = '';
						}
	                }
	            }
	        }
	        else
	        {
				if($row['exam_status'] == 'Pending')
				{
					$status = '<span class="badge badge-warning">Pending</span>';
					$action_button = '
					<div align="center">
					<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["exam_id"].'"><i class="fas fa-edit"></i></button>
					&nbsp;
					<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["exam_id"].'"><i class="fas fa-times"></i></button>
					</div>
					';
				}

				if($row['exam_status'] == 'Created')
				{
					$status = '<span class="badge badge-success">Created</span>';
					$action_button = '';
				}

				if($row['exam_status'] == 'Started')
				{
					$status = '<span class="badge badge-primary">Started</span>';
					$action_button = '';
				}

				if($row['exam_status'] == 'Completed')
				{
					$status = '<span class="badge badge-dark">Completed</span>';
					$action_button = '';
				}
				
			}

			$sub_array[] = $status;

			$sub_array[] = $row["exam_created_on"];

			$sub_array[] = $action_button; 

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
			':exam_class_id'	=>	$_POST["exam_class_id"],
			':exam_title'		=>	$_POST["exam_title"]
		);

		$object->query = "
		SELECT * FROM exam_soes 
		WHERE exam_class_id = :exam_class_id 
		AND exam_title = :exam_title
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Exam Already Exists for <b>'.$object->Get_class_name($_POST["exam_class_id"]).'</b> Class</div>';
		}
		else
		{
			$data = array(
				':exam_title'			=>	$_POST["exam_title"],
				':exam_class_id'		=>	$_POST["exam_class_id"],
				':exam_duration'		=>	$_POST["exam_duration"],
				':exam_status'			=>	'Pending',
				':exam_created_on'		=>	$object->now,
				':exam_code'			=>	md5(uniqid())
			);

			$object->query = "
			INSERT INTO exam_soes 
			(exam_title, exam_class_id, exam_duration, exam_status, exam_created_on, exam_code) 
			VALUES (:exam_title, :exam_class_id, :exam_duration, :exam_status, :exam_created_on, :exam_code)
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Exam Added in <b>'.$object->Get_class_name($_POST["exam_class_id"]).'</b> Class</div>';
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
		SELECT * FROM exam_soes 
		WHERE exam_id = '".$_POST["exam_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['exam_title'] = $row['exam_title'];
			$data['exam_class_id'] = $row['exam_class_id'];
			$data['exam_duration'] = $row['exam_duration'];
			$data['exam_status'] = $row['exam_status'];

			//$object->query = ""

		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':exam_title'			=>	$_POST["exam_title"],
			':exam_duration'		=>	$_POST["exam_duration"],
			':exam_status'			=>	$_POST["exam_status"]
		);

		$object->query = "
		UPDATE exam_soes 
		SET exam_title = :exam_title,
		exam_duration = :exam_duration, 
		exam_status = :exam_status    
		WHERE exam_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Exam Updated</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_result_publish_data')
	{
		$object->query = "
		SELECT * FROM exam_soes 
		WHERE exam_id = '".$_POST["exam_id"]."'
		";

		$result = $object->get_result();

		$exam_result_datetime = '';

		foreach($result as $row)
		{
			$exam_result_datetime = $row['exam_result_datetime'];
		}

		if($exam_result_datetime == '0000-00-00 00:00:00')
		{
			$exam_result_datetime = '';
		}

		echo $exam_result_datetime;
	}

	if($_POST['action'] == 'Result Publish')
	{
		$success = '';

		$data = array(
			':exam_result_datetime'		=>	$_POST["exam_result_publish_datetime"],
			':exam_id'					=>	$_POST["hidden_exam_id"]
		);

		$object->query = "
		UPDATE exam_soes 
		SET exam_result_datetime = :exam_result_datetime 
		WHERE exam_id = :exam_id
		";

		$object->execute($data);

		$output = array(
			'success'		=>	'Exam Result has been Publish'
		);
		echo json_encode($output);
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM exam_soes 
		WHERE exam_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Exam Deleted</div>';
	}
}

?>