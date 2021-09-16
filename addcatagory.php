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
			$sqlupd = "UPDATE category SET name='$_POST[Category]',description='$_POST[description]',status='$_POST[status]' WHERE id='$_GET[editid]'";
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
   
$usersSql=mysqli_query($con,"insert into category(name,description,status) values('$_POST[Category]','$_POST[description]','$_POST[status]')");
if(!$usersSql)
	{
		$msg ="<font color='red' size='4'>Failed to insert... Problem in sql query</font>".mysqli_error($con);
	}
	else
	{
		$msg = "<font color='green' size='4'><br>Record Added successfully...</font>";

	}
	
	
		}
}

$query=mysqli_query($con,"SELECT max(id) FROM category");
$rs=mysqli_fetch_array($query);
$GetID=$rs['max(id)']+1;

?>
<script type="text/javascript">
function validate() 
{
	 if(document.showroom.status.value== "Select Status")
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
$sqlst = "SELECT * FROM category WHERE id='$_GET[editid]'";
$sqquery = mysqli_query($con,$sqlst);
$row = mysqli_fetch_array($sqquery);
}
 
?>

        <div id="doctorlist" class="switchgroup">

		  <center> <?php echo $msg; ?> </center> <br>
		  <form class="form-horizontal" action="" method="post" name="showroom" onsubmit="return validate()">
			  
                    <div class="form-group">
                    <label  class="col-sm-2 control-label">Category Id:</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="doctid" placeholder="Doctor ID" value="<?php if(isset($_GET["editid"])){echo $row['id']; }else { echo $GetID; }?>" required="required" style="width:250px; height:35px;"  readonly >
                    </div>
                    </div>

                    <div class="form-group">
                                <label  class="col-sm-2 control-label">Category Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Category" name="Category" placeholder="Please Enter Category"  value="<?php if(isset($_GET["editid"])){echo $row['name']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
									var f1 = new LiveValidation('Category');
									f1.add(Validate.Presence,{failureMessage: " Please enter Category"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid Category"});
								 </script>
								 
					     </div>
							 <div class="form-group">
                                <label  class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
								
								 <textarea name="description" id="description" class="form-control" placeholder="Please type description" style="width:555px; height:100px;"  ><?php if(isset($_GET["editid"])){echo $row['description']; } ?></textarea>
								 <script type="text/javascript">
									var f1 = new LiveValidation('description');
									f1.add(Validate.Presence,{failureMessage: " Please enter description"});
								 </script>
                                </div>
								
								
                                </div>
							
						
					
				  				
					<div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
						<select name="status" class="form-control" style="width:250px; height:35px;" >
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
								
                                </div>
							 <div class="form-group">
                                <label  class="col-sm-2 control-label">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" >
                                </div>
							
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

    <!----------------   Add Doctor Start   --------------->
    <div id="adddoctor" class="switchgroup">
        <div class="panel panel-default">
            <div class="panel-body">
            

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