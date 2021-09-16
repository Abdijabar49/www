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

	<div class="contact_map">
		<div id="google_map" class="google_map">
			<section class="google_map">
				<iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&amp;height=600&amp;hl=en&amp;q=mogadishu+(Alaabo%20)&amp;ie=UTF8&amp;t=&amp;z=14&amp;iwloc=B&amp;output=embed"></iframe>
			</section>
		</div>
	</div>
	

	<!-- Contact Form -->

	<div class="contact_form">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 offset-lg-1">
					<div class="contact_form_container">
						<?php
if($ctins == 1)
{
	echo "<center><h3><b>Message sent successfully......</h3></b></center>";
		
}
else
{
	?>
						<div class="contact_form_title">You may send us a message below</div>
						
				 <form id="form1" name="form1" method="post" action="" onsubmit="return validate()">	
					
							<div class="contact_form_inputs d-flex flex-md-row flex-column justify-content-between align-items-between">
								<input type="text" id="contact_form_name"  name="name" class="contact_form_name input_field" placeholder="Your name" required="required" data-error="Name is required.">
								<input type="text" id="contact_form_email" name="email"  class="contact_form_email input_field" placeholder="Your email" required="required" data-error="Email is required.">
								<input type="text" id="contact_form_phone" name="phone"  class="contact_form_phone input_field" placeholder="Your phone number">
							</div>
							<div class="contact_form_text">
								<textarea id="contact_form_message" class="text_field contact_form_message" name="message" rows="4" placeholder="Message" required="required" data-error="Please, write us a message."></textarea>
							</div>
							<div class="contact_form_button">
								<button type="submit"  name="submit" class="button contact_submit_button">Send Message</button>
							</div>
						</form>
						      <?php
}
?>
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