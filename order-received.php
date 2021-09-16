<?php
session_start();
include("header.php");
?>
	<!-- Blog -->
	<br><br>
	<div class="blog">
		<div class="container">
		<!-- row -->
				<div class="row">
							<?php
							$ctins=null;
					
						
						$dt = date("Y-m-d h:i:s");
								if(isset($_POST["submit"]))
							{ 
												
						$insertQuery ="insert into shippingdetails(firstname,lastname,email,address,country,city,mobile,payment_type,total,created,status,comments) values('$_POST[firstname]','$_POST[lastname]','$_POST[email]','$_POST[address]','$_POST[country]','$_POST[city]','$_POST[tel]','$_POST[payment]','$_POST[totalprice]','$dt','Pending','')";
							if(!mysqli_query($con,$insertQuery))
								
									 {
									  die('Error: ' . mysqli_error($con));
									 }
									else
									 {
										$msg = "Not Registered";
									}
									$shippingid=mysqli_insert_id($con);
								
							$ctins =  mysqli_affected_rows($con);
							
							if($_POST['productid']!= "")
						{
			
							for($i=0;$i<count($_POST['productid']); $i++)
							{				
								$productid =$_POST['productid'][$i];
								$quantity =$_POST['quantity'][$i];
								$price =$_POST['price'][$i];
								
						$result = mysqli_query($con,"INSERT INTO  product_order(shippingid,productid,qty,amount,orderdate) values('$shippingid','$productid','$quantity','$price','$dt')");
							}
						}
								
								unset($_SESSION["products"]);
																	
											
								
							}
						
						?>
			<div class="col-md-7 ">
						<!-- Billing Details -->
						<div class="billing-details">
	

							<div class="section-title">
						<center><font color='green' size="4"><b>Thank you. Your order has been received.</b></font></center><br><br>

								<h3 class="title">Customer Informaiton</h3>
							</div>
							<div class="order-summary">
							
						
							<div class="order-products">
							<?php			
							
							
						
						

									$result2= mysqli_query($con,"select * from shippingdetails where id='$shippingid'");
									$row2= mysqli_fetch_array($result2);
									
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
							
								?>
								
								<div class="order-col">
								<div><strong>Name: </strong><?php echo $firstname; ?> <?php echo $lastname; ?></div>
								<div><strong></strong> </div>
								</div>
								<div class="order-col">
								<div><strong>Phone: </strong> <?php echo $mobile; ?></div>
								<div><strong></strong> </div>
								</div>
									<div class="order-col">
								<div><strong>Email: </strong> <?php echo $email; ?></div>
								<div><strong></strong> </div>
								</div>
								<div class="order-col">
								<div><strong>Address: </strong> <?php echo $address; ?></div>
								<div><strong></strong> </div>
								</div>
									<div class="order-col">
								<div><strong>City: </strong> <?php echo $city; ?></div>
								<div><strong></strong> </div>
								</div>
								<div class="order-col">
								<div><strong>Country: </strong> <?php echo $country; ?></div>
								<div><strong></strong> </div>
								</div>
								
								
							
									
							</div>
							
							
						</div>
							
							
						</div>
						
						<!-- /Order notes -->
					</div>

					<!-- Order Details -->
					
					<div class="col-md-5 ">
					<br><br><br>
						<div class="section-title text-center">
							<h3 class="title">Order Details</h3>
							
						</div>
						<div class="order-summary order-details">
							<div class="order-col">
								<div><strong>PRODUCT</strong></div>
								<div><strong>TOTAL</strong></div>
							</div>
						
							<div class="order-products">
							<?php			
							$cart_box = '';
							//foreach($_SESSION['products'] as $data)
							//echo $data["product_code"];
							
						
											
						
					$result= mysqli_query($con,"select * from product_order where shippingid='$shippingid '");			
						
								
							while($row= mysqli_fetch_array($result))
		         { 
								
								$qty = $row["qty"];
								$amount = $row["amount"];
								$productid = $row["productid"];
								
								
								$result4= mysqli_query($con,"select * from product where id='$productid'");
								$row4= mysqli_fetch_array($result4);
								

									
							
								?>
								<div class="order-col">
									<div><?php echo $qty ; ?> x <?php echo $row4["name"]; ?></div>
									<div>$<?php echo $amount; ?></div>
									
								</div>
								
									<?php		
							
							
							
						}
						
							
						
						
						?>
							</div>
							<div class="order-col">
								<div><strong>Shiping </strong> </div>
								<div>FREE</div>
							</div>
							<div class="order-col">
								<div><strong>Payment Method</strong></div>
								<div><?php echo $payment_type; ?> </div>
							</div>
							<div class="order-col">
								<div><strong>TOTAL</strong></div>
								<div><strong class="order-total">$<?php echo $total; ?></strong></div>
							</div>
							
						</div>
						
				
					</div>
					
					
					<!-- /Order Details -->
				</div>
				<!-- /row -->
		</div>
	</div>

<?php
	include("footer.php");
	?>

	