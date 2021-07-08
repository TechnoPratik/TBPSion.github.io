<?php

//exam.php

include('admin/soes.php');

$object = new soes();

if(!$object->is_student_login())
{
    header("location:".$object->base_url."");
}
$ec = '';

$exam_result_datetime = '';

if(isset($_GET["ec"]))
{
    $ec = $_GET["ec"];
    $object->query = "
    SELECT exam_id, exam_result_datetime FROM exam_soes 
    WHERE exam_code = '".$_GET["ec"]."';
    ";

    $result = $object->get_result();

    foreach($result as $row)
    {
        $exam_result_datetime = $row["exam_result_datetime"];
        $object->query = "
        SELECT * FROM subject_wise_exam_detail 
        INNER JOIN subject_soes 
        ON subject_soes.subject_id = subject_wise_exam_detail.subject_id 
        WHERE subject_wise_exam_detail.exam_id = '".$row["exam_id"]."'
        ";

        $exam_result = $object->get_result();
    }
}
else
{
    header('location:exam.php');
}

if(isset($_SESSION["ec"]))
{
    unset($_SESSION["ec"]);
}
if(isset($_SESSION["esc"]))
{
    unset($_SESSION["esc"]);
}

include('header.php');
                
?>

                    <!-- Page Heading -->
                    <h1 class="h3 mt-4 mb-4 text-gray-800">Exam Schedule Details</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Subject List</h6>
                            	</div>
                            	<div class="col" align="right">
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <?php
                            if(isset($exam_result))
                            {
                            ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Exam Date & Time</th>
                                        <th>Day</th>
                                        <th>Exam Duration</th>
                                        <th>Total Question</th>
                                        <th>Correct Answer Marks</th>
                                        <th>Wrong Answer Marks</th>
                                        <th>Status</th>
                                        <th>Action</th>               
                                    </tr>
                                    <?php

                                    foreach($exam_result as $row)
                                    {
                                        $status = '';

                                        $action_button = '';

                                        $start_time = $row["subject_exam_datetime"];

                                        $duration = $object->Get_exam_duration($row["exam_id"]) . ' minute';

                                        $end_time = strtotime($start_time . '+' . $duration);

                                        $end_time = date('Y-m-d H:i:s', $end_time);

                                        if(time() >= strtotime($start_time) && time() <= strtotime($end_time))
                                        {
                                            $data = array(
                                                ':subject_exam_status'  =>  'Started',
                                                ':exam_subject_id'      =>  $row["exam_subject_id"]    
                                            );
                                            $object->query = "
                                            UPDATE subject_wise_exam_detail 
                                            SET subject_exam_status = :subject_exam_status 
                                            WHERE exam_subject_id = :exam_subject_id
                                            ";

                                            $object->execute($data);

                                            $status = '<span class="badge badge-primary">Started</span>';

                                            $action_button = '<button type="button" class="btn btn-primary btn-sm view_subject_exam" data-ec="'.$ec.'" data-esc="'.$row["subject_exam_code"].'"><i class="fas fa-pencil-alt"></i></button>';
                                        }
                                        else
                                        {
                                            if(time() > strtotime($end_time))
                                            {
                                                $data = array(
                                                    ':subject_exam_status'  =>  'Completed',
                                                    ':exam_subject_id'      =>  $row["exam_subject_id"]    
                                                );
                                                $object->query = "
                                                UPDATE subject_wise_exam_detail 
                                                SET subject_exam_status = :subject_exam_status 
                                                WHERE exam_subject_id = :exam_subject_id
                                                ";

                                                $object->execute($data);

                                                $status = '<span class="badge badge-dark">Completed</span>';

                                                if($exam_result_datetime == '0000-00-00 00:00:00')
                                                {
                                                    $action_button = '';
                                                }
                                                else
                                                {
                                                    if(time() > strtotime($exam_result_datetime))
                                                    {
                                                        $action_button = '<a href="single_subject_result.php?ec='.$ec.'&esc='.$row["subject_exam_code"].'" target="_blank" class="btn btn-danger btn-sm">PDF</a>';
                                                    }
                                                    else
                                                    {
                                                        $action_button = '';
                                                    }
                                                }

                                                
                                            }

                                            if(strtotime($start_time) > time())
                                            {
                                                $status = '<span class="badge badge-warning">Pending</span>';
                                                //$action_button = '<button type="button" class="btn btn-primary btn-sm view_subject_exam" data-ec="'.$ec.'" data-esc="'.$row["subject_exam_code"].'"><i class="fas fa-pencil-alt"></i></button>';
                                                $action_button = '';
                                            }
                                        }
                                        echo '
                                        <tr>
                                            <td>'.$row["subject_name"].'</td>
                                            <td>'.$row["subject_exam_datetime"].'</td>
                                            <td>'.date('l', strtotime($row["subject_exam_datetime"])).'</td>
                                            <td>'.$duration.' Minute</td>
                                            <td>'.$row["subject_total_question"].' Question</td>
                                            <td><b class="text-success">'.$row["marks_per_right_answer"].' Marks</b></td>
                                            <td><b class="text-danger">-'.$row["marks_per_wrong_answer"].' Marks</b></td>
                                            <td>'.$status.'</td>
                                            <td>'.$action_button.'</td>
                                        </tr>
                                        ';
                                    }

                                    ?>
                                </table>
                            <?php
                            }
                            ?>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){
    
    $('.view_subject_exam').click(function(){
        var ec = $(this).data('ec');
        var esc = $(this).data('esc');
        $.ajax({
            url:"ajax_action.php",
            method:"POST",
            data:{page:"view_exam", action:"view_subject_exam", ec:ec, esc:esc},
            success:function(data)
            {
                window.open(data,'_blank');
            }
        });
    });  

});
</script>