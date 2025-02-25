<?php
include "../../../../../../model/model.php";

$customer_id = $_SESSION['customer_id'];
$booking_id = $_POST['booking_id'];

$query = "select * from hotel_booking_master where 1 and delete_status='0' ";
$query .=" and customer_id='$customer_id'";
if($booking_id!=""){
	$query .=" and booking_id='$booking_id'";
}

?>
<div class="row mg_tp_20"> <div class="col-md-12"> <div class="table-responsive">
	
<table class="table table-bordered bg_white cust_table" id="tbl_report" style="margin:20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Booking_ID</th>
			<th>City</th>
			<th>Hotel_Name</th>
			<th>Check_In</th>
			<th>Check_Out</th>
			<th>Rooms</th>
			<th>Room_Type</th>
			<th>Extra_Beds</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$sq_booking = mysqlQuery($query);
		while($row_booking = mysqli_fetch_assoc($sq_booking)){
			
			$date = $row_booking['created_at'];
			$yr = explode("-", $date);
			$year =$yr[0];
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));

			$sq_entry=mysqlQuery("SELECT * from hotel_booking_entries where booking_id='$row_booking[booking_id]'");
			while($row_entry = mysqli_fetch_assoc($sq_entry))
			{
				$sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_entry[city_id]'"));
				$sq_hotel_nm = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_entry[hotel_id]'"));

				$bg = ($row_entry['status']=="Cancel") ? "danger" : "";
				?>
				<tr class="<?= $bg ?>">
					<td><?= ++$count ?></td>
					<td><?= get_hotel_booking_id($row_booking['booking_id'],$year) ?></td>
					<td><?= $sq_city['city_name'] ?></td>
					<td><?= $sq_hotel_nm['hotel_name'] ?></td>
					<td><?= date('d-m-Y H:i', strtotime($row_entry['check_in'])) ?></td>
					<td><?= date('d-m-Y H:i', strtotime($row_entry['check_out'])) ?></td>
					<td><?= $row_entry['rooms'] ?></td>
					<td><?= $row_entry['room_type'] ?></td>
					<td><?= $row_entry['extra_beds'] ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>

</div> </div> </div>
<script type="text/javascript">
	
$('#tbl_report').dataTable({
	"pagingType": "full_numbers"
});
</script>