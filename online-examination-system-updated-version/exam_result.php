<?php

//single_subject_result.php

include('admin/soes.php');

require_once('class/pdf.php');

$object = new soes();

if(isset($_GET['ec']))
{
	$exam_id = $object->Get_exam_id($_GET['ec']);

	$object->query = "
	SELECT student_soes.student_name, student_soes.student_image, student_to_class_soes.class_id, student_to_class_soes.student_roll_no FROM student_soes 
	INNER JOIN student_to_class_soes 
	ON student_to_class_soes.student_id = student_soes.student_id 
	WHERE student_soes.student_id = '".$_SESSION["student_id"]."'
	";
	$student_data = $object->get_result();
	$student_name = '';
	$student_profile_img = '';
	$student_roll_no = '';
	$class_id = '';
	foreach($student_data as $student_row)
	{
		$student_name = $student_row["student_name"];
		$student_profile_img = str_replace("../", "", $student_row["student_image"]);
		$student_roll_no = $student_row["student_roll_no"];
		$class_id = $student_row["class_id"];
	}
	$output = '
		<h3 align="center">'.$object->Get_exam_name($exam_id).'</h3><br /><br />
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td width="50%">
				<b>Student Name : </b>'.$student_name.'<br /><br />
				<b>Roll No. : </b>'.$student_roll_no.'<br /><br />
				<b>Class : </b>'.$object->Get_class_name($class_id).'<br /><br />
				</td>
				<td width="50%" align="right">
					<img src="'.$student_profile_img.'" width="100" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%" border="1" cellpadding="5" cellspacing="0">
						<tr>
							<th>Subject</th>
							<th>Total Marks</th>
							<th>Marks Obtain</th>
						</tr>
		';



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
		";

		$mark_result = $object->get_result(); 

		$subject_mark = 0;

		foreach($mark_result as $mark_row)
		{
			$subject_mark = $mark_row["total"];
		}

		$total_mark = $total_mark + $subject_mark;

		$output .= '
			<tr>
				<td>'.$subject.'</td>
				<td>'.$subject_total_mark.'</td>
				<td>'.$subject_mark.'</td>
			</tr>
			';
	}

	$output .= '
			<tr>
				<td align="right">Total Marks</td>
				<td>'.$stm.'</td>
				<td>'.$total_mark.'</td>
			</tr>
	';

	$output .= '</table></td></tr></table>';

	//echo $output;

	$pdf = new Pdf();

	$pdf->set_paper('letter', 'A4');

	$file_name = 'Exam Result.pdf';

	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
	exit(0);

}

?>