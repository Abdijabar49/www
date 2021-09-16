<?php
$con=mysqli_connect("localhost","root","","ahmed_mansor");
if(mysqli_connect_error($con))
{
	echo "Failed to connect MySQl:" . mysqli_connect_error();
}
?>