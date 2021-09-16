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
									<img class="etalage_thumb_image" src="admin/upload/<?php echo $record2["image1"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="admin/upload/<?php echo $record2["image1"]; ?>" class="img-responsive" title="" />
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
									<img class="etalage_thumb_image" src="admin/upload/<?php echo $record2["image2"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="admin/upload/<?php echo $record2["image2"]; ?>" class="img-responsive" title="" />
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
									<img class="etalage_thumb_image" src="admin/upload/<?php echo $record2["image3"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="admin/upload/<?php echo $record2["image3"]; ?>" class="img-responsive" title="" />
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
									<img class="etalage_thumb_image" src="admin/upload/<?php echo $record2["image4"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="admin/upload/<?php echo $record2["image4"]; ?>" class="img-responsive" title="" />
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
									<img class="etalage_thumb_image" src="admin/upload/<?php echo $record2["image5"]; ?>" class="img-responsive" />
									<img class="etalage_source_image" src="admin/upload/<?php echo $record2["image5"]; ?>" class="img-responsive" title="" />
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
						
					
						<div class="product_name"><?php echo $record2['name']; ?> </div>
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
						<h3 class="viewed_title">Product Related</h3>
						<div class="viewed_nav_container">
							<div class="viewed_nav viewed_prev"><i class="fas fa-chevron-left"></i></div>
							<div class="viewed_nav viewed_next"><i class="fas fa-chevron-right"></i></div>
						</div>
					</div>

					<div class="viewed_slider_container">
						
						<!-- Recently Viewed Slider -->

						<div class="owl-carousel owl-theme viewed_slider">
							
							

							<!-- Recently Viewed Item -->
							
							<?php
							$cid=$record2['categoryid'];
							$sql = "SELECT * FROM product where categoryid='$cid' and status='Active'";   
						$rs_result = mysqli_query($con, $sql); 
						while ($data = mysqli_fetch_assoc($rs_result)) {
								$pro_id = $data['id'];
								$pro_name = $data['name'];
								$pro_price = $data['price'];
								$pro_image = $data['image1'];
								?>
								<div class="owl-item">
								<div class="viewed_item d-flex flex-column align-items-center justify-content-center text-center">
									<div class="viewed_image">
									<?php
									if($pro_image == 0)
										{
										?>
										<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										echo "<img src='img/noimage.jpg'   alt='' />";
										}
										else
										{
										?>
										<a href="product-detail.php?id=<?php echo $pro_id; ?>" >
										<?php 
										echo "<img src='admin/upload/$pro_image' alt='img' width='100%' height='100%' >";
										
										
										}
										?>
									
										</a>
									
									
									
									</div>
									<div class="viewed_content text-center">
										<div class="viewed_price">$<?php echo $pro_price; ?> </div>
										<div class="viewed_name"><a href="product-detail.php?id=<?php echo $pro_id; ?>" ><?php echo $pro_name; ?> </a></div>
									</div>
									<ul class="item_marks">
										<li class="item_mark item_discount">-25%</li>
										<li class="item_mark item_new">new</li>
									</ul>
								</div>
								</div>
						<?php  }?>
							

							

						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	include("footer.php");
	?>