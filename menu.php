<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" href="./eosMenu/eosMenu.css" />
             <?php 
  if(isset($_SESSION["type"]))
  {
  if($_SESSION["type"]=="Administrator")
  {
	  ?>    
                
		<div class="eos-menu" id="menu">
			<div class="eos-menu-title">Welcome to Alaabo<i class="fa fa-bars fa-lg eos-pull-right" aria-hidden="true"></i></div>
			<div class="eos-menu-content">
				<li class="eos-item">
					<a href="dashboard.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
				</li>
				<div class="eos-group-title"><i class="fa fa-user" aria-hidden="true"></i> Manage Users <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="addusers.php"><i class="fa fa-user-plus" aria-hidden="true"></i> New User</a>
					</li>
					<li class="eos-item">
						<a href="listusers.php"><i class="fa fa-users" aria-hidden="true"></i> All Users</a>
					</li>
				</div>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Catagory <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="addcatagory.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Category</a>
					</li>
					<li class="eos-item">
						<a href="viewcatagory.php"><i class="fa fa-users" aria-hidden="true"></i> View Category</a>
					</li>
				</div>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Product <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="addproducts.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Product</a>
					</li>
					<li class="eos-item">
						<a href="listproduct.php"><i class="fa-search-plus" aria-hidden="true"></i> All Product</a>
					</li>
				</div>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Order <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="product-order.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Product Order</a>
					</li>
					
				</div>
				<li class="eos-item">
					<a href="viewcontact.php"><i class="fa fa-gear" aria-hidden="true"></i> Contact</a>
				</li>
				<li class="eos-item">
					<a href="profile.php"><i class="fa fa-gear" aria-hidden="true"></i> My Profile</a>
				</li>
				
				
				<li class="eos-item">
					<a href="logout.php"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a>
				</li>
			</div>
		</div>
				 <?php 
  }
  else if($_SESSION["type"]=="User")
  {

	  ?>
	           <div class="eos-menu" id="menu">
			<div class="eos-menu-title">Welcome to Alaabo<i class="fa fa-bars fa-lg eos-pull-right" aria-hidden="true"></i></div>
			<div class="eos-menu-content">
				<li class="eos-item">
					<a href="dashboard.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
				</li>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Catagory <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="addcatagory.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Category</a>
					</li>
					<li class="eos-item">
						<a href="viewcatagory.php"><i class="fa fa-users" aria-hidden="true"></i> View Category</a>
					</li>
				</div>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Product <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="addproducts.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Product</a>
					</li>
					<li class="eos-item">
						<a href="listproduct.php"><i class="fa-search-plus" aria-hidden="true"></i> All Product</a>
					</li>
				</div>
				<div class="eos-group-title"><i class="fa-product-hunt" aria-hidden="true"></i> Manage Order <i class="fa fa-angle-right fa-lg eos-pull-right" aria-hidden="true"></i></div>
				<div class="eos-group-content">
					<li class="eos-item">
						<a href="product-order.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Product Order</a>
					</li>
					
				</div>
				<li class="eos-item">
					<a href="viewcontact.php"><i class="fa fa-gear" aria-hidden="true"></i> Contact</a>
				</li>
				<li class="eos-item">
					<a href="profile.php"><i class="fa fa-gear" aria-hidden="true"></i> My Profile</a>
				</li>
				
				
				<li class="eos-item">
					<a href="logout.php"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a>
				</li>
			</div>
		</div>
				 <?php 
				  }
  }
	 ?>
	<script src="http://code.jquery.com/jquery-3.1.0.slim.min.js"></script>
		<script src="./eosMenu/eosMenu.js" type="text/javascript" charset="utf-8"></script>
		<script>
			$("#menu").eosMenu();
		</script>
	</body>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>