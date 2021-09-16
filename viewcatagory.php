<?php
include("adminheader.php");
if(isset($_GET["delid"]))
{
$sqldel = "DELETE FROM category where id='$_GET[delid]'";
$resdel = mysqli_query($con,$sqldel);
	if(!$resdel)
	{
		$msg= "<font color='red'>Failed to delete... Problem in sql query </font>";
	}
	else
	{
		$msg = "<font color='green'>Record deleted successfully..</font>";
	}
}
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
                <div class="panel-heading">List Users</div>
                <!----------------   Panel body Start   --------------->
                <div class="panel-body">
                        <ul class="nav nav-tabs doctor">
								<form action="" name="searchfrm" method="post"
								  enctype="multipart/form-data" >
								   <strong><p style= 'float:right; margin-right:1px; margin-top:6px; 
									font-size:15px;  background-color:lightgoldenrodyellow;'> 
								  <input type="text" name="search_value" style="width:200;height:39px;border-radius:10px;" required>
								  <input class='searching-tab' style="height:39px;width:117px;font-weight:bold; "
								  type="submit" name="search" value="Search"> 
								  </p></strong>
								  </form>
                        </ul>

    <!----------------   Display Doctor Data List Start  --------------->
    
        <div id="doctorlist" class="switchgroup">
		<?php 
										 $msg=null;
				   
$i=1;
	if(isset($_POST['search']))
    {
        $search = $_POST['search_value'];
        $query_search = "SELECT * FROM category WHERE name LIKE '$search%'
		OR description LIKE '$search%' OR status LIKE '$search%'";
        $st = mysqli_query($con,$query_search);
		}
		else
		{
$st=mysqli_query($con,"select * from category order by id desc");
		}			   
if (mysqli_num_rows($st)>=1){
?>
		 <div class="clear" style="overflow:scroll;height:600px;">
        <table class="table table-bordered table-hover">
                <tr class="active">
                        <td>#</td>
                        <td>Catagory Name</td>
						<td>Descripton</td>
						 <td>Status</td>
                        <td>Options</td>
                </tr>
                
                     <?php
					 $msg=null;
				   
$i=1;
				   


while($row=mysqli_fetch_array($st)){

    echo '<tr>';
	echo '<td>'.$i++.'</td>';  
	echo '<td>'.$row['name'].'</td>'; 
    echo '<td>'.$row['description'].'</td>'; 
	echo '<td>'.$row['status'].'</td>';
	
?>
	<td><a class="btn btn-primary" href='addcatagory.php?editid=<?php echo $row ['id'];?>'>
						   <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></a> 
                            <a  href='viewcatagory.php?delid=<?php echo $row ['id'];?>'onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                           
						   </td>
	
	</tr>
	

<?php
}
}
else{
						 echo'<br><br><b><font color="red" size="4">No Result found</font><b/><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';

						}
?>
          
        </table>
        </div>
		</div>
    <!----------------   Display Doctor Data List Ends  --------------->
   
    <!------ Doctor Edit Info Modal Start Here ---------->
                            
           
              <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                       
                    
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Doctor Information</h4>
                        </div>
                        
                        <div class="modal-body">
                        <div class="panel panel-default">
                            <div class="panel-body">
                             <form class="form-horizontal" action="editDoct.jsp" method="post">
                                <div class="form-group">
                                <label  class="col-sm-2 control-label">Doctor Id:</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="doctid" placeholder="Doctor ID" value="" readonly="readonly">
                                </div>
                                </div>

                                <div class="form-group">
                                <label  class="col-sm-2 control-label">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" >
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
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" >
                                </div>
								<script type="text/javascript">
				    var f1 = new LiveValidation('lname');
				    f1.add(Validate.Presence,{failureMessage: " Please enter Firstname"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Firstname"});
				 </script>
                                </div>
                                <div class="form-group">
                                      <label class="col-sm-2 control-label">Email</label>
                                      <div class="col-sm-10">
                                          <input type="email" class="form-control" name="email" placeholder="Email" >
                                      </div>
                                </div>

                    <div class="form-group">
                          <label class="col-sm-2 control-label">Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" name="pwd" placeholder="Password" >
                          </div>
                    </div>

                    <div class="form-group">
                        <label  class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="add" placeholder="Address" >
                        </div>
                    </div>

                     <div class="form-group">
                        <label  class="col-sm-2 control-label">Phone</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="phon" placeholder="Phone No." >
                        </div>
                    </div>
              
             
                    <div class="form-group">
                        <label  class="col-sm-2 control-label">Department</label>
                        <div class="col-sm-10">

                        <select class="form-control" name="dept">
                        <option selected="selected">Depatrtment</option>
                        
                        <option> Neurology</option>
                   
                              
                        </select>
                        </div>
                    </div>
									
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Update"></button>
                                 </div>
                            </form>
                             </div>
                         </div>
                         </div>
                    </div>
                 </div>
               </div>
<!----------------   Modal ends here  --------------->
<?php 
$dt= date("Y-m-d"); 
if(isset($_POST["insert"]))
{ 

 $sql1 = "select username from users where username = '$_POST[username]'";
    $result1 = mysqli_query($con,$sql1) or die ("Couldn't execute query.");										  
    
	$num1=mysqli_num_rows($result1);
	if($num1 == 1)
	{
		?>
	 <script>
	 alert("Sorry ! username allready exists , Please Try another one");
	 </script>
<?php
		
		$msg = "<font color='red'> Sorry ! username allready exists , Please Try another one  </font><font color='darkBlue'>".$_POST["username"]."</font>";
	}
	else
	{   
$usersSql=mysqli_query($con,"insert into users(firstName,lastName,contact,email,jobTitle,email,gender,username,password,userType,status,registerDate) values('$_POST[fname]','$_POST[lname]','$_POST[contact]','$_POST[jobTitle]','$_POST[gender]','$_POST[username]','$_POST[password]','$_POST[userType]','$_POST[status]','$dt')");
if(!$usersSql)
	{
		$msg ="<font color='red'>Failed to insert... Problem in sql query<</font>";
	}
	else
	{
		?>
	 <script>
	 alert("Record Inserted Successfully");
	 window.location="managerusers.php";
	 </script>
<?php

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
 else if(document.showroom.status.value== "Select")
	{
		alert("Please select status");
		document.showroom.doctid.focus();
		return false;
	}
else if(document.showroom.sex.value== "Select")
	{
		alert("Please select gender");
		document.showroom.doctid.focus();
		return false;
	}
}
</script>
    <!----------------   Add Doctor Start   --------------->
    <div id="adddoctor" class="switchgroup">
        <div class="panel panel-default">
            <div class="panel-body">
               <form class="form-horizontal" action="" method="post" name="showroom" onsubmit="return validate()">
			   <?php echo $msg; ?>
                    <div class="form-group">
                    <label  class="col-sm-2 control-label">User Id:</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="doctid" placeholder="Doctor ID" value="<?php  echo $GetID;?>" required="required" readonly >
                    </div>
                    </div>

                    <div class="form-group">
                                <label  class="col-sm-2 control-label">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required="required">
                                </div>
					     </div>
							 <div class="form-group">
                                <label  class="col-sm-2 control-label">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required="required">
                                </div>
						
                                </div>
								 <div class="form-group">
                          <label class="col-sm-2 control-label">Contact</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="contact" placeholder="Contact" required="required">
                          </div>
                    </div>
						 <div class="form-group">
                          <label class="col-sm-2 control-label">Job Title</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="jobtitle" placeholder="Job Title" required="required">
                          </div>
                    </div>
                    <div class="form-group">
                          <label class="col-sm-2 control-label">Email</label>
                          <div class="col-sm-10">
                              <input type="email" class="form-control" name="email" placeholder="Email" required="required">
                          </div>
                    </div>
					<div class="form-group">    
                                                                <label class="col-sm-2 control-label">Gender</label>
                                                                <div class="col-sm-2">
                                                            <select class="form-control" name="gender">
																 <option value="Select">Select gender</option>
                                                                  <option value="Active">Male</option>
                                                                  <option Value="Deactive">Female</option>
                                                                  </select>
                                                                    </div>
                                                            </div>
                 <div class="form-group">
                          <label class="col-sm-2 control-label">Username</label>
                          <div class="col-sm-10">
                              <input type="text" class="form-control" name="username" placeholder="Username" required="required">
                          </div>
                    </div>

                    <div class="form-group">
                          <label class="col-sm-2 control-label">Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                          </div>
                    </div>
					 <div class="form-group">
                          <label class="col-sm-2 control-label">Re-Password</label>
                          <div class="col-sm-10">
                            <input type="password" class="form-control" name="conpass" placeholder="Password" value="<?php if(isset($_GET["editid"])){echo $row['password']; } ?>" required="required">
                          </div>
                    </div>
					<div class="form-group">    
                                                                <label class="col-sm-2 control-label">User Type</label>
                                                                <div class="col-sm-2">
                                                            <select class="form-control" name="userType">
																 <option value="Select">Select User Type</option>
                                                                  <option value="Active">Administrator</option>
                                                                  <option Value="Deactive">User</option>
                                                                  </select>
                                                                    </div>
                                                            </div>
					
             
															<div class="form-group">    
                                                                <label class="col-sm-2 control-label">Status</label>
                                                                <div class="col-sm-2">
                                                            <select class="form-control" name="status">
																 <option value="Select">Select Status</option>
                                                                  <option value="Active">Active</option>
                                                                  <option Value="Deactive">Deactive</option>
                                                                  </select>
                                                                    </div>
                                                            </div>

                    <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" name="insert">Add Record</button>
                          </div>
                    </div>
                </form>

               </div>
        </div>
    </div>
                           <!----------------   Add Doctor Ends   --------------->
                </div>
           <!----------------   Panel body Ends   --------------->
         </div>
    </div>
	</div>
</div>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>