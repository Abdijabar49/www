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
                <div class="panel-heading">Add Products</div>
                <!----------------   Panel body Start   --------------->
                <div class="panel-body">
                       

    <!----------------   Display Doctor Data List Start  --------------->
    <?php
	include("dbconnection.php");
		$msg=null;
$res="";
if(isset($_POST["submit"]))
{
				//image1 
				if($_FILES["image1"]['size'] == 0)
				{
				$img1 = $_POST['oldimage1'];
				}
				else
				{
				$img1 = rand(). $_FILES["image1"]["name"];
				move_uploaded_file($_FILES["image1"]["tmp_name"],"upload/".$img1);
				}
				//image2 
				if($_FILES["image2"]['size'] == 0)
				{
				$img2 = $_POST['oldimage2'];
				}
				else
				{
				$img2 = rand(). $_FILES["image2"]["name"];
				move_uploaded_file($_FILES["image2"]["tmp_name"],"upload/".$img2);
				}
				//image3 
				if($_FILES["image3"]['size'] == 0)
				{
				$img3 = $_POST['oldimage3'];
				}
				else
				{
				$img3 = rand(). $_FILES["image3"]["name"];
				move_uploaded_file($_FILES["image3"]["tmp_name"],"upload/".$img3);
				}
				//image4
				if($_FILES["image4"]['size'] == 0)
				{
				$img4 = $_POST['oldimage4'];
				}
				else
				{
				$img4 = rand(). $_FILES["image4"]["name"];
				move_uploaded_file($_FILES["image4"]["tmp_name"],"upload/".$img4);
				}
				//image5 
				if($_FILES["image5"]['size'] == 0)
				{
				$img5 = $_POST['oldimage5'];
				}
				else
				{
				$img5 = rand(). $_FILES["image5"]["name"];
				move_uploaded_file($_FILES["image5"]["tmp_name"],"upload/".$img5);
				}
				
	if(isset($_GET["editid"]))
	{
		$result = mysqli_query($con,"UPDATE product SET name='$_POST[pro_name]',description='$_POST[description]',status='$_POST[status]',discount='$_POST[Discount]',categoryid='$_POST[Category]',qty='$_POST[Quantity]',homepage='$_POST[pagetitle]',shippingcharge='$_POST[ShippingCharge]',price='$_POST[Price]',weight='$_POST[Weight]',image1='$img1',image2='$img2',image3='$img3',image4='$img4',image5='$img5' where id='$_GET[editid]'");
		$ctins =  mysqli_affected_rows($con);
		if(!$result)
		{
			$msg = "<strong>Failed to update record</strong>".mysqli_error($con);;
		}
		else
		{
			$msg = "<strong>Record updated successfully...</strong>"; 
		}
	}
	else
	{
	$result=mysqli_query($con,"INSERT INTO product(name,description,discount,categoryid,qty,homepage,shippingcharge,price,weight,status,image1,image2,image3,image4,image5) values('$_POST[pro_name]','$_POST[description]','$_POST[Discount]','$_POST[Category]','$_POST[Quantity]','$_POST[pagetitle]','$_POST[ShippingCharge]','$_POST[Price]','$_POST[Weight]','$_POST[Weight]','$img1','$img2','$img3','$img4','$img5')");
	$ctins =  mysqli_affected_rows($con);
	if(!$result)
		{
			$msg = "<strong>failed to insert record</strong>".mysqli_error($con);
		}
		else
		{
			$msg = "<strong>Record inserted successfully...</strong>"; 
		}
	
	}
}
 



$query=mysqli_query($con,"SELECT max(id) FROM product");
$rs=mysqli_fetch_array($query);
$GetID=$rs['max(id)']+1;

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
$sqlst = "SELECT * FROM product WHERE id='$_GET[editid]'";
$sqquery = mysqli_query($con,$sqlst);
$row = mysqli_fetch_array($sqquery);
}
 
?>


        <div id="doctorlist" class="switchgroup">
		


		  <center> <?php echo $msg; ?> </center> <br>
			<form  class="form-horizontal" name="showroom" method="post" action="" enctype="multipart/form-data" onsubmit="return validate()">
			  
                    <div class="form-group">
                    <label  class="col-sm-2 control-label">Product Id:</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="doctid" placeholder="Doctor ID" value="<?php if(isset($_GET["editid"])){echo $row['id']; }else { echo $GetID; }?>" required="required" style="width:250px; height:35px;"  readonly >
                    </div>
                    </div>

                    <div class="form-group">
                                <label  class="col-sm-2 control-label">Product Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="pro_name" name="pro_name" placeholder="Please Enter Product Name"  value="<?php if(isset($_GET["editid"])){echo $row['name']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
									var f1 = new LiveValidation('pro_name');
									f1.add(Validate.Presence,{failureMessage: " Please enter Product Name"});
								
								 </script>
								 
					     </div>
							 <div class="form-group">
                                <label  class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
								
								 <textarea name="description" id="description" class="form-control" placeholder="Please type description" style="width:350px; height:100px;"  ><?php if(isset($_GET["editid"])){echo $row['description']; } ?></textarea>
								 <script type="text/javascript">
									var f1 = new LiveValidation('description');
									f1.add(Validate.Presence,{failureMessage: " Please enter description"});
								 </script>
                                </div>
								
								
                                </div>
							
						<div class="form-group">
                                <label  class="col-sm-2 control-label">Page Title Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="pagetitle1" name="pagetitle" placeholder="Please Enter page title"  value="<?php if(isset($_GET["editid"])){echo $row['name']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
									var f1 = new LiveValidation('pagetitle');
									f1.add(Validate.Presence,{failureMessage: " Please enter page title Name"});
								  
								 </script>
								 
					     </div>
						 <div class="form-group">
                                <label  class="col-sm-2 control-label">Category </label>
                                <div class="col-sm-10">
								 <select name="Category" class="form-control" style="width:250px; height:35px;" >
										<option value="Select">Select Category </option>
										<?php
										$sqlselcourse = "select * from category";
										$sqlquerycourse = mysqli_query($con,$sqlselcourse);
										while($recs = mysqli_fetch_array($sqlquerycourse))
										{
											if($recs["id"] == $row["categoryid"])
											{
											echo "<option value='$recs[id]' selected>$recs[name]</option>";
											}
											else
											{
											echo "<option value='$recs[id]'>$recs[name]</option>";
											}
										}
										?>
										</select>
                               
								
							</div>	 
					     </div>
						 <div class="form-group">
                                <label  class="col-sm-2 control-label">Product Price</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Price" name="Price" placeholder="Please Enter Price"  value="<?php if(isset($_GET["editid"])){echo $row['price']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
								var f1 = new LiveValidation('Price');
							   f1.add(Validate.Presence,{failureMessage: " Please enter Price"});
							   f1.add(Validate.Format,{pattern: /^[0-9+.]+$/ ,failureMessage: " It allows only numbers"});
							  
								</script>
								 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Quantity</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Quantity" name="Quantity" placeholder="Please Enter Quantity"  value="<?php if(isset($_GET["editid"])){echo $row['qty']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								  <script type="text/javascript">
								var f1 = new LiveValidation('Quantity');
							   f1.add(Validate.Presence,{failureMessage: " Please enter Quantity"});
							   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: " It allows only numbers"});
							  
								</script>
								 
					     </div>
						 <div class="form-group">
                                <label  class="col-sm-2 control-label">Shipping Charge</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="ShippingCharge" name="ShippingCharge" placeholder="Please Enter Shipping Charge"  value="<?php if(isset($_GET["editid"])){echo $row['shippingcharge']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
								var f1 = new LiveValidation('ShippingCharge');
							   f1.add(Validate.Presence,{failureMessage: " Please enter Shipping Charge"});
							   f1.add(Validate.Format,{pattern: /^[0-9+.]+$/ ,failureMessage: " It allows only numbers"});
							  
								</script>
								 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Discount</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Discount" name="Discount" placeholder="Please Enter Discount"  value="<?php if(isset($_GET["editid"])){echo $row['discount']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								  <script type="text/javascript">
								var f1 = new LiveValidation('Discount');
							   f1.add(Validate.Presence,{failureMessage: " Please enter Discount"});
							   f1.add(Validate.Format,{pattern: /^[0-9+.]+$/ ,failureMessage: " It allows only numbers"});
							  
								</script>
								 
					     </div>
						 <div class="form-group">
                                <label  class="col-sm-2 control-label">Weight</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Weight" name="Weight" placeholder="Please Enter Weight"  value="<?php if(isset($_GET["editid"])){echo $row['weight']; } ?>" style="width:250px; height:35px;" >
                               
								</div>
								 <script type="text/javascript">
								var f1 = new LiveValidation('Weight');
							   f1.add(Validate.Presence,{failureMessage: " Please enter Weight"});
							   f1.add(Validate.Format,{pattern: /^[0-9+.]+$/ ,failureMessage: " It allows only numbers"});
							  
								</script>
								 
					     </div>
						
						   <div class="form-group">
                                <label  class="col-sm-2 control-label">Main Image</label>
                                <div class="col-sm-10">
									   <input name="image1" type="file" size="20" value="<?php echo $row["image1"]; ?>" style="width:250px; height:35px;">
										<input type="hidden" name="oldimage1" value="<?php echo $row["image1"]; ?>" class="form-control"/>
											<?php   
											if(isset($_GET["editid"]))     
											{
												if($row["image1"] == "")
												{
												echo "<img src='images/noimage.jpg' width='125' height='100'>";		
												}
												else
												{
												echo "<img src='upload/$row[image1]' width='125' height='100'>";
												}
											}
											?>
									
									
								</div>
						 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Select Image1</label>
                                <div class="col-sm-10">
									   <input name="image2" type="file" size="20" value="<?php echo $row["image2"]; ?>" style="width:250px; height:35px;">
										<input type="hidden" name="oldimage2" value="<?php echo $row["image2"]; ?>" class="form-control"/>
											<?php   
											if(isset($_GET["editid"]))     
											{
												if($row["image2"] == "")
												{
												echo "<img src='images/noimage.jpg' width='125' height='100'>";		
												}
												else
												{
												echo "<img src='upload/$row[image2]' width='125' height='100'>";
												}
											}
											?>
									Must ( width:1080 * height:1440)
									
								</div>
						 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Select Image2</label>
                                <div class="col-sm-10">
									   <input name="image3" type="file" size="20" value="<?php echo $row["image3"]; ?>" style="width:250px; height:35px;">
										<input type="hidden" name="oldimage3" value="<?php echo $row["image3"]; ?>" class="form-control"/>
											<?php   
											if(isset($_GET["editid"]))     
											{
												if($row["image3"] == "")
												{
												echo "<img src='images/noimage.jpg' width='125' height='100'>";		
												}
												else
												{
												echo "<img src='upload/$row[image3]' width='125' height='100'>";
												}
											}
											?>
									
									
								</div>
						 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Select Image3</label>
                                <div class="col-sm-10">
									   <input name="image4" type="file" size="20" value="<?php echo $row["image4"]; ?>" style="width:250px; height:35px;">
										<input type="hidden" name="oldimage4" value="<?php echo $row["image4"]; ?>" class="form-control"/>
											<?php   
											if(isset($_GET["editid"]))     
											{
												if($row["image4"] == "")
												{
												echo "<img src='images/noimage.jpg' width='125' height='100'>";		
												}
												else
												{
												echo "<img src='upload/$row[image4]' width='125' height='100'>";
												}
											}
											?>
									
									
								</div>
						 
					     </div>
						  <div class="form-group">
                                <label  class="col-sm-2 control-label">Select Image4</label>
                                <div class="col-sm-10">
									   <input name="image5" type="file" size="20" value="<?php echo $row["image5"]; ?>" style="width:250px; height:35px;">
										<input type="hidden" name="oldimage5" value="<?php echo $row["image5"]; ?>" class="form-control"/>
											<?php   
											if(isset($_GET["editid"]))     
											{
												if($row["image5"] == "")
												{
												echo "<img src='images/noimage.jpg' width='125' height='100'>";		
												}
												else
												{
												echo "<img src='upload/$row[image5]' width='125' height='100'>";
												}
											}
											?>
									
									
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
						  <?php if(isset($_GET['editid'])) { ?> <button type="submit" class="btn btn-primary" name="submit">Update Record</button><?php } else { ?> <button type="submit" class="btn btn-primary" name="submit">Add Record</button><?php } ?></td>
                           
                          </div>
                    </div>
                </form>
		
		</div>

                            
           
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