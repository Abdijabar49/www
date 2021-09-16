<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");
$dt = date("Y-m-d h:i:s");
?>
	

	<!-- Shop -->
	
	<div class="shop">
		<div class="container">
		
			<div class="row">
				<?php
				
				include("sidebar.php");
				
				?>

				<div class="col-lg-9">
					
					<!-- Shop Content -->

					<div class="shop_content">
					
						
							<div class="row">
					
							<!-- product -->
							<?php
						
							
		
			
				// qaybta display products //				
			
	
			$cid=intval($_GET['cid']);
			
			$ret=mysqli_query($con,"select * from product where categoryid='$cid' AND status='Active'");
			$num=mysqli_num_rows($ret);
			if($num>0)
			{
			
						while ($data = mysqli_fetch_assoc($ret)) {
								$pro_id = $data['id'];
								$pro_name = $data['name'];
								$pro_price = $data['price'];
								$pro_image = $data['image1'];
								
								

							
								?>
							<!-- product -->
							<div class="col-md-4 col-xs-6">
							<form action="" method="Post" class="product-form" >
							
								<div class="product">
								
								
									<div class="product-img">
									<?php
									if($pro_image == 0)
										{
										?>
										<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										echo "<img src='img/noimage.jpg'  width='100%' height='180' alt='' />";
										}
										else
										{
										?>
										<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										echo "<img src='admin/upload/$pro_image' alt='img'  width='100%' height='180' >";
										
										
										}
										?>
									
										</a>
										
									</div>
									</a>
									<div class="product-body">
										
										<h3 class="product-name"><a href="product-detail.php?id=<?php echo $pro_id; ?>" ><?php echo $pro_name; ?></a></h3>
										<h4 class="product-price">$<?php echo $pro_price; ?> <del class="product-old-price"></del></h4>
										
										
									</div>
									<div class="add-to-cart">
										
									<input type="hidden" name="pro_name" style="max-width:50px;" value="<?php echo $pro_name; ?>" >
									<input type="hidden" name="product_color" style="max-width:50px;" value="<?php echo $pro_image; ?>" >	
									<input name="product_code" type="hidden" value="<?php echo $data['id']; ?>">
									 <input type="hidden" name="product_qty"  style="max-width:50px;" value="1" >
									
									
									
										
										<button type="submit"  name="submit"  class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> add to cart</button>
									
									</form>	
									</div>
								</div>
							</div>
						 <?php } } else {?>
	
		<div class="col-sm-6 col-md-4 wow fadeInUp"> <h3>No Product Found</h3>
		</div>
		
<?php } ?>	

							<!-- /product -->

							
						<!-- /store products -->
						</div>
							
						


							
						</div>
						
						

						<!-- Shop Page Navigation -->

						
							
						

					</div>

				</div>
			</div>
		</div>
	</div>



	

	<?php
	include("footer.php");
	?>