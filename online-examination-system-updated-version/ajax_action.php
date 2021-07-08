<?php

//ajax_action.php

include('admin/soes.php');

$object = new soes();

if(isset($_POST['page']))
{
	if($_POST['page'] == 'login')
	{
		if($_POST['action'] == 'check_login')
		{
			sleep(2);
			$error = '';
			$url = '';
			$data = array(
				':student_email_id'	=>	$_POST["student_email_id"]
			);

			$object->query = "
				SELECT * FROM student_soes 
				WHERE student_email_id = :student_email_id
			";

			$object->execute($data);

			$total_row = $object->row_count();

			if($total_row == 0)
			{
				$error = '<div class="alert alert-danger">Wrong Email Address</div>';
			}
			else
			{
				$result = $object->statement_result();

				foreach($result as $row)
				{
					if($row['student_email_verified'] == 'Yes')
					{
						if($row["student_status"] == 'Enable')
						{
							if($_POST["student_password"] == $row["student_password"])
							{
								$_SESSION['student_id'] = $row['student_id'];
								$url = $object->base_url . 'student_dashboard.php';
							}
							else
							{
								$error = '<div class="alert alert-danger">Wrong Password</div>';
							}
						}
						else
						{
							$error = '<div class="alert alert-danger">Sorry, Your account has been disable, contact Admin</div>';
						}
					}
					else
					{
						$error = '<div class="alert alert-danger">You have not verify you email address, so for email verification, click <a href="resend_email_verification.php">here</a></div>';
					}
				}
			}

			$output = array(
				'error'		=>	$error,
				'url'		=>	$url
			);

			echo json_encode($output);
		}
	}

	if($_POST['page'] == 'resend_email_verificaiton')
	{
		if($_POST['action'] == 'send_verificaton_email')
		{
			sleep(2);
			$error = '';
			$data = array(
				':student_email_id'	=>	$_POST["student_email_id"]
			);

			$object->query = "
				SELECT * FROM student_soes 
				WHERE student_email_id = :student_email_id
			";

			$object->execute($data);

			$total_row = $object->row_count();

			if($total_row == 0)
			{
				$error = '<div class="alert alert-danger">Email Address Not Found</div>';
			}
			else
			{
				$result = $object->statement_result();

				foreach($result as $row)
				{
					if($row['student_email_verified'] == 'No')
					{
						$verification_code = md5(uniqid());
						$data = array(
							':student_email_verification_code'		=>	$verification_code,
							':student_id'							=>	$row["student_id"]
						);

						$object->query = "
						UPDATE student_soes 
						SET student_email_verification_code = :student_email_verification_code 
						WHERE student_id = :student_id
						";

						$object->execute($data);

						require_once('class/class.phpmailer.php');

						$subject = 'Student Verification Email for Online Exam System';

						$body = '
						<p>Hello '.$row["student_name"].'.</p>
						<p>This is a verification eMail for your '.$row["student_email_id"].' email address, and without email verification you can not take part in online exam, So please click the link to verify your eMail address by clicking this <a href="'.$object->base_url.'verify_email.php?type=student&code='.$verification_code.'" target="_blank"><b>link</b></a>.</p>
						<p>In case if you have any difficulty please eMail us.</p>
						<p>Thank you,</p>
						<p>Online Student Exam System</p>
						';

						$object->send_email($row["student_email_id"], $subject, $body);

						$error = '<div class="alert alert-success">Verification eMail has been send to <b>'.$row["student_email_id"].'</b></div>';

					}
					else
					{
						$error = '<div class="alert alert-info">Your email already verified, so you can login into system, by click <a href="login.php">here</a></div>';
					}
				}
			}

			$output = array(
				'error'		=>	$error,
			);

			echo json_encode($output);
		}
	}

	if($_POST['page'] == 'forget_password')
	{
		if($_POST['action'] == 'get_password')
		{
			sleep(2);
			$error = '';
			$data = array(
				':student_email_id'	=>	$_POST["student_email_id"]
			);

			$object->query = "
				SELECT * FROM student_soes 
				WHERE student_email_id = :student_email_id
			";

			$object->execute($data);

			$total_row = $object->row_count();

			if($total_row == 0)
			{
				$error = '<div class="alert alert-danger">Email Address not Found</div>';
			}
			else
			{
				$result = $object->statement_result();

				foreach($result as $row)
				{
					if($row['student_email_verified'] == 'Yes')
					{
						if($row["student_status"] == 'Enable')
						{
							require_once('class/class.phpmailer.php');

							$subject = 'Online Student Exam System Password Detail';

							$body = '
							<p>Hello '.$row["student_name"].'.</p>
							<p>For login into this Online Student Exam System by visiting <a href="'.$object->base_url.'login.php" target="_blank"><b>'.$object->base_url.'login.php</a></b> this link. Below you can find password details.</a></p>
							<p><b>Password : </b>'.$row["student_password"].'</p>
							<p>In case if you have any difficulty please eMail us.</p>
							<p>Thank you,</p>
							<p>Online Student Exam System</p>
							';

							$object->send_email($row["student_email_id"], $subject, $body);

							$error = '<div class="alert alert-success">Hey <b>'.$row["student_name"].'</b> your password details has been send to <b>'.$row["student_email_id"].'</b> email address.</div>';
						}
						else
						{
							$error = '<div class="alert alert-danger">Sorry, Your account has been disable, contact Admin</div>';
						}
					}
					else
					{
						$error = '<div class="alert alert-danger">You have not verify you email address, so for email verification, click <a href="resend_email_verification.php">here</a></div>';
					}
				}
			}

			$output = array(
				'error'		=>	$error
			);

			echo json_encode($output);
		}
	}

	if($_POST['page'] == 'exam')
	{
		if($_POST["action"] == 'fetch')
		{
			$order_column = array('exam_title', 'exam_duration', 'exam_result_datetime', 'exam_status');

			$output = array();

			$main_query = "
			SELECT * FROM exam_soes 
			WHERE exam_status != 'Pending' AND exam_class_id = '".$_POST["class_id"]."' 
			";

			$search_query = '';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'AND (exam_title LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR exam_duration LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR exam_result_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR exam_status LIKE "%'.$_POST["search"]["value"].'%") ';
			}

			if(isset($_POST["order"]))
			{
				$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
			}
			else
			{
				$order_query = 'ORDER BY exam_id DESC ';
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
						if(time() > strtotime($exam_last_subject_end_datetime))
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

							if($exam_result_datetime != 'Not Publish')
							{
								if(time() > strtotime($exam_result_datetime))
								{
									$action_button = '<a href="exam_result.php?ec='.$row["exam_code"].'" target="_blank" class="btn btn-danger btn-sm">Result</a>';
								}
								else
								{
									$action_button = '';
								}
							}
							else
							{
								$action_button = '';
							}
						}

						if(strtotime($first_subject_datetime) > time())
						{
							$status = '<span class="badge badge-success">Created</span>';
							$action_button = '';
						}
					}
				}
				else
				{
					if($row['exam_status'] == 'Created')
					{
						$status = '<span class="badge badge-success">Created</span>';
					}

					if($row['exam_status'] == 'Started')
					{
						$status = '<span class="badge badge-primary">Started</span>';
					}

					if($row['exam_status'] == 'Completed')
					{
						$status = '<span class="badge badge-dark">Completed</span>';
					}
					$action_button = '';
				}
				

				$sub_array[] = $status;

				//$sub_array[] = '<button type="button" class="btn btn-sm btn-secondary view_timetable" data-id="'.$row["exam_id"].'">View Exam Schedule</button>';

				$sub_array[] = '<a href="view_exam.php?ec='.$row["exam_code"].'">View</a>';

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

		if($_POST["action"] == 'fetch_timetable')
		{
			$object->query = "
			SELECT * FROM subject_wise_exam_detail 
			INNER JOIN subject_soes 
			ON subject_soes.subject_id = subject_wise_exam_detail.subject_id 
			WHERE subject_wise_exam_detail.exam_id = '".$_POST["exam_id"]."'
			";

			$result = $object->get_result();

			$output = '
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr>
						<th>Subject Name</th>
						<th>Exam Date & Time</th>
						<th>Exam Duration</th>
						<th>Total Question</th>
						<th>Correct Answer Marks</th>
						<th>Wrong Answer Marks</th>						
					</tr>
			';

			foreach($result as $row)
			{
				$output .= '
					<tr>
						<td>'.$row["subject_name"].'</td>
						<td>'.$row["subject_exam_datetime"].'</td>
						<td>'.$object->Get_exam_duration($_POST["exam_id"]).' Minute</td>
						<td>'.$row["subject_total_question"].' Question</td>
						<td><b class="text-success">'.$row["marks_per_right_answer"].' Marks</b></td>
						<td><b class="text-danger">-'.$row["marks_per_wrong_answer"].' Marks</b></td>
					</tr>
				';
			}

			$output .= '
				</table>
			</div>
			';
			echo $output;
		}
	}

	if($_POST['page'] == 'view_exam')
	{
		if($_POST['action'] == 'view_subject_exam')
		{
			$_SESSION['ec'] = $_POST['ec'];
			$_SESSION['esc'] = $_POST['esc'];
			echo 'view_subject_exam.php';
		}
	}

	if($_POST['page'] == 'view_subject_exam')
	{
		if($_POST['action'] == 'load_question')
		{
			if($_POST['question_id'] == '')
			{
				$object->query = "
				SELECT * FROM exam_subject_question_soes 
				WHERE exam_id = '".$_POST["exam_id"]."' 
				AND exam_subject_id = '".$_POST["exam_subject_id"]."' 
				ORDER BY exam_subject_question_id ASC 
				LIMIT 1
				";
			}
			else
			{
				$object->query = "
				SELECT * FROM exam_subject_question_soes 
				WHERE exam_subject_question_id = '".$_POST["question_id"]."' 
				";
			}

			$result = $object->get_result();

			$output = '';

			foreach($result as $row)
			{
				$output .= '
				<div class="card">
					<div class="card-header"><b>Question - </b>'.$row["exam_subject_question_title"].'</div>
					<div class="card-body">
						<div class="row">
				';

				$object->query = "
				SELECT * FROM question_option_soes 
				WHERE exam_subject_question_id = '".$row['exam_subject_question_id']."'
				";
				$sub_result = $object->get_result();

				$count = 1;
				$temp_array = ['A', 'B', 'C', 'D'];
				$temp_count = 0;
				foreach($sub_result as $sub_row)
				{
					$is_checked = '';

					if($object->Get_student_question_answer_option($row['exam_subject_question_id'], $_SESSION['student_id']) == $count)
					{
						$is_checked = 'checked';
					}

					$output .= '
					<div class="col-md-6 mb-4">
						<div class="radio">
							<label><b>'.$temp_array[$temp_count].'.&nbsp;&nbsp;</b><input type="radio" name="option_1" class="answer_option" data-question_id="'.$row['exam_subject_question_id'].'" data-id="'.$count.'" '.$is_checked.'> '.$sub_row["question_option_title"].'</label>
						</div>
					</div>
					';
					$count++;
					$temp_count++;
				}
				$output .= '
				</div>
				';
				$object->query = "
				SELECT exam_subject_question_id FROM exam_subject_question_soes 
				WHERE exam_subject_question_id < '".$row['exam_subject_question_id']."' 
				AND exam_id = '".$_POST["exam_id"]."' 
				AND exam_subject_id = '".$_POST["exam_subject_id"]."' 
				ORDER BY exam_subject_question_id DESC 
				LIMIT 1";

				$previous_result = $object->get_result();

				$previous_id = '';
				$next_id = '';

				foreach($previous_result as $previous_row)
				{
					$previous_id = $previous_row['exam_subject_question_id'];
				}

				$object->query = "
				SELECT exam_subject_question_id FROM exam_subject_question_soes 
				WHERE exam_subject_question_id > '".$row['exam_subject_question_id']."' 
				AND exam_id = '".$_POST["exam_id"]."' 
				AND exam_subject_id = '".$_POST["exam_subject_id"]."' 
				ORDER BY exam_subject_question_id ASC 
				LIMIT 1";
  				
  				$next_result = $object->get_result();

  				foreach($next_result as $next_row)
				{
					$next_id = $next_row['exam_subject_question_id'];
				}

				$if_previous_disable = '';
				$if_next_disable = '';

				if($previous_id == "")
				{
					$if_previous_disable = 'disabled';
				}
				
				if($next_id == "")
				{
					$if_next_disable = 'disabled';
				}

				$output .= '
				  	<div align="center">
				   		<button type="button" name="previous" class="btn btn-info btn-lg previous" id="'.$previous_id.'" '.$if_previous_disable.'>Previous</button>
				   		<button type="button" name="next" class="btn btn-warning btn-lg next" id="'.$next_id.'" '.$if_next_disable.'>Next</button>
				  	</div>
				  	</div></div>';
			}

			echo $output;
		}

		if($_POST['action'] == 'question_navigation')
		{
			$object->query = "
				SELECT exam_subject_question_id FROM exam_subject_question_soes 
				WHERE exam_id = '".$_POST["exam_id"]."' 
				AND exam_subject_id = '".$_POST["exam_subject_id"]."' 
				ORDER BY exam_subject_question_id ASC 
				";
			$result = $object->get_result();
			$output = '
			<div class="card">
				<div class="card-header"><b>Question Navigation</b></div>
				<div class="card-body">
					<div class="row">
			';
			$count = 1;
			foreach($result as $row)	
			{
				$output .= '
				<div class="col-sm-1 mb-2">
					<button type="button" class="btn btn-primary question_navigation" data-question_id="'.$row["exam_subject_question_id"].'">'.$count.'</button>
				</div>
				';
				$count++;
			}
			$output .= '
				</div>
			</div></div>
			';
			echo $output;
		}

		if($_POST['action'] == 'answer')
		{
			$exam_right_answer_mark = $object->Get_question_right_answer_mark($_POST["exam_subject_id"]);
			$exam_wrong_answer_mark = $object->Get_question_wrong_answer_mark($_POST["exam_subject_id"]);
			$orignal_answer = $object->Get_question_answer_option($_POST["question_id"]);
			$marks = 0;
			if($orignal_answer == $_POST['answer_option'])
			{
				$marks = '+'.$exam_right_answer_mark;
			}
			else
			{
				$marks = '-' . $exam_wrong_answer_mark;
			}

			$object->query = "
			SELECT * FROM exam_subject_question_answer 
			WHERE student_id='".$_SESSION["student_id"]."' 
			AND exam_subject_question_id = '".$_POST["question_id"]."'
			";

			$object->execute();

			if($object->row_count() > 0)
			{
				$object->query = "
				UPDATE exam_subject_question_answer 
			   	SET student_answer_option='".$_POST['answer_option']."', 
			   	marks = '".$marks."' 
			   	WHERE student_id='".$_SESSION["student_id"]."' AND exam_subject_question_id = '".$_POST["question_id"]."' 
				";
			}
			else
			{
				$object->query = "
				INSERT INTO exam_subject_question_answer 
			   	(student_id, exam_subject_question_id, student_answer_option, marks) 
			   	VALUES ('".$_SESSION["student_id"]."', '".$_POST["question_id"]."', '".$_POST['answer_option']."', '".$marks."')
				";
			}

			$object->execute();

			echo 'done';
		}
	}
}

?>