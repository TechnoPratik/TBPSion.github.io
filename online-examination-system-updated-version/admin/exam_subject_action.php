<?php

//exam_action.php

include('soes.php');

$object = new soes();

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('exam_soes.exam_title', 'subject_soes.subject_name', 'subject_wise_exam_detail.subject_exam_datetime', 'subject_wise_exam_detail.subject_total_question', 'subject_wise_exam_detail.marks_per_right_answer', 'subject_wise_exam_detail.marks_per_wrong_answer');

		$output = array();

		$main_query = "
		SELECT * FROM subject_wise_exam_detail 
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
			$search_query .= 'OR subject_wise_exam_detail.subject_exam_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_wise_exam_detail.subject_total_question LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_wise_exam_detail.marks_per_right_answer LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR subject_wise_exam_detail.marks_per_wrong_answer LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY subject_wise_exam_detail.exam_subject_id DESC ';
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
			$sub_array[] = $row["subject_exam_datetime"];
			$sub_array[] = $row["subject_total_question"] . ' Question';
			$sub_array[] = $row["marks_per_right_answer"] . ' Mark';
			$sub_array[] = '-' . $row["marks_per_wrong_answer"] . ' Mark';

			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["exam_subject_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["exam_subject_id"].'"><i class="fas fa-times"></i></button>
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
		SELECT subject_soes.subject_id, subject_soes.subject_name 
		FROM exam_soes 
		INNER JOIN subject_to_class_soes 
		ON subject_to_class_soes.class_id = exam_soes.exam_class_id 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_to_class_soes.subject_id 
		WHERE exam_soes.exam_id = '".$_POST["exam_id"]."' 
		ORDER BY subject_soes.subject_id ASC";

		$result = $object->get_result();
		$html = '<option value="">Select Subject</option>';
		foreach($result as $row)
		{
			if(!$object->Check_subject_already_added_in_exam($_POST["exam_id"], $row['subject_id']))
			{
				$html .= '<option value="'.$row['subject_id'].'">'.$row['subject_name'].'</option>';
			}
		}
		echo $html;
	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$exam_id = $_POST["exam_id"];
		$subject_id = $_POST["subject_id"];
		$subject_total_question = $_POST["subject_total_question"];
		$marks_per_right_answer = $_POST["marks_per_right_answer"];
		$marks_per_wrong_answer = $_POST["marks_per_wrong_answer"];
		$subject_exam_datetime = $_POST["subject_exam_datetime"];
		$subject_exam_code = md5(uniqid());
		
		$data = array(
			':exam_id'					=>	$_POST["exam_id"],
			':subject_id'				=>	$_POST["subject_id"],
			':subject_total_question'	=>	$_POST["subject_total_question"],
			':marks_per_right_answer'	=>	$_POST["marks_per_right_answer"],
			':marks_per_wrong_answer'	=>	$_POST["marks_per_wrong_answer"],
			':subject_exam_datetime'	=>	$_POST["subject_exam_datetime"],
			':subject_exam_code'		=>	md5(uniqid())
		);

		$object->query = "
		INSERT INTO subject_wise_exam_detail 
		(exam_id, subject_id, subject_total_question, marks_per_right_answer, marks_per_wrong_answer, subject_exam_datetime, subject_exam_code) 
		VALUES ('$exam_id', '$subject_id', '$subject_total_question', '$marks_per_right_answer', '$marks_per_wrong_answer', '$subject_exam_datetime', '$subject_exam_code')
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Subject Added in <b>'.$object->Get_exam_name($_POST["exam_id"]).'</b> Class</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM subject_wise_exam_detail 
		WHERE exam_subject_id = '".$_POST["exam_subject_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['exam_id'] = $row['exam_id'];
			$data['subject_id'] = $row['subject_id'];
			$data['subject_total_question'] = $row['subject_total_question'];
			$data['marks_per_right_answer'] = $row['marks_per_right_answer'];
			$data['marks_per_wrong_answer'] = $row['marks_per_wrong_answer'];
			$data['subject_exam_datetime'] = $row['subject_exam_datetime'];
			$data['subject_select_box'] = '<option value="">Select Subject</option><option value="'.$row['subject_id'].'">'.$object->Get_Subject_name($row['subject_id']).'</option>';;
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':subject_total_question'	=>	$_POST["subject_total_question"],
			':marks_per_right_answer'	=>	$_POST["marks_per_right_answer"],
			':marks_per_wrong_answer'	=>	$_POST["marks_per_wrong_answer"],
			':subject_exam_datetime'	=>	$_POST["subject_exam_datetime"]
		);

		$object->query = "
		UPDATE subject_wise_exam_detail 
		SET subject_total_question = :subject_total_question, 
		marks_per_right_answer = :marks_per_right_answer,
		marks_per_wrong_answer = :marks_per_wrong_answer, 
		subject_exam_datetime = :subject_exam_datetime    
		WHERE exam_subject_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Exam Subject Data Updated</div>';
		
		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM subject_wise_exam_detail 
		WHERE exam_subject_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Exam Subject Data Deleted</div>';
	}
}

?>