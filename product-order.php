<?php
include("adminheader.php");
if(isset($_GET["delid"]))
{
$sqldel = "DELETE FROM shippingdetails where id='$_GET[delid]'";
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
   <?php 
	$msg=null;
$dt= date("Y-m-d"); 
if(isset($_POST["insert"]))
{ 
	
			$sqlupd = "UPDATE shippingdetails SET status='$_POST[status]',comments='$_POST[comments]' WHERE id='$_POST[shippingid]'";
	$qresult = mysqli_query($con,$sqlupd);
	$ctins =  mysqli_affected_rows($con);
			if(!$qresult)
	{
		$msg = "<br><font color='green' size='4'>Failed to update record </font>";
	}
	else
	{
		$msg = "<font color='green' size='4'><br>Record updated successfully...</font>";

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
                <div class="panel-heading">Manage Orders</div>
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
        $query_search = "SELECT * FROM shippingdetails WHERE id LIKE '$search%'
		OR payment_type LIKE '$search%' OR total LIKE '$search%' OR  firstname LIKE '$search%' OR  lastname LIKE '$search%'";
        $st = mysqli_query($con,$query_search);
		}
		else
		{
$st=mysqli_query($con,"select * from shippingdetails order by id desc");
		}			   
if (mysqli_num_rows($st)>=1){
?>
		 <div class="clear" style="overflow:scroll;height:600px;">
        <table class="table table-bordered table-hover">
                <tr class="active">
                        <td>#</td>
                        <td>Order ID</td>
						<td>Ordar Date</td>
                        <td>Amount</td>
						<td>Order Method</td>
                        <td>Show Status</td>
						<td>View</td>
                        <td>Options</td>
                </tr>
                
                     <?php
					 $msg=null;
				   
			$i=1;
							   


			while($row=mysqli_fetch_array($st)){

				echo '<tr>';
				echo '<td>'.$i++.'</td>'; 
				echo '<td>'.$row['id'].'</td>'; 
			   echo '<td>'.$row['created'].'</td>';
			   echo '<td> $ '.$row['total'].'</td>';
				echo '<td>'.$row['payment_type'].'</td>';
				echo '<td>'.$row['status'].'</td>';
				
			?>
	<td> 
                            <a  href='product-order-details.php?prodid=<?php echo $row ['id'];?>' class="btn btn-danger" ><span class="glyphicon glyphicon-list" aria-hidden="true"></span></a>
                           
						   </td>
						   <td> 
                           <a  href='product-order.php?prodid=<?php echo $row ['id'];?>'  class="btn btn-danger" ><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a>
                           <a  href='product-order.php?delid=<?php echo $row ['id'];?>' onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>

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
    <!----------------   Display Doctor Data List Ends  --------------->
   
<!----------------   Modal ends here  --------------->
<?php 
$dt= date("Y-m-d"); 
if(isset($_POST["insert"]))
{ 

 $sql1 = "select username from users where username = '$_POST[username]'";
    $result1 = mysqli_query($con,$sql1) or die ("Couldn't execute query.");										  
    
	$num1=mysqli_num_rows($result1);
	if($num1 == 1)
	{
		?>
	 <script>
	 alert("Sorry ! username allready exists , Please Try another one");
	 </script>
<?php
		
		$msg = "<font color='red'> Sorry ! username allready exists , Please Try another one  </font><font color='darkBlue'>".$_POST["username"]."</font>";
	}
	else
	{   
$usersSql=mysqli_query($con,"insert into users(firstName,lastName,contact,email,jobTitle,email,gender,username,password,userType,status,registerDate) values('$_POST[fname]','$_POST[lname]','$_POST[contact]','$_POST[jobTitle]','$_POST[gender]','$_POST[username]','$_POST[password]','$_POST[userType]','$_POST[status]','$dt')");
if(!$usersSql)
	{
		$msg ="<font color='red'>Failed to insert... Problem in sql query<</font>";
	}
	else
	{
		?>
	 <script>
	 alert("Record Inserted Successfully");
	 window.location="managerusers.php";
	 </script>
<?php

	}
	}
	
}

$query=mysqli_query($con,"SELECT max(userid) FROM users");
$rs=mysqli_fetch_array($query);
$GetID=$rs['max(userid)']+1;

?>
<script type="text/javascript">
function validate() 
{
	if(document.showroom.password.value != document.showroom.conpass.value)
	{
		alert("Password and confirm password not matching.");
		document.showroom.conpass.focus();
		return false;
	}
 else if(document.showroom.status.value== "Select")
	{
		alert("Please select status");
		document.showroom.doctid.focus();
		return false;
	}
else if(document.showroom.sex.value== "Select")
	{
		alert("Please select gender");
		document.showroom.doctid.focus();
		return false;
	}
}
</script>
    
           <!----------------   Panel body Ends   --------------->
         </div>
    </div>
	</div>
</div>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>