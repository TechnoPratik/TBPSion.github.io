
<?php

include('soes.php');

$object = new soes();

if($object->is_login())
{
    header("location:".$object->base_url."admin/dashboard.php");
}



?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Student Online Exam Management System in PHP</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../vendor/parsley/parsley.css"/>

</head>

<body>
    <div class="container">
        <div class="row justify-content-md-center mt-5">
            <div class="col-sm-6 mt-5">
                <span id="error"></span>
                <div class="card mt-5">
                    <form method="post" class="form-horizontal" id="login_form">
                        <div class="card-header"><h3 class="text-center">Student Exam Management System</h3></div>
                        <div class="card-body">                   
                            <div class="form-group">
                                <input type="text" name="user_email" id="user_email" class="form-control" required autofocus data-parsley-type="email" data-parsley-trigger="keyup" placeholder="Enter Email Address..." />
                            </div>
                            <div class="form-group">
                                <input type="password" name="user_password" id="user_password" class="form-control" required  data-parsley-trigger="keyup" placeholder="Password" />
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <div class="form-group">
                                <button type="submit" name="login_button" id="login_button" class="btn btn-primary btn-user btn-block">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>



    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <script type="text/javascript" src="../vendor/parsley/dist/parsley.min.js"></script>

</body>

</html>

<script>

$(document).ready(function(){

    $('#login_form').parsley();

    $('#login_form').on('submit', function(event){
        event.preventDefault();
        if($('#login_form').parsley().isValid())
        {       
            $.ajax({
                url:"login_action.php",
                method:"POST",
                data:$(this).serialize(),
                dataType:'json',
                beforeSend:function()
                {
                    $('#login_button').attr('disabled', 'disabled');
                    $('#login_button').val('wait...');
                },
                success:function(data)
                {
                    $('#login_button').attr('disabled', false);
                    if(data.error != '')
                    {
                        $('#error').html(data.error);
                        $('#login_button').val('Login');
                    }
                    else
                    {
                        window.location.href = data.url;
                    }
                }
            })
        }
    });

});

</script>