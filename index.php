<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");
$dt = date("Y-m-d h:i:s");
?>
	
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
						include("dbconnection.php");	
							
		
			
				// qaybta display products //				
			$limit = 12;  
						if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };  
						$start_from = ($page-1) * $limit; 
	
	
					
			$sql = "SELECT * FROM product where status='Active' ORDER BY id desc LIMIT $start_from, $limit";   
						$rs_result = mysqli_query($con, $sql); 
						while ($data = mysqli_fetch_assoc($rs_result)) {
								$pro_id = $data['id'];
								$pro_name = $data['name'];
								$pro_price = $data['price'];
								$pro_image = $data['image1'];
								
								

							
								?>
							<!-- product -->
							<div class="col-md-4 col-xs-6">
							
							
								<div class="product">
								
								
									<div class="product-img">
									<?php
									if($pro_image == 0)
										{
										?>
								
										<?php 
										
										echo "<img src='images/noimage.jpg'  width='100%' height='180' alt='' />";
										}
										else
										{
										?>
										
										
										<?php 
										echo "<img src='admin/upload/$pro_image' alt='img' width='100%' height='180' >";
										
										
										}
										?>
									
										
										<!--div class="product-label">
													<span class="sale">-30%</span>
												</div>
												<div class="product-label">
													<span class="new">NEW</span>
												</div>
												<div class="product-label">
													<span class="sale">-30%</span>
													<span class="new">NEW</span>
												</div-->
										
									</div>
									
									
									<div class="product-body">
										
										<h3 class="product-name"><?php echo $pro_name; ?></h3>
										<h4 class="product-price">$<?php echo $pro_price; ?> <del class="product-old-price"></del></h4>
										
										<!--div class="product-btns">
													<button class="add-to-wishlist"><i class="fa fa-heart"></i><span class="tooltipp">add to wishlist</span></button>
													<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></button>
												</div-->
										
									</div>
									<form action="" method="Post" class="product-form" >
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
							<?php						
			
		
						}
					
	?>
							<!-- /product -->

							
						<!-- /store products -->
						</div>
							<?php  
							$sql = "SELECT COUNT(id) FROM product";  
							$rs_result = mysqli_query($con, $sql);  
							$row = mysqli_fetch_row($rs_result);  
							$total_records = $row[0];  
							$total_pages = ceil($total_records / $limit);  
							$pagLink = "<nav><ul class='pagination'>";  
							for ($i=1; $i<=$total_pages; $i++) {  
										 $pagLink .= "<li><a href='index.php?page=".$i."'>".$i."</a></li>";  
							};  
							echo $pagLink . "</ul></nav>";  
					?>
						
						<script type="text/javascript">
						$(document).ready(function(){
						$('.pagination').pagination({
								items: <?php echo $total_records;?>,
								itemsOnPage: <?php echo $limit;?>,
								cssStyle: 'light-theme',
								currentPage : <?php echo $page;?>,
								hrefTextPrefix : 'index.php?page='
							});
							});
						</script>
						


							
						</div>
						
						

						<!-- Shop Page Navigation -->

						
							
						

					</div>

				</div>
			</div>
		</div>
	</div>



					
											
												<div class="custom_dropdown_placeholder clc"></div>
											
												<div class="custom_list clc">
													
												</div>
											
										


	<?php
	include("footer.php");
	?>