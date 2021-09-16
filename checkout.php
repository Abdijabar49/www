<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");

?>


	<!-- Blog -->
	<br><br>
	<div class="blog">
		<div class="container">
		<!-- row -->
				<div class="row">
							<?php
							$ctins=null;
							
					if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){
						$total = 0;
						
						
						$dt = date("Y-m-d h:i:s");
								if(isset($_POST["submit"]))
							{ 
								unset($_SESSION["products"]);				
						$insertQuery ="insert into shippingdetails(firstname,lastname,email,address,country,city,mobile,payment_type,total,created,status) values('$_POST[firstname]','$_POST[lastname]','$_POST[email]','$_POST[address]','$_POST[country]','$_POST[city]','$_POST[tel]','$_POST[payment]','$_POST[total]','$dt','Pending')";
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
								
								
																	
											
								
							}
						
						?>
						
					<div class="col-md-7">
						<!-- Billing Details -->
						<div class="billing-details">
						  <?php
						if($ctins == 1)
						{
						
							echo "<center><font color='green'><b>Thank your. Your order has been received.</b></font></center><br><br>";
						?>
					
						<?php
						}
						else
						{
							?>
							<div class="section-title">
								<h3 class="title">Billing and Shipping Address</h3>
							</div>
							<form action="order-received.php" method="post" name="form1" onsubmit="return ValidateTest();">
							<div class="form-group">
								<input class="input" type="text" id="firstname" name="firstname" placeholder="First Name">
								 <script type="text/javascript">
									var f1 = new LiveValidation('firstname');
									f1.add(Validate.Presence,{failureMessage: " Please enter Firstname"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid Firstname"});
								 </script>
							</div>
							<div class="form-group">
								<input class="input" type="text" id="lastname" name="lastname" placeholder="Last Name">
								 <script type="text/javascript">
									var f1 = new LiveValidation('lastname');
									f1.add(Validate.Presence,{failureMessage: " Please enter lastname"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid lastname"});
								 </script>
							</div>
							<div class="form-group">
								<input class="input" type="text" id="email" name="email" placeholder="Email ">
								 <script type="text/javascript">
								var f1 = new LiveValidation('email');
								f1.add(Validate.Presence,{failureMessage: " Please enter Email"});
								f1.add( Validate.Email );
								</script>
							</div>
							<div class="form-group">
								<input class="input" type="text" id="address"  name="address" placeholder="Address">
								 <script type="text/javascript">
									var f1 = new LiveValidation('address');
									f1.add(Validate.Presence,{failureMessage: " Please enter address"});
								  
								 </script>
							</div>
							<div class="form-group">
								<input class="input" type="text" id="country" name="country" placeholder="Country">
								 <script type="text/javascript">
									var f1 = new LiveValidation('country');
									f1.add(Validate.Presence,{failureMessage: " Please enter country"});
								   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:" It allows only characters"});
									f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
										   " Invalid country"});
								 </script>
							</div>
								
							
							<div class="form-group">
								<input class="input" type="text" id="city" name="city"  placeholder="City">
								<script type="text/javascript">
									var f1 = new LiveValidation('city');
									f1.add(Validate.Presence,{failureMessage: " Please enter city"});
								   
								 </script>
							</div>
						
							<div class="form-group">
								<input class="input" type="text" id="tel" name="tel" placeholder="Telephone">
								  <script type="text/javascript">
								var f1 = new LiveValidation('tel');
							   f1.add(Validate.Presence,{failureMessage: " Please enter contact"});
							   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: " It allows only numbers"});
							   f1.add( Validate.Length, { maximum: 10 } );
						  </script>
							</div>
							
						</div>
						
						<!-- /Order notes -->
					</div>

					<!-- Order Details -->
					<div class="col-md-5 order-details">
						<div class="section-title text-center">
							<h3 class="title">Your Order</h3>
						</div>
						<div class="order-summary">
							<div class="order-col">
								<div><strong>PRODUCT</strong></div>
								<div><strong>TOTAL</strong></div>
							</div>
						
							<div class="order-products">
							<?php			
							$cart_box = '';
							//foreach($_SESSION['products'] as $data)
							//echo $data["product_code"];
							
						
											
						
								
							foreach($_SESSION["products"] as $product){
								
								
								$product_id = $product["product_code"];
								$product_name = $product["product_name"];
								$product_qty = $product["product_qty"];
								$product_price = $product["product_price"];
								$product_code = $product["product_code"];
								$product_color = $product["product_color"];			
								$item_price = sprintf("%01.2f",($product_price * $product_qty)); 

									
							
								?>
								<div class="order-col">
									<div><?php echo $product_qty; ?> x <?php echo $product_name; ?></div>
									<div>$<?php echo sprintf("%01.2f", ($product_price * $product_qty)); ?></div>
									
								</div>
								<input type="hidden" name="productid[]"  value="<?php echo $product_id; ?>">
								<input type="hidden" name="quantity[]"  value="<?php echo $product_qty; ?>">
								<input type="hidden" name="price[]"  value="<?php echo sprintf("%01.2f", ($product_price * $product_qty)); ?>">
									<?php		
							$subtotal = ($product_price * $product_qty);
							$total = ($total + $subtotal);
							
							
						}
						
							
						
						
						?>
							</div>
							<input type="hidden" name="totalprice"  value="<?php echo $total; ?>">
							<div class="order-col">
								<div>Shiping</div>
								<div><strong>FREE </strong> </div>
							</div>
							<div class="order-col">
								<div><strong>TOTAL</strong></div>
								<div><strong class="order-total">$<?php echo $total; ?></strong></div>
								
							</div>
							
						</div>
						<div class="section-title">
								<h3 class="title">Payment Method</h3>
							</div>
						<div class="payment-method">
							<div class="input-radio">
								<input type="radio" name="payment" id="payment-2" value="Cash On Delivery" checked>
								<label for="payment-2">
									<span></span>
									Cash on Delivery 
								</label>
								
							</div>
							<div class="input-radio">
								<input type="radio" name="payment" id="payment-1" value="Mobile Payments">
								<label for="payment-1">
									<span></span>
									 Mobile Payments 
								</label>
								<div class="caption">
									<p>Fadlan ku dir lacagta numbaradaan hoos ku qoran: </p>
									<ul>
									<li><input type="radio" id="terms"> <b>EVC+:</b> <a href="#"> *712*615530849*<?php echo $total; ?>#</a></li>
									<li><input type="radio" id="terms"> <b>eDahab:</b> <a href="#"> *110*615530849*<?php echo $total; ?>#</a></li>
									</ul>
									
								</div>
							</div>
							
							
						</div>
						
						<input type="submit" name="submit" class="primary-btn order-submit" value="Place Order">
					</form>
					</div>
					<?php
					  }
					  ?>
						<?php	
					} else {
						echo "<h3>Your Cart is empty </h3>";
					}
					?>
					<!-- /Order Details -->
				</div>
				<!-- /row -->
		</div>
	</div>

<?php
	include("footer.php");
	?>

	