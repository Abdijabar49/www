<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");
if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
    $id = $_REQUEST['id'];
    $query6 = "select product.* from product where product.id = '$id' ";
    $result2 = mysqli_query($con,$query6);
    $record2 = mysqli_fetch_assoc($result2);
    if (isset($_REQUEST['image'])) {
        $mainimg = $_REQUEST['image'];
    } else {
        $mainimg = $record2["image1"];
    }
    $date = date('Y-m-d H:i:s');
    $query6 = "update product set datetime = '$date'  where id = '$id' ";
    $result2 = mysqli_query($con,$query6);
    $imgUrl = $record2['image1'];
    $updateProductView = "UPDATE product SET total_view=total_view+1 WHERE id = '$id'";
    mysqli_query($con,$updateProductView);
} else {
    header('location:index.php');
}
?>
	<!-- Home -->


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
					<ul id="etalage">
						
							<li>
							<?php 
									if($record2["image1"] == 0)
											{
											?>
									<img class="etalage_thumb_image" src="images/noimage.jpg" class="img-responsive" />
									<img class="etalage_source_image" src="images/noimage.jpg" class="img-responsive" title="" />
										

											<?php 
											}
											else
											{
											?>
									<img class="etalage_thumb_image" src="upload/<?php echo $record2["image1"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="upload/<?php echo $record2["image1"]; ?>" class="img-responsive" title="" />
										<?php
											}
										?>
							</li>
							<li>
							<?php 
									if($record2["image2"] == 0)
											{
											?>
									<img class="etalage_thumb_image" src="images/noimage.jpg" class="img-responsive" />
									<img class="etalage_source_image" src="images/noimage.jpg" class="img-responsive" title="" />
										

											<?php 
											}
											else
											{
											?>
									<img class="etalage_thumb_image" src="upload/<?php echo $record2["image2"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="upload/<?php echo $record2["image2"]; ?>" class="img-responsive" title="" />
										<?php
											}
										?>
							</li>
							<li>
							<?php 
									if($record2["image3"] == 0)
											{
											?>
									<img class="etalage_thumb_image" src="images/noimage.jpg" class="img-responsive" />
									<img class="etalage_source_image" src="images/noimage.jpg" class="img-responsive" title="" />
										

											<?php 
											}
											else
											{
											?>
									<img class="etalage_thumb_image" src="upload/<?php echo $record2["image3"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="upload/<?php echo $record2["image3"]; ?>" class="img-responsive" title="" />
										<?php
											}
										?>
							</li>
							<li>
							<?php 
									if($record2["image4"] == 0)
											{
											?>
									<img class="etalage_thumb_image" src="images/noimage.jpg" class="img-responsive" />
									<img class="etalage_source_image" src="images/noimage.jpg" class="img-responsive" title="" />
										

											<?php 
											}
											else
											{
											?>
									<img class="etalage_thumb_image" src="upload/<?php echo $record2["image4"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="upload/<?php echo $record2["image4"]; ?>" class="img-responsive" title="" />
										<?php
											}
										?>
							</li>
							<li>
							<?php 
									if($record2["image5"] == 0)
											{
											?>
									<img class="etalage_thumb_image" src="images/noimage.jpg" class="img-responsive" />
									<img class="etalage_source_image" src="images/noimage.jpg" class="img-responsive" title="" />
										

											<?php 
											}
											else
											{
											?>
									<img class="etalage_thumb_image" src="upload/<?php echo $record2["image5"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="upload/<?php echo $record2["image5"]; ?>" class="img-responsive" title="" />
										<?php
											}
										?>
							</li>
						</ul>
						 <?php
							$res1 = mysqli_query($con,"SELECT sum(upqr_total_qty) as sumqty FROM `user_product_qty_rel` WHERE upqr_product_id = '$record2[id]' ");
							$sumproduct = mysqli_fetch_array($res1);
							if ($sumproduct['sumqty'] == '') {
								$sumproduct['sumqty'] = 0;
							}

							//p($sumproduct['sumqty']);
							
							?>
						
							<div class="product_description">
						
					
						<div class="product_name"><?php echo $record2['name']; ?></div>
						<div class="product_text"><p></p></div>
						<address>
									
								   <strong>Contact:</strong> <span>615568673,620000158</span>
									<br><strong>Lacation:</strong> <span>Mogadishu-Marka</span>
									<br><strong><?php
                                                if ($record2['qty'] >= $sumproduct['sumqty']) {
                                                    ?>
                                                    Availability: </strong> <span><?php echo $record2['qty'] - $sumproduct['sumqty'] . " Item(s)" ?></span>
                                                <?php } else { ?>
                                                  <strong>  Availability: </strong> <span>SOLD OUT</span>
                                                <?php } ?>
									<br><br><strong>Price:</strong> <span>$<?php echo $record2['price']; ?></span><br>							
								</address>
						
						
						<form action="" method="Post" class="product-form" >
									
									
									<label>Qty:</label>
									<input type="hidden" name="product_color" style="max-width:50px;" value="<?php echo $record2['image1'] ?>" >	
									<input name="product_code" type="hidden" value="<?php echo $record2['id']; ?>">
									<input type="number" class="form-control"  name="product_qty"  value="1"  style="width:60px; height:35px;" >
									<button class="button contact_submit_button" type="submit" ><i class="fa fa-shopping-cart"></i> IIBSO/BUY</button>
								</form>
								
						
					</div>
							
						<!-- /store products -->
						</div>
							
						


							
						</div>
						
						
						
							
						

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