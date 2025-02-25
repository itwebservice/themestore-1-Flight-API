<?php
include "../../../../../../model/model.php";

$customer_id = $_SESSION['customer_id'];
?>
    <!-- Filter-panel -->

    <div class="app_panel_content Filter-panel">
		<div class="row">
			<div class="col-md-3">
				<select name="booking_id_filterc" id="booking_id_filterc" style="width:100%" onchange="booking_list_reflect()">
			        <option value="">Select Booking</option>
			        <?php 
			        $sq_booking = mysqlQuery("select * from car_rental_booking where customer_id='$customer_id' and delete_status='0'");
			        while($row_booking = mysqli_fetch_assoc($sq_booking)){
						$date = $row_booking['created_at'];
						$yr = explode("-", $date);
						$year =$yr[0];
		              $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
			          ?>
			          <option value="<?= $row_booking['booking_id'] ?>"><?= get_car_rental_booking_id($row_booking['booking_id'],$year).' : '.$sq_customer['first_name'].' '.$sq_customer['last_name'] ?></option>
			          <?php
			        }
			        ?>
			    </select>
			</div>
		</div>
	</div>

<div id="div_booking_list" class="main_block"></div>
<div id="div_car_content_display" class="main_block"></div>
<script>
$('#booking_id_filterc').select2();
function booking_list_reflect()
{
	var booking_id = $('#booking_id_filterc').val();
	$.post('bookings/car_rental/booking/booking_list_reflect.php', { booking_id : booking_id }, function(data){
		$('#div_booking_list').html(data);
	});
}
booking_list_reflect();
function car_display_modal(booking_id)
{
    $('#car-'+booking_id).button('loading');
	$.post('bookings/car_rental/booking/view/index.php', { booking_id : booking_id }, function(data){
		console.log(data);
		$('#div_car_content_display').html(data);
		$('#car-'+booking_id).button('reset');
	});
}
function booking_registration_pdf(booking_id) {

	var base_url = $('#base_url').val();
    url = base_url+"view/car_rental/booking/booking_registration_pdf.php?booking_id=" + booking_id;
    window.open(url, '_BLANK');
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>