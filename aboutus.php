<?php
session_start();
if(isset($_SESSION["uid"])){
	header("location:profile.php");
}
include("header.php");
include("dbconnection.php");
$ctins =null;

$dt= date("Y-m-d");
if(isset($_POST["submit"]))
{
$sql ="insert into contact(name,email,phone,message,date) values('$_POST[name]','$_POST[email]','$_POST[phone]','$_POST[message]','$dt')";
if (!mysqli_query($con,$sql))
  {
  die('Error: ' . mysqli_error($con));
  }
$ctins =  mysqli_affected_rows($con);
}
?>

<!-- Map -->

	
	

	<!-- Contact Form -->

	<div class="contact_form">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 offset-lg-1">
					<div class="contact_form_container">
					

						<div class="contact_form_title"></div>
					<div class="contact_form_title">About Us</div>

				<p>Alaabo.com waa shirkad ganacsi oo Soomaaliyeed oo saldhigeedu yahay magaalada Muqdisho. Waa ganacsi ku saleysan habka casriga ah ee internetka oo loo yaqaano Ecommerce. </p>

				<h5>Mission/Shaqada Shirkadda</h5>
				<p>Alaabo.com waa shirkad fududeysa ganacsiga iyada oo laga faaideysanaayo Shabakadda  internet ka iyo adeegga loojistikada. Waxey bixisaa Suuqgeynta, iibinta iyo soo iibsashada badeecadaha, iyo keenis lacag la' aan ah.</p>

				<h5>Vision/Hiigsiga</h5>
				1.	Hogaanka  ganacsiga ecommerce -ga  ee Geeska Afrika. The leading ecommerce enterprise in East Africa.<br>
				2.	 Hormuudka alaabaha  iyo adeegyada tayada leh: The Leading Quality in products and services <br>
				<br>
				<h5>Core Values (Qiyamka Shirkadda)</h5>
				3.	<b>Customer Priority:</b> Markasta waxaa mudnaanta iska leh macaamiilkeena,<br>
				4.	<b>Excellent Services:</b> Adeeg hufan oo tayeysan<br>
				5.	<b>Continuous Improvement:</b> Horumarin joogta ah<br>
				6.	<b>Social Responsibility:</b> Bulshada aan kamidnahay masuuliyad ayaa naga saaran<br>
				7.	<b>Principle-oriented:</b> waxaa dhamaan adeegyadeenna hagaya qiyamka  Islaamiga ah. Ganacsi ka madaxbanaan ribada iyo waxii kale oo aan banaaneyn.  We are all guided by Islamic values, free from interests and other unlawful practices. <br> 
				<br>
				<h5>Badeecadaha Alaabo.com  Iibiso amd suuqgeyso waxaa ka mid ah </h5>
				1.	Dharka qeybihiisa kala duwan sida: Dharka dumarka , ragga, iyo caruurta <br>
				2.	Kabaha ragga iyo dumarka<br>
				3.	Electronics<br>
				4.	Cosmetics/shopping<br>
				5.	Buugaagta <br>
				6.	Saacadaha<br>
				7.	Suumanka<br>
				8.	Baabuurta<br>
				9.	Sports and Toys<br>
				10.	Ticket yada<br>
				11.	 Hantida maguurtada ah<br>



				 <hr>

					</div>
				</div>
			</div>
		</div>
		
	</div>
	<!-- Contact Info -->

	<div class="contact_info">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 offset-lg-1">
					<div class="contact_info_container d-flex flex-lg-row flex-column justify-content-between align-items-between">

						<!-- Contact Item -->
						<div class="contact_info_item d-flex flex-row align-items-center justify-content-start">
							<div class="contact_info_image"><img src="images/contact_1.png" alt=""></div>
							<div class="contact_info_content">
								<div class="contact_info_title">Phone</div>
								<div class="contact_info_text">+252615568673</div>
							</div>
						</div>

						<!-- Contact Item -->
						<div class="contact_info_item d-flex flex-row align-items-center justify-content-start">
							<div class="contact_info_image"><img src="images/contact_2.png" alt=""></div>
							<div class="contact_info_content">
								<div class="contact_info_title">Email</div>
								<div class="contact_info_text">Alaaboinfo@gmail.com</div>
							</div>
						</div>

						<!-- Contact Item -->
						<div class="contact_info_item d-flex flex-row align-items-center justify-content-start">
							<div class="contact_info_image"><img src="images/contact_3.png" alt=""></div>
							<div class="contact_info_content">
								<div class="contact_info_title">Address</div>
								<div class="contact_info_text">Via-rome, Muqdishu, Somali</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	

<?php
	include("footer.php");
	?>