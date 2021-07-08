<?php

//exam.php

include('admin/soes.php');

$object = new soes();

if(!$object->is_student_login())
{
    header("location:".$object->base_url."");
}

$exam_id = '';
$exam_title = '';
$class_id = '';
$exam_duration = '';

$subject_id = '';
$exam_subject_id = '';
$total_question = '';
$marks_per_right_answer = '';
$marks_per_wrong_answer = '';
$subject_exam_start_time = '';
$subject_exam_end_time = '';
$remaining_minutes = '';
$subject_exam_status = '';

$student_name = '';
$student_roll_no = '';
$student_image = '';

if(isset($_SESSION['ec']))
{
    $object->query = "
    SELECT * FROM exam_soes 
    WHERE exam_code = '".$_SESSION['ec']."';
    ";

    $result = $object->get_result();

    foreach($result as $row)
    {
        $exam_id = $row["exam_id"];
        $exam_title = $row["exam_title"];
        $class_id = $row["exam_class_id"];
        $exam_duration = $row["exam_duration"];
    }
}
else
{
    header('location:view_exam.php');
}

if(isset($_SESSION["esc"]))
{
    $object->query = "
    SELECT * FROM subject_wise_exam_detail 
    WHERE subject_exam_code = '".$_SESSION['esc']."';
    ";

    $result = $object->get_result();

    foreach($result as $row)
    {
        $subject_id = $row["subject_id"];
        $exam_subject_id = $row["exam_subject_id"];
        $total_question = $row["subject_total_question"];
        $marks_per_right_answer = $row["marks_per_right_answer"];
        $marks_per_wrong_answer = $row["marks_per_wrong_answer"];
        $subject_exam_start_time = $row["subject_exam_datetime"];
        $subject_exam_end_time = strtotime($subject_exam_start_time . '+' . $exam_duration . ' minute');
        $subject_exam_end_time = date('Y-m-d H:i:s', $subject_exam_end_time);
        $total_second = strtotime($subject_exam_end_time) - strtotime($subject_exam_start_time);
        $remaining_minutes = strtotime($subject_exam_end_time) - time();
        $subject_exam_status = $row["subject_exam_status"];
    }
}
else
{
    header('location:view_exam.php');
}

$object->query = "
SELECT student_soes.student_name, student_soes.student_image, student_to_class_soes.student_roll_no FROM student_to_class_soes 
INNER JOIN student_soes 
ON student_soes.student_id = student_to_class_soes.student_id 
WHERE student_to_class_soes.student_id = '".$_SESSION["student_id"]."' 
ORDER BY student_to_class_soes.student_to_class_id DESC 
LIMIT 1 
";

$result = $object->get_result();

foreach($result as $row)
{
    $student_name = $row["student_name"];
    $student_roll_no = $row["student_roll_no"];
    $student_image = str_replace("../", "", $row["student_image"]);
}

include('header.php');
                
?>

                    <!-- Page Heading -->
                    <h1 class="h3 mt-4 mb-4 text-gray-800"></h1>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                <b>Exam : </b><?php echo $exam_title; ?>
                                </div>
                                <div class="col-md-6">
                                    <b>Subject : </b><?php echo $object->Get_Subject_name($subject_id); ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div id="single_question_area" class="mb-2"></div>
                                    <div id="question_navigation_area" class="mb-2"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center mt-2 mb-2">
                                        <div id="exam_timer" data-timer="<?php echo $remaining_minutes; ?>" style="max-width:375px; width: 100%; height: 190px; margin:0 auto"></div>
                                    </div>
                                    <div class="card shadow">
                                        <div class="card-header"><b>Student Details</b></div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p class="text-center"><img src="<?php echo $student_image; ?>" class="img-fluid img-thumbnail" width="100" /></p>
                                                </div>
                                                <div class="col-md-8">
                                                    <b>Roll No : </b><?php echo $student_roll_no; ?><br />
                                                    <b>Name : </b><?php echo $student_name; ?><br />
                                                    <b>Class : </b><?php echo $object->Get_class_name($class_id); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="container">
                            <video autoplay="true" id="videoElement">
                            
                            </video>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){
    var exam_id = "<?php echo $exam_id; ?>";
    var exam_subject_id = "<?php echo $exam_subject_id; ?>";
    function load_question(question_id = '', exam_id, exam_subject_id)
    {
        $.ajax({
            url:"ajax_action.php",
            method:"POST",
            data:{exam_id:exam_id, exam_subject_id:exam_subject_id, question_id:question_id, page:'view_subject_exam', action:'load_question'},
            success:function(data)
            {
                $('#single_question_area').html(data);
            }
        })
    }

    load_question('', exam_id, exam_subject_id);

    question_navigation();

    function question_navigation()
    {
        $.ajax({
            url:"ajax_action.php",
            method:"POST",
            data:{exam_id:exam_id, exam_subject_id:exam_subject_id, page:'view_subject_exam', action:'question_navigation'},
            success:function(data)
            {
                $('#question_navigation_area').html(data);
            }
        })
    }

    $(document).on('click', '.next', function(){
        var question_id = $(this).attr('id');
        load_question(question_id, exam_id, exam_subject_id);
    });

    $(document).on('click', '.previous', function(){
        var question_id = $(this).attr('id');
        load_question(question_id, exam_id, exam_subject_id);
    });


    $(document).on('click', '.question_navigation', function(){
        var question_id = $(this).data('question_id');
        load_question(question_id, exam_id, exam_subject_id);
    });

    $(document).on('click', '.answer_option', function(){
        var question_id = $(this).data('question_id');
        var answer_option = $(this).data('id');
        $.ajax({
            url:"ajax_action.php",
            method:"POST",
            data:{question_id:question_id, answer_option:answer_option, exam_id:exam_id, exam_subject_id:exam_subject_id, page:'view_subject_exam', action:'answer'},
            success:function()
            {

            }
        });
    });

    $("#exam_timer").TimeCircles({
        "animation": "smooth",
        "bg_width": 1.2,
        "fg_width": 0.1,
        "circle_bg_color": "#eee",
        "time": {
            "Days":
            {
                "show": false
            },
            "Hours":
            {
                "show": false
            },
            "Minutes": {
                "text": "Minutes",
                "color": "#ffc107",
                "show": true
            },
            "Seconds": {
                "text": "Seconds",
                "color": "#007bff",
                "show": true
            }
        }
    });

    var total_second = "<?php echo $total_second; ?>";
    var remaining_minutes = "<?php echo $remaining_minutes; ?>";

    $("#exam_timer").TimeCircles().addListener(function(unit, value, total) {
        if(total < 1)
        {
            $("#exam_timer").TimeCircles().destroy();
            alert('Exam Time Completed');
            location.href="view_exam.php?ec=<?php echo $_SESSION["ec"]; ?>";
        }
    });

});
var video = document.querySelector("#videoElement");
if (navigator.mediaDevices.getUserMedia) {
  navigator.mediaDevices.getUserMedia({ video: true })
    .then(function (stream) {
      video.srcObject = stream;
    })
    .catch(function (err0r) {
      console.log("Something went wrong!");
    });
}
</script>

<style>
#container {
    margin: 0px auto;
    width: 500px;
    height: 375px;
    border: 10px #333;
}
#videoElement {
    width: 500px;
    height: 375px;
    background-color: #666;
}
</style>