<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");
$dt = date("Y-m-d h:i:s");
?>

	<!-- Cart -->

	<div class="cart_section">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 offset-lg-1">
					<div class="cart_container">
					<div class="cart_title">Shopping Cart</div>
						<div class="cart_items">
					
		<h3>My Cart (<span id="cart-items-count"><?PHP if(isset($_SESSION["products"])){echo count($_SESSION["products"]); }else{ echo 0;} ?></span>)</h3>			

						
						
						
			<?php		
			if(isset($_SESSION["products"]) && count($_SESSION["products"])>0) { 
			?>
				<table class="table" id="shopping-cart-results" width="20%">
				<thead>
				<tr>
				<th>Item</th>
				<th>Product</th>
				<th>Price</th>
				<th width="150">Quantity</th>
				<th>Subtotal</th>
				<th>Remove</th>
				</tr>
				</thead>
				
				<tbody>
			<?php
				$cart_box = '<ul class="cart-products-loaded">';
				$total = 0;
				foreach($_SESSION["products"] as $product){					
					$product_name = $product["product_name"]; 
					$product_price = $product["product_price"];
					$product_code = $product["product_code"];
					$product_qty = $product["product_qty"];
					$product_color = $product["product_color"];					
					$subtotal = ($product_price * $product_qty);
					$total = ($total + $subtotal);
				?>
				<tr>
				
				<td>
				
				<?php
						if($product_color == 0)
							{
							?>
							<img src="images/noimage.jpg"  style="height:90px; width:100px;" class="img-responsive" alt="img" />";

							<?php 
							}
							else
							{
							?>
							<img src='admin/upload/<?php echo $product_color; ?>' border="0" style="height:90px; width:100px;"></td>
						<?php
							}
						?>
				
				
				<td><?php echo $product_name;?></td>
				<td><?php echo "$" .$product_price; ?></td>
				<td width="90"><input type="number" data-code="<?php echo $product_code; ?>" class="form-control text-center quantity" value="<?php echo $product_qty; ?>" style="width:80px;"></td>
				<td><?php echo '$'; echo sprintf("%01.2f", ($product_price * $product_qty)); ?></td>
				<td>				
				<a href="#" class="btn btn-danger remove-item" data-code="<?php echo $product_code; ?>"><i class="glyphicon glyphicon-trash">X</i></a>
				</td>
				</tr>
				<?php } ?>
				</tbody>
				<tfoot>
			<br>
			<br>
			<tr>
			<?php 
			if(isset($total)) {
			?>	
			<td class="text-center cart-products-total"><strong><font color="#000">Total $<?php echo sprintf("%01.2f",$total); ?></strong></td>
			<?php } ?>
			</tr>
			</tfoot>
				
			</table>
			
			
					
			
			<div class="cart_buttons">
						
							<a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a>
							<?php 
					if(isset($total)) {
					?>
							<a href="checkout.php" class="btn btn-success">Checkout <i class="glyphicon glyphicon-menu-right"></i></a>
					
						</div>
					<?php
					}
					?>
						
			<?php		
			} else {
				echo "Your Cart is empty";
			?>
		<br><br>
			<div class="cart_buttons">
			<a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a>
			</div>
			
			<?php } ?>				
				
		
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	include("footer.php");
	?>