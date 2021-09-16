<?php
include("adminheader.php");
?>
       
        <div class="row">
		
        <!----- Menu Area Start ------>
        <div class="col-md-2 menucontent">
          <?php
			include("menu.php");
			?>
        </div>
        <!---- Menu Ares Ends  -------->
       
<!---- Content Ares Start  -------->
    <div class="col-md-10 maincontent" >
    
    <!----------------   Menu Tab   --------------->
    <div class="panel panel-default contentinside">
        <div class="panel-heading">Change Password</div>
		<?php
$ctins=null;
$msg=null;
$sqlst = "SELECT * FROM users WHERE username='$_SESSION[username]'";
$sqquery = mysqli_query($con,$sqlst);
$row = mysqli_fetch_array($sqquery);

 if(isset($_POST["update1"]))
{
	$sqlupd2 = "UPDATE users SET fname='$_POST[fname]',lname='$_POST[lname]',email='$_POST[email]' WHERE username='$_SESSION[username]'";
	$qresult = mysqli_query($con,$sqlupd);
	$updateusers = mysqli_query($con,$sqlupd2);
	if(!$qresult && !$usersSql)
	{
		
		$msg ="<font color='red'>Failed to update... Problem in sql query<</font>";
	}
	else
	{
		?>
	 <script>
	 alert("Record Updated Successfully");
	 window.location="profile.php";
	 </script>
<?php
	$msg = "<font color='green'>Record Updated successfully...</font>";

	}
}

?>
<?php
$msg1=null;
	if(isset($_POST["changepassword"]))
	{
		
	$result = mysqli_query($con,"SELECT password FROM users where username='$_SESSION[username]' AND password='$_POST[password]'");

if(mysqli_num_rows($result) == 1)
{
	   $sqlupd = "UPDATE doctors SET password='$_POST[newpwd]' WHERE username='$_SESSION[username]'";
	$sqlupd2 = "UPDATE users SET password='$_POST[newpwd]' WHERE username='$_SESSION[username]'";
	$qresult = mysqli_query($con,$sqlupd);
	$updateusers = mysqli_query($con,$sqlupd2);
	if(!$qresult && !$usersSql)
	{
		
		$msg ="<font color='red'>Failed to update... Problem in sql query<</font>";
	}
	else
	{
		$msg1 = "<font color='green'>Password changed successfully...</font>";
		?>
	 <script>
	 alert("Password changed successfully");
	 window.location="profile.php";
	 </script>
<?php
	

	}
}
else
{
	$msg1 = "<center><font color='red'>Invalid Old-password entered..</font></center>";
}


}
?>
		
        <!----------------   Panel body Start   --------------->
            
                        <!----------------   Panel body Ends   --------------->
    </div>
							
    <div class="panel panel-default contentinside">
        
        <!----------------   Panel body Start   --------------->
		<script type="text/javascript">
function validate() 
{
	if(document.showroom.newpwd.value != document.showroom.conpwd.value)
	{
		alert("Password and confirm password not matching.");
		document.showroom.conpwd.focus();
		return false;
	}
 else if(document.showroom.dept.value== "Select")
	{
		alert("Please select department");
		document.showroom.doctid.focus();
		return false;
	}
else if(document.showroom.category.value== "Select")
	{
		alert("Please select Category");
		document.showroom.des.focus();
		return false;
	}
}
</script>
        <div class="panel-body">
		<?php echo $msg1; ?> 
            <form class="form-horizontal" action="" method="post" name="showroom" onsubmit="return validate()">
			<div class="form-group">
                <div class="form-group">
                       <label class="col-sm-2 control-label">Old Password</label>
                       <div class="col-sm-10">
                       <input type="password" class="form-control" name="password" placeholder="Current Password" required="required">
                       </div>
                </div>
                <div class="form-group">
                       <label class="col-sm-2 control-label">New Password</label>
                       <div class="col-sm-10">
                       <input type="password" class="form-control" name="newpwd" placeholder="Enter New Password" required="required">
                       </div>
                </div>
                <div class="form-group">
                       <label class="col-sm-2 control-label">Confirm New Password</label>
                       <div class="col-sm-10">
                       <input type="password" class="form-control" name="conpwd" placeholder="Confirm New Password"  required="required">
                       </div>
                </div>
                <div class="form-group">
                       <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" name="changepassword">Update Password</button>
                       </div>
               </div>
            </form>
        </div>
        <!----------------   Panel body Ends   --------------->
    </div>
    </div>
            
    </div>
</div>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>