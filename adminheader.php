<?php
include("auth.php");
include("dbconnection.php");
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="images/logo.jpg" rel="icon"/>
        <title>Alaabo</title>
	<!-- Form Validation system -->
<link rel="stylesheet" type="text/css" href="formValidator/validationEngine.jquery.css" />
<link rel="stylesheet" type="text/css" href="css/message.css" media="all">
<script type="text/javascript" src="formValidator/jquery.validationEngine.js"></script>
<script type="text/javascript" src="js/validation.js"></script>
<script src="datetimepicker_css.js"></script> 
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/dashboard.css" rel="stylesheet">
        <script src="js/jquery.js"></script>
        <script type="text/javascript">
                $(document).ready(function()
                {

                        $('#doctorlist').show();
                        $('.doctor li:first-child a').addClass('active');
                        $('.doctor li a').click(function(e){

                                var tabDiv=this.hash;
                                $('.doctor li a').removeClass('active');
                                $(this).addClass('.active');
                                $('.switchgroup').hide();
                                $(tabDiv).fadeIn();
                                e.preventDefault();

                        });


                });
        </script>
    </head>
	<!--- Header Ends --------->
       <body>
        <div class="container-fluid">
            
        <!--- Header Start -------->
        <div class="row header">

            <div class="col-md-10">
          <a href="dashboard.php"><img src="images/logo.jpg" alt="Alaabo" border="0" height="70" width="200" /></a>

            </div>
		
            <div class="col-md-2 ">
                <ul class="nav nav-pills ">
                    <li class="dropdown dmenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["fname"]. " ".$_SESSION["lname"]; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu ">
                                <li><a href="profile.php">Change Profile</a></li>
                                <li role="separator" class="divider"></li>
                                 <li><a href="logout.php">Logout</a></li>
                            </ul>
                     </li>
                </ul>
            </div>
        </div>