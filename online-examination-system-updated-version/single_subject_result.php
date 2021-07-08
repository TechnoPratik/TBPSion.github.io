<?php

//single_subject_result.php

include('admin/soes.php');

require_once('class/pdf.php');

$object = new soes();

if(isset($_GET['ec'], $_GET['esc']))
{
	$exam_id = $object->Get_exam_id($_GET['ec']);

	$exam_subject_id = $object->Get_exam_subject_id($_GET['esc']);

	$object->query = "
	SELECT * FROM exam_subject_question_soes 
	WHERE exam_subject_question_soes.exam_id = '$exam_id' 
	AND exam_subject_question_soes.exam_subject_id = '$exam_subject_id' 
	";

	$result = $object->get_result();
	
	$output = '
		<h3 align="center">Exam Result</h3>
		<table width="100%" border="1" cellpadding="5" cellspacing="0">
			<tr>
				<th>Question</th>
				<th>Your Answer</th>
				<th>Answer</th>
				<th>Result</th>
				<th>Marks</th>
			</tr>
		';

	$total_mark = 0;

	foreach($result as $row)
	{
		$question_title = $row["exam_subject_question_title"];

		$object->query = "
		SELECT * FROM exam_subject_question_answer 
		WHERE student_id = '".$_SESSION["student_id"]."' 
		AND exam_subject_question_id = '".$row["exam_subject_question_id"]."'
		";

		$object->execute();

		if($object->row_count() > 0)
		{
			$answer_result = $object->statement_result();
			foreach($answer_result as $answer_row)
			{
				$student_answer = $answer_row["student_answer_option"];

				if($answer_row["marks"] > '0')
				{
					$question_result = 'Right';
				}
								
				if($answer_row["marks"] < '0')
				{
					$question_result = 'Wrong';
				}
				$marks = $answer_row["marks"];

				$total_mark = $total_mark + $marks;
			}
		}
		else
		{
			$student_answer = 'Not Attempt';
			$question_result = 'Not Attempt';
			$marks = 'Not Attempt';
		}

		$object->query = "
		SELECT * FROM question_option_soes 
		WHERE exam_subject_question_id = '".$row["exam_subject_question_id"]."'
		";
		$option_result = $object->get_result();

		foreach($option_result as $option_row)
		{
			if($option_row["question_option_number"] == $student_answer)
			{
				$student_answer = $option_row["question_option_title"];
			}

			if($option_row["question_option_number"] == $row["exam_subject_question_answer"])
			{
				$orignal_answer = $option_row["question_option_title"];
			}
		}

		$output .= '
			<tr>
				<td>'.$question_title.'</td>
				<td>'.$student_answer.'</td>
				<td>'.$orignal_answer.'</td>
				<td>'.$question_result.'</td>
				<td>'.$marks.'</td>
			</tr>
			';

		
	}

	$output .= '
			<tr>
				<td colspan="4" align="right">Total Marks</td>
				<td>'.$total_mark.'</td>
			</tr>
	';

	$output .= '</table>';

	//echo $output;

	$pdf = new Pdf();

	$pdf->set_paper('letter', 'landscape');

	$file_name = 'Exam Result.pdf';

	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
	exit(0);

}

?>