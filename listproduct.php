<?php
include("adminheader.php");
if(isset($_GET["delid"]))
{
$sqldel = "DELETE FROM product where id='$_GET[delid]'";
$resdel = mysqli_query($con,$sqldel);
	if(!$resdel)
	{
		$msg= "<font color='red'>Failed to delete... Problem in sql query </font>";
	}
	else
	{
		$msg = "<font color='green'>Record deleted successfully..</font>";
	}
}
?>
        <div class="row">
		
        <!----- Menu Area Start ------>
        <div class="col-md-2 menucontent">
          
           <?php
			include("menu.php");
			?>
        </div>
        <!---- Menu Ares Ends  -------->
<!---- Content Ares Start  -------->
    <div class="col-md-10 maincontent" >
    <!----------------   Menu Tab   --------------->
        <div class="panel panel-default contentinside">
                <div class="panel-heading">List Products</div>
                <!----------------   Panel body Start   --------------->
                <div class="panel-body">
                        <ul class="nav nav-tabs doctor">
								<form action="" name="searchfrm" method="post"
								  enctype="multipart/form-data" >
								   <strong><p style= 'float:right; margin-right:1px; margin-top:6px; 
									font-size:15px;  background-color:lightgoldenrodyellow;'> 
								  <input type="text" name="search_value" style="width:200;height:39px;border-radius:10px;" required>
								  <input class='searching-tab' style="height:39px;width:117px;font-weight:bold; "
								  type="submit" name="search" value="Search"> 
								  </p></strong>
								  </form>
                        </ul>

    <!----------------   Display Doctor Data List Start  --------------->
    
        <div id="doctorlist" class="switchgroup">
		<?php 
										 $msg=null;
				   
$i=1;
	if(isset($_POST['search']))
    {
        $search = $_POST['search_value'];
        $query_search = "SELECT * FROM product WHERE name LIKE '$search%'
		OR quantity LIKE '$search%' OR description LIKE '$search%' OR status LIKE '$search%' OR price LIKE '$search%'";
        $st = mysqli_query($con,$query_search);
		}
		else
		{
$st=mysqli_query($con,"select * from product order by id desc");
		}			   
if (mysqli_num_rows($st)>=1){
?>
		 <div class="clear" style="overflow:scroll;height:600px;">
        <table class="table table-bordered table-hover">
                <tr class="active">
                        <td>#</td>
                        <td>Image</td>
						<td>Name</td>
                        <td>Category</td>
						<td>Price</td>
                        <td>Quantity</td>
						 <td>Discount</td>
                        <td>Options</td>
                </tr>
                
                     <?php
					 $msg=null;
				   
$i=1;
				   


while($row=mysqli_fetch_array($st)){
	$categoryid=$row['categoryid'];
	$sql= mysqli_query($con,"select * from category where id='$categoryid'");
	$row1= mysqli_fetch_array($sql);
	//$sql=mysqli_query($con,"Select * from category where categoryid='categoryid'");
	//$row1=mysqli_fetch_array($sql);
	

    echo '<tr>';
	echo '<td>'.$i++.'</td>';  ?>
	<td width="80"><img src="upload/<?php echo $row['image1'] ?>" width="75" height="75" class="img1"/></td> 
   <?php echo '<td>'.$row['name'].'</td>'; 
   echo '<td>'.$row1['name'].'</td>';
   echo '<td> $ '.$row['price'].'</td>';
	echo '<td>'.$row['qty'].'</td>';
	echo '<td>'.$row['discount'].'</td>';
	
?>
	<td><a class="btn btn-primary" href='addproducts.php?editid=<?php echo $row ['id'];?>'>
						   <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></a> 
                            <a  href='listproduct.php?delid=<?php echo $row ['id'];?>'onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                           
						   </td>
	
	</tr>
	

<?php
}
}
else{
						 echo'<br><br><b><font color="red" size="4">No Result found</font><b/><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';

						}
?>
          
        </table>
        </div>
		</div>
    
                
           <!----------------   Panel body Ends   --------------->
         </div>
    </div>
	</div>
</div>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>