<?php 
include "../../../../model/model.php";

$booking_id = $_POST['booking_id'];
$sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$date = $sq_booking['created_at'];
$yr = explode("-", $date);
$year =$yr[0];

?>
<div class="modal fade profile_box_modal" id="visa_display_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body profile_box_padding">
      	
      	<div>
			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
			    <li role="presentation" class="active"><a href="#basic_information" aria-controls="home" role="tab" data-toggle="tab" class="tab_name">General Information</a></li>
			    <li role="presentation"><a href="#payment_information" aria-controls="profile" role="tab" data-toggle="tab" class="tab_name">Receipt Information</a></li>
			    <li class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>
			  </ul>

              <div class="panel panel-default panel-body fieldset profile_background">

				  <!-- Tab panes1 -->
				  <div class="tab-content">

				    <!-- *****TAb1 start -->
				    <div role="tabpanel" class="tab-pane active" id="basic_information">
				     <?php include "tab1.php"; ?>
				    </div>
				    <!-- ********Tab1 End******** --> 
	                   
	                <!-- ***Tab2 Start*** -->
				    <div role="tabpanel" class="tab-pane" id="payment_information">
				       <?php include "tab2.php"; ?>
				    </div>
	                <!-- ***Tab2 End*** -->

				  </div>

			  </div>
        </div>
        
	   </div>
     
      </div>
    </div>
  
</div>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$('#visa_display_modal').modal('show');
</script>