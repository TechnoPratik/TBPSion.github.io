<?php

//exam_subject_question_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('exam_soes.exam_title', 'subject_soes.subject_name', 'exam_subject_question_soes.exam_subject_question_title');

		$output = array();

		$main_query = "
		SELECT * FROM exam_subject_question_soes 
		INNER JOIN subject_wise_exam_detail 
		ON subject_wise_exam_detail.exam_subject_id = exam_subject_question_soes.exam_subject_id 
		INNER JOIN exam_soes 
		ON exam_soes.exam_id = subject_wise_exam_detail.exam_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_wise_exam_detail.subject_id 
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE exam_soes.exam_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_soes.subject_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR exam_subject_question_soes.exam_subject_question_title LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY exam_subject_question_soes.exam_subject_question_id DESC ';
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
			$sub_array[] = html_entity_decode($row["subject_name"]);
			$sub_array[] = $row["exam_subject_question_title"];
			$sub_array[] = $object->Get_question_option_data($row['exam_subject_question_id'], 1);
			$sub_array[] = $object->Get_question_option_data($row['exam_subject_question_id'], 2);
			$sub_array[] = $object->Get_question_option_data($row['exam_subject_question_id'], 3);
			$sub_array[] = $object->Get_question_option_data($row['exam_subject_question_id'], 4);
			$sub_array[] = $row["exam_subject_question_answer"] . ' Option';

			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["exam_subject_question_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["exam_subject_question_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST['action'] == 'fetch_subject')
	{
		$object->query = "
		SELECT subject_wise_exam_detail.exam_subject_id, subject_soes.subject_name 
		FROM subject_wise_exam_detail 
		INNER JOIN exam_soes 
		ON exam_soes.exam_id = subject_wise_exam_detail.exam_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_wise_exam_detail.subject_id 
		WHERE exam_soes.exam_id = '".$_POST["exam_id"]."' 
		ORDER BY subject_soes.subject_id ASC";

		$result = $object->get_result();
		$html = '<option value="">Select Subject</option>';
		foreach($result as $row)
		{
			if($object->Can_add_question_in_this_subject($row['exam_subject_id']))
			{
				$html .= '<option value="'.$row['exam_subject_id'].'">'.$row['subject_name'].'</option>';
			}
		}
		echo $html;
	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';
		
		$data = array(
			':exam_id'						=>	$_POST["exam_id"],
			':exam_subject_id'				=>	$_POST["exam_subject_id"],
			':exam_subject_question_title'	=>	$_POST["exam_subject_question_title"],
			':exam_subject_question_answer'	=>	$_POST["exam_subject_question_answer"]
		);

		$object->query = "
		INSERT INTO exam_subject_question_soes 
		(exam_id, exam_subject_id, exam_subject_question_title, exam_subject_question_answer) 
		VALUES (:exam_id, :exam_subject_id, :exam_subject_question_title, :exam_subject_question_answer)
		";

		$object->execute($data);

		$exam_subject_question_id = $object->connect->lastInsertId();

		for($count = 1; $count <= 4; $count++)
		{
			$data = array(
				':exam_subject_question_id'		=>	$exam_subject_question_id,
				':question_option_number'		=>	$count,
				':question_option_title'		=>	$object->clean_input($_POST['option_title_' . $count])
			);

			$object->query = "
			INSERT INTO question_option_soes 
			(exam_subject_question_id, question_option_number, question_option_title) 
			VALUES (:exam_subject_question_id, :question_option_number, :question_option_title)
			";
				
			$object->execute($data);
		}

		$success = '<div class="alert alert-success">Question Added</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM exam_subject_question_soes 
		WHERE exam_subject_question_id = '".$_POST["exam_subject_question_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		$exam_subject_id = '';

		foreach($result as $row)
		{
			$data['exam_id'] = $row['exam_id'];
			$data['exam_subject_id'] = $row['exam_subject_id'];
			$data['exam_subject_question_title'] = $row['exam_subject_question_title'];
			$data['exam_subject_question_answer'] = $row['exam_subject_question_answer'];
			$exam_subject_id = $row['exam_subject_id'];
		}

		$object->query = "
		SELECT * FROM question_option_soes 
		WHERE exam_subject_question_id = '".$_POST["exam_subject_question_id"]."'
		";

		$result = $object->get_result();

		foreach($result as $row)
		{
			$data['option_title_'.$row["question_option_number"].''] = $row["question_option_title"];
		}

		$object->query = "
		SELECT subject_soes.subject_name FROM subject_wise_exam_detail 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_wise_exam_detail.subject_id 
		WHERE subject_wise_exam_detail.exam_subject_id = '".$exam_subject_id."'
		";

		$result = $object->get_result();

		foreach($result as $row)
		{
			$data['subject_name'] = $row["subject_name"];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':exam_subject_question_title'	=>	$_POST["exam_subject_question_title"],
			':exam_subject_question_answer'	=>	$_POST["exam_subject_question_answer"]
		);

		$object->query = "
		UPDATE exam_subject_question_soes 
		SET exam_subject_question_title = :exam_subject_question_title, 
		exam_subject_question_answer = :exam_subject_question_answer     
		WHERE exam_subject_question_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		for($count = 1; $count <= 4; $count++)
		{
			$data = array(
				':exam_subject_question_id'		=>	$_POST['hidden_id'],
				':question_option_number'		=>	$count,
				':question_option_title'		=>	$object->clean_input($_POST['option_title_' . $count])
			);

			$object->query = "
			UPDATE question_option_soes 
			SET question_option_title = :question_option_title 
			WHERE exam_subject_question_id = :exam_subject_question_id 
			AND question_option_number = :question_option_number
			";
				
			$object->execute($data);
		}

		$success = '<div class="alert alert-success">Exam Subject Question Data Updated</div>';
		
		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM exam_subject_question_soes 
		WHERE exam_subject_question_id = '".$_POST["id"]."'
		";

		$object->execute();

		$object->query = "
		DELETE FROM question_option_soes 
		WHERE exam_subject_question_id = '".$_POST["id"]."'
		";

		echo '<div class="alert alert-success">Exam Subject Question Data Deleted</div>';
	}
}

?>