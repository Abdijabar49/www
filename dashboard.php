<?php
include("adminheader.php");
?>
		 <div class="row">
  <!--- Header Ends --------->
   <div class="col-md-2 menucontent">
          
           <?php
			include("menu.php");
			?>
        </div>
    
	   <div class="panel-body">
	   <div id="doctorlist" class="switchgroup">
	  	  </form>
                        </ul>

    <!----------------   Display Doctor Data List Start  --------------->
    
        
		
        <!----- Menu Area Start ------>
        
		<h1> <font color='green' size='5'>&nbsp;&nbsp;Welcome, <?php echo $_SESSION["fname"]. " ".$_SESSION["lname"]; ?></font></h1>
		 <h3>Last login date: <?php echo $_SESSION["lastlogin"]; ?></h3><hr />
		<?php
		include("dbconnection.php");
  if(isset($_SESSION["type"]))
  {
  if($_SESSION["type"]=="Administrator")
  {
		?> 
           	  
            <div class="col-md-4">
           	  <a href="listusers.php"><h4>Users</h4></a>
              	<img src="images/employees.jpg" alt="Image 1" width="50" height="50"  class="img-responsive img-rounded img_left"  />             
              	<h4>No. of Users:- 
			<?php 


$sql = "SELECT * FROM users";
$restraining = mysqli_query($con,$sql);
echo mysqli_num_rows($restraining);

?>
                </h4>
                <hr />
            </div>
            
                        <div class="col-md-4">
           	   <a href="product-order.php"><h4>Orders</h4></a>
              	<img src="images/order.jpg" alt="Image 1" width="90" height="100" class="img-responsive img-rounded img_left" />             
              	<h4>No. of Orders:-
                <?php
				
				
$sql = "SELECT * FROM shippingdetails";
$restraining = mysqli_query($con,$sql);
echo mysqli_num_rows($restraining);

?>
              </h4>  <hr />
            </div>
            
                        <div class="col-md-4">
           	 <a href="listproduct.php"><h4>Products</h4></a>
              	<img src="images/Services__Icon_Cart.svg" alt="Image 1" width="90" height="50" class="img-responsive img-rounded img_left" />             
              	<h4>No. of Products:-
            <?php
				
				
$sql = "SELECT * FROM product";
$restraining = mysqli_query($con,$sql);
echo mysqli_num_rows($restraining);

?></h4>
                <hr />
            </div>
            
                        <div class="col-md-4">
           	  <a href="viewcatagory.php"><h4>Category</h4></a>
           	  <img src="images/category.png" alt="Image 1 "width="100" height="200" class="img-responsive img-rounded img_left" />
           	  <h4>No. of Category:-
            <?php
				
				
$sql = "SELECT * FROM category";
$restraining = mysqli_query($con,$sql);
echo mysqli_num_rows($restraining);

?></h4>
                <hr />
            </div>
            
                        
          

</div>

 </div>
		  

<?php 
  }
  }
  ?>
   </div>
		   </div>

  <?php
  include("adminfooter.php");
?>
      