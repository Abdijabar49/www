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
                <div class="panel-heading">Add Users</div>
                <!----------------   Panel body Start   --------------->
                <div class="panel-body">
                       

    <!----------------   Display Doctor Data List Start  --------------->
    <?php 
	$msg=null;
$dt= date("Y-m-d"); 
if(isset($_POST["insert"]))
{ 
	if(isset($_GET["editid"]))
		{
			$sqlupd = "UPDATE users SET firstName='$_POST[fname]',lastName='$_POST[lname]',contact='$_POST[contact]',jobTitle='$_POST[jobTitle]',gender='$_POST[gender]',userType='$_POST[userType]',email='$_POST[email]',password='$_POST[password]',status='$_POST[status]' WHERE userID='$_GET[editid]'";
	$qresult = mysqli_query($con,$sqlupd);
	$ctins =  mysqli_affected_rows($con);
			if(!$qresult)
	{
		$msg = "<br><font color='green' size='4'>Failed to update record </font>";
	}
	else
	{
		$msg = "<font color='green' size='4'><br>Record updated successfully...</font>";

	}
	}
		else
		{
 $sql1 = "select username from users where username = '$_POST[username]'";
    $result1 = mysqli_query($con,$sql1) or die ("Couldn't execute query.");										  
    
	$num1=mysqli_num_rows($result1);
	if($num1 == 1)
	{
		
		$msg = "<font color='red'> Sorry ! username allready exists , Please Try another one  </font><font color='darkBlue'>".$_POST["username"]."</font>";
	}
	else
	{   
$usersSql=mysqli_query($con,"insert into users(firstName,lastName,contact,email,jobTitle,gender,username,password,userType,status,registerDate) values('$_POST[fname]','$_POST[lname]','$_POST[contact]','$_POST[email]','$_POST[jobTitle]','$_POST[gender]','$_POST[username]','$_POST[password]','$_POST[userType]','$_POST[status]','$dt')");
if(!$usersSql)
	{
		$msg ="<font color='red' size='4'>Failed to insert... Problem in sql query</font>";
	}
	else
	{
		$msg = "<font color='green' size='4'><br>Record Added successfully...</font>";

	}
	}
	
		}
}

$query=mysqli_query($con,"SELECT max(userid) FROM users");
$rs=mysqli_fetch_array($query);
$GetID=$rs['max(userid)']+1;

?>
<script type="text/javascript">
function validate() 
{
	if(document.showroom.password.value != document.showroom.conpass.value)
	{
		alert("Password and confirm password not matching.");
		document.showroom.conpass.focus();
		return false;
	}
 else if(document.showroom.gender.value== "Select Gender")
	{
		alert("Please select Gender");
		document.showroom.doctid.focus();
		return false;
	}
else if(document.showroom.userType.value== "Select Type")
	{
		alert("Please select Type");
		document.showroom.doctid.focus();
		return false;
	}
else if(document.showroom.status.value== "Select Status")
	{
		alert("Please select Status");
		document.showroom.doctid.focus();
		return false;
	}
}
</script>
						   <?php
$ctins=null;
if(isset($_GET["editid"]))
{
$sqlst = "SELECT * FROM users WHERE userid='$_GET[editid]'";
$sqquery = mysqli_query($con,$sqlst);
$row = mysqli_fetch_array($sqquery);
}
 
?>

        <div id="doctorlist" class="switchgroup">

		  <center> <?php echo $msg; ?> </center> <br>
		  <form class="form-horizontal" action="" method="post" name="showroom" onsubmit="return validate()">
			  
                    <div class="form-group">
                    <label  class="col-sm-2 control-label">User Id:</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="doctid" placeholder="Doctor ID" value="<?php  echo $GetID;?>" required="required" readonly >
                    </div>
                    </div>

                    <div class="form-group">
                                <label  class="col-sm-2 control-label">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name"  value="<?php if(isset($_GET["editid"])){echo $row['firstName']; } ?>">
                               
								</div>
								 <script type="text/javascript">
									var f1 = new LiveValidation('fname');
									f1.add(Validate.Presence,{failureMessage: " Please enter Firstname"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid Firstname"});
								 </script>
								 
					     </div>
							 <div class="form-group">
                                <label  class="col-sm-2 control-label">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name"  value="<?php if(isset($_GET["editid"])){echo $row['lastName']; } ?>">
                                </div>
								 <script type="text/javascript">
									var f1 = new LiveValidation('lname');
									f1.add(Validate.Presence,{failureMessage: " Please enter Lastname"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid Lastname"});
								 </script>
								
                                </div>
								 <div class="form-group">
                          <label class="col-sm-2 control-label">Contact</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="contact" id="contact" placeholder="Contact"  value="<?php if(isset($_GET["editid"])){echo $row['contact']; } ?>">
                          </div>
						   <script type="text/javascript">
								var f1 = new LiveValidation('contact');
							   f1.add(Validate.Presence,{failureMessage: " Please enter contact"});
							   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: " It allows only numbers"});
							   f1.add( Validate.Length, { maximum: 10 } );
						  </script>
                    </div>
						 <div class="form-group">
                          <label class="col-sm-2 control-label">Job Title</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="jobTitle" id="jobTitle"  placeholder="Job Title" value="<?php if(isset($_GET["editid"])){echo $row['jobTitle']; } ?>">
                          </div>
								<script type="text/javascript">
									var f1 = new LiveValidation('jobTitle');
									f1.add(Validate.Presence,{failureMessage: " Please enter jobTitle"});
								  
								 </script>
						 
                    </div>
                    <div class="form-group">
                          <label class="col-sm-2 control-label">Email</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="email" id="email" placeholder="Email"  value="<?php if(isset($_GET["editid"])){echo $row['email']; } ?>">
                          </div>
						  <script type="text/javascript">
								var f1 = new LiveValidation('email');
								f1.add(Validate.Presence,{failureMessage: " Please enter Email"});
								f1.add( Validate.Email );
						</script>
                    </div>
					<div class="form-group">
                        <label class="col-sm-2 control-label">Gender</label>
                          <div class="col-sm-10">
						<select name="gender" class="form-control" >
								<?php
								$arr = array("Select Gender","Male","Female");
								foreach($arr as $val)
								{
									if($val == $row["gender"])
									{
									echo "<option value='$val' selected>$val</option>";
									}
									else
									{
									echo "<option value='$val'>$val</option>";
									}
								}
								?>
								</select>
						  
                        </div>
                    </div>
                 <div class="form-group">
                          <label class="col-sm-2 control-label">Username</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="username" id="username"  placeholder="Username" required="required" value="<?php if(isset($_GET["editid"])){echo $row['username'];  } ?>" placeholder="Email" required="required" >
                          </div>
								<script type="text/javascript">
									var f1 = new LiveValidation('username');
									f1.add(Validate.Presence,{failureMessage: " Please enter username"});
								  
								 </script>
                    </div>

                    <div class="form-group">
                          <label class="col-sm-2 control-label">Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" name="password" id="password"  placeholder="Password"  value="<?php if(isset($_GET["editid"])){echo $row['password']; } ?>">
                          </div>
								<script type="text/javascript">
									var f1 = new LiveValidation('password');
									f1.add(Validate.Presence,{failureMessage: " Please enter password"});
								  
								 </script>
                    </div>
					 <div class="form-group">
                          <label class="col-sm-2 control-label">Re-Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" name="conpass" id="conpass" placeholder="Password" value="<?php if(isset($_GET["editid"])){echo $row['password']; } ?>" >
                          </div>
								<script type="text/javascript">
									var f1 = new LiveValidation('conpass');
									f1.add(Validate.Presence,{failureMessage: " Please enter Re-password"});
								  
								 </script>
                    </div>
				     <div class="form-group">
                        <label class="col-sm-2 control-label">User Type</label>
                          <div class="col-sm-10">
						<select name="userType" class="form-control">
								<?php
								$arr = array("Select Type","Administrator","User");
								foreach($arr as $val)
								{
									if($val == $row["userType"])
									{
									echo "<option value='$val' selected>$val</option>";
									}
									else
									{
									echo "<option value='$val'>$val</option>";
									}
								}
								?>
								</select>
						  
                        </div>
                    </div>				
					<div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
						<select name="status" class="form-control">
								<?php
								$arr = array("Select Status","Active","Deactive");
								foreach($arr as $val)
								{
									if($val == $row["status"])
									{
									echo "<option value='$val' selected>$val</option>";
									}
									else
									{
									echo "<option value='$val'>$val</option>";
									}
								}
								?>
								</select>
						  
                        </div>
                    </div>

                    <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
						  <?php if(isset($_GET['editid'])) { ?> <button type="submit" class="btn btn-primary" name="insert">Update Record</button><?php } else { ?> <button type="submit" class="btn btn-primary" name="insert">Add Record</button><?php } ?></td>
                           
                          </div>
                    </div>
                </form>
		</div>
    
<!----------------   Modal ends here  --------------->

    
                           <!----------------   Add Doctor Ends   --------------->
                </div>
           <!----------------   Panel body Ends   --------------->
         </div>
    </div>
	</div>
	


