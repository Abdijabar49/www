<?php
session_start();
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
					<div class="clearfix">
							
						</div>
						
							<div class="row">
					
							<!-- product -->
							<?php
							
							
		
			
				// qaybta display products //
 if (isset($_GET['search'])) 
 {				

        $search = $_GET['search'];
        $query_search = "SELECT * FROM product WHERE name LIKE '$search%' 
		OR description LIKE '$search%' 
		";
        $st = mysqli_query($con,$query_search);
		}
					   
if (mysqli_num_rows($st)>=1){
	
	
					
		
					while($row=mysqli_fetch_array($st)){
							
								$pro_id = $row['id'];
								$pro_name = $row['name'];
								$pro_price = $row['price'];
								$pro_image = $row['image1'];
								
								

							
								?>
							<!-- product -->
							<div class="col-md-4 col-xs-6">
							
							
								<div class="product">
								
								
									<div class="product-img">
									<?php
									if($pro_image == 0)
										{
										?>
									<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										
										echo "<img src='images/noimage.jpg'  width='100%' height='180' alt='' />";
										}
										else
										{
										?>
										</a>
										<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										echo "<img src='upload/$pro_image' alt='img' width='100%' height='180' >";
										
										
										}
										?>
									
										</a>
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
										
										<h3 class="product-name"><a href="product-detail.php?id=<?php echo $pro_id; ?>" ><?php echo $pro_name; ?></a></h3>
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
}	
else {
    ?>

                        <br><br><br><h3 style="color: red;">No result found.</h3>

                        <?php
                    }
                    ?>				<!-- /product -->

							
						<!-- /store products -->
						</div>
							
						


							
						</div>
						
						

						<!-- Shop Page Navigation -->

						
							
						

					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- Recently Viewed -->

	<div class="viewed">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="viewed_title_container">
						<h3 class="viewed_title">Recently Viewed</h3>
						<div class="viewed_nav_container">
							<div class="viewed_nav viewed_prev"><i class="fas fa-chevron-left"></i></div>
							<div class="viewed_nav viewed_next"><i class="fas fa-chevron-right"></i></div>
						</div>
					</div>

					<div class="viewed_slider_container">
						
						<!-- Recently Viewed Slider -->

						<div class="owl-carousel owl-theme viewed_slider">
							
							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item discount d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_1.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$225<span>$300</span></div>
										<div class="viewed_name"><a href="#">Beoplay H7</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>

							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_2.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$379</div>
										<div class="viewed_name"><a href="#">LUNA Smartphone</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>

							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_3.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$225</div>
										<div class="viewed_name"><a href="#">Samsung J730F...</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>

							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item is_new d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_4.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$379</div>
										<div class="viewed_name"><a href="#">Huawei MediaPad...</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>

							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item discount d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_5.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$225<span>$300</span></div>
										<div class="viewed_name"><a href="#">Sony PS4 Slim</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>

							<!-- Recently Viewed Item -->
							<div class="owl-item">
								<div class="viewed_item d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image"><img src="images/view_6.jpg" alt=""></div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$375</div>
										<div class="viewed_name"><a href="#">Speedlink...</a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Brands -->

	
<script src="themes/js/common.js"></script>
		<script src="themes/js/jquery.flexslider-min.js"></script>
		<script type="text/javascript">
			$(function() {
				$(document).ready(function() {
					$('.flexslider').flexslider({
						animation: "fade",
						slideshowSpeed: 4000,
						animationSpeed: 600,
						controlNav: false,
						directionNav: true,
						controlsContainer: ".flex-container" // the container that holds the flexslider
					});
				});
			});
		</script>

	<?php
	include("footer.php");
	?>