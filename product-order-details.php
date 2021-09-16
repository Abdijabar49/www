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
                <div class="panel-heading">Manage Orders</div>
                <!----------------   Panel body Start   --------------->
                <div class="panel-body">
                 
				    
						   
						   <?php


 
 
										 $msg=null;
				   
$i=1;
	

$query= mysqli_query($con,"select * from shippingdetails where id='$_GET[prodid]'");
									$row2= mysqli_fetch_array($query);
									
									$firstname = $row2["firstname"];
								$lastname = $row2["lastname"];
								$mobile = $row2["mobile"];
								$email = $row2["email"];
								$city = $row2["city"];
								$country = $row2["country"];
								$payment_type = $row2["payment_type"];
								$total = $row2["total"];
								$address = $row2["address"];
								$shippingid = $row2["id"];
								$comments = $row2["comments"];
								$status = $row2["status"];
								
?>
		
		
				<div class="section-title">

								<h3 class="title">Customer Informaiton</h3>
							</div>					
		<table>
               
				<tr>
				<td height="40" width="100"><strong>Name:</strong> </td><td> <?php echo $firstname; ?> <?php echo $lastname; ?></td>
				</tr>
				<tr>
				<td height="40"><strong>Phone:</strong> </td><td> <?php echo $mobile; ?></td>
				</tr>
				<tr>
				<td height="40"><strong>Email:</strong> </td><td> <?php echo $email; ?></td>
				</tr>
				<tr>
				<td height="40"><strong>Address:</strong> </td><td> <?php echo $address; ?></td>
				</tr>
				<tr>
				<td height="40" ><strong>City:</strong> </td><td> <?php echo $city; ?></td>
				</tr>
				<tr>
				<td height="40"><strong>Country:</strong> </td><td> <?php echo $country; ?></td>
				</tr>
				<tr>
				
				</tr>
              
        </table>
		 <hr>
		 <div class="section-title">

								<h3 class="title">Order Details</h3>
							</div>
        <table class="table table-bordered table-hover">
                <tr class="active">
                       
                        <td>Image</td>
						<td>Product</td>
                        <td>Unit Price</td>
						<td>Quantity</td>
						 <td>Total Amount</td>
                        
                </tr>
                
                     <?php
					 $msg=null;
				   
$result= mysqli_query($con,"select * from product_order where shippingid='$shippingid '");			
						
								
							while($row= mysqli_fetch_array($result))
		         { 
								
								$qty = $row["qty"];
								$amount = $row["amount"];
								$productid = $row["productid"];
								
								
								$sqlquery= mysqli_query($con,"select * from product where id='$productid'");
								$row2= mysqli_fetch_array($sqlquery);
								
						
								
					$productname= $row2['name'];	
					$unitprice= $row2['price'];
					$image= $row2['image1'];

    echo '<tr>';
	?>
	<td width="80"><img src="upload/<?php echo $image ?>" width="75" height="75" class="img1"/></td> 
   <?php echo '<td>'.$productname.'</td>'; 
   echo '<td>'.$unitprice.'</td>';
   echo '<td>'.$row['qty'].'</td>';
	
	echo '<td>'.$row['amount'].'</td>';
}

$sqlquery= mysqli_query($con,"select * from product where id='20'");
								$row2= mysqli_fetch_array($sqlquery);
							
								
?>
	
	</tr>
	<tr>
	<td colspan="3"></td>
	<td><strong>Total </strong></td>
	<td><strong> $<?php echo $total; ?></strong></td>
	</tr>


          
        </table>
		 <div id="doctorlist" class="switchgroup">

		  
		
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

		  <form class="form-horizontal" action="product-order.php" method="post" name="showroom" onsubmit="return validate()">
			  
                     				
					<div class="form-group">
					<?php echo $msg; ?>
                        <label class="col-sm-2 control-label">Update Status</label>
                            <div class="col-sm-10">
						<input type="hidden" name="shippingid"  value="<?php echo $shippingid; ?>">
						<select name="status" class="form-control" style="width:250px; height:35px;" >
								<?php
								$arr = array("Select Status","Ready To ship","Shipping Done","Order Completed");
								foreach($arr as $val)
								{
									if($val == $status)
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
                                <label  class="col-sm-2 control-label">Comments</label>
                                <div class="col-sm-10">
								
								 <textarea name="comments" id="comments" class="form-control" placeholder="Please type comments" style="width:350px; height:100px;"  ><?php echo $comments  ?></textarea>
								 <script type="text/javascript">
									var f1 = new LiveValidation('comments');
									f1.add(Validate.Presence,{failureMessage: " Please enter comments"});
								 </script>
                                </div>
								
								
                                </div>
							
						
					
				 

                    <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
						  <?php if(isset($_GET['editid'])) { ?> <button type="submit" class="btn btn-primary" name="insert">Update Record</button><?php } else { ?> <button type="submit" class="btn btn-primary" name="insert">Update Record</button><?php } ?></td>
                           
                          </div>
                    </div>
                </form>
		</div>
      <div class="modal-footer">
                                   
                                    <a href="product-order.php"><input type="submit" class="btn btn-primary" value="Back"></button></a>
                                 </div>
		</div>
    <!----------------   Display Doctor Data List Ends  --------------->
   
    
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