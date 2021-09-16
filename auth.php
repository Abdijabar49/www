<?php
session_start();
include("dbconnection.php");
if(!isset($_SESSION['username']))  {

 ?>
 <script>
 alert("Marka hore loging samey!!");
 window.location='login.php';
 </script>
<?php
}
?>