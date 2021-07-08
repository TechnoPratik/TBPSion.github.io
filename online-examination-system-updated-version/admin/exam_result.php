<?php

//exam_result.php

include('../admin/soes.php');

require_once('../class/pdf.php');

$object = new soes();

if(isset($_GET['ec']))
{
	$exam_id = $object->Get_exam_id($_GET['ec']);

	$object->query = "
	SELECT exam_class_id FROM exam_soes 
	WHERE exam_id = '".$exam_id."'
	";

	$exam_data = $object->get_result();

	$exam_class_id = '';

	foreach($exam_data as $exam_row)
	{
		$exam_class_id = $exam_row["exam_class_id"];
	}

	$object->query = "
	SELECT student_id, student_roll_no FROM student_to_class_soes 
	WHERE class_id = '".$exam_class_id."'
	";

	$class_data = $object->get_result();

	$output = '
	<h3 align="center">Exam - '.$object->Get_exam_name($exam_id).'</h3><br />
	<h3 align="center">Class - '.$object->Get_class_name($exam_class_id).'</h3>
	<br />
	<table width="100%" border="1" cellpadding="5" cellspacing="0">
		<tr>
			<td>Image</td>
			<td>Roll No.</td>
			<td>Student Name</td>
			<th>Total Marks</th>
			<th>Marks Obtain</th>
		</tr>
	';

	foreach($class_data as $class_row)
	{
		$student_name = '';
		$student_profile_img = '';
		$student_roll_no = $class_row["student_roll_no"];

		$object->query = "
		SELECT student_soes.student_name, student_soes.student_image FROM student_soes 
		WHERE student_id = '".$class_row["student_id"]."'
		";
		$student_data = $object->get_result();

		foreach($student_data as $student_row)
		{
			$student_name = $student_row["student_name"];
			$student_profile_img = $student_row["student_image"];
		}

		$object->query = "
		SELECT * FROM subject_wise_exam_detail 
		INNER JOIN subject_soes 
		ON subject_soes.subject_id = subject_wise_exam_detail.subject_id
		WHERE subject_wise_exam_detail.exam_id = '$exam_id' 
		ORDER BY subject_wise_exam_detail.exam_subject_id ASC
		";

		$result = $object->get_result();

		$total_mark = 0;

		$stm = 0;

		foreach($result as $row)
		{

			$subject = $row["subject_name"];

			$subject_total_mark = $row["subject_total_question"] * $row["marks_per_right_answer"];

			$stm = $stm + $subject_total_mark;

			$object->query = "
			SELECT SUM(exam_subject_question_answer.marks) AS total FROM exam_subject_question_answer 
			INNER JOIN exam_subject_question_soes 
			ON exam_subject_question_soes.exam_subject_question_id = exam_subject_question_answer.exam_subject_question_id 
			WHERE exam_subject_question_soes.exam_subject_id = '".$row["exam_subject_id"]."' 
			AND exam_subject_question_answer.student_id = '".$class_row["student_id"]."'
			";

			$mark_result = $object->get_result(); 

			$subject_mark = 0;

			foreach($mark_result as $mark_row)
			{
				$subject_mark = $mark_row["total"];
			}

			$total_mark = $total_mark + $subject_mark;
		}

		$output .= '
		<tr>
			<td><img src="'.$student_profile_img.'" width="50" /></td>
			<td>'.$student_roll_no.'</td>
			<td>'.$student_name.'</td>
			<td>'.$stm.'</td>
			<td>'.$total_mark.'</td>
		</tr>
		';		
	}

	$output .= '</table></td></tr></table>';

	$pdf = new Pdf();

	$pdf->set_paper('letter', 'landscape');

	$file_name = 'Exam Result.pdf';

	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
	exit(0);

}

?>