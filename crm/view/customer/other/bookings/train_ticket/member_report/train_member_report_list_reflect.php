<?php
include "../../../../../../model/model.php";

$customer_id = $_SESSION['customer_id'];
$ticket_id = $_POST['ticket_id'];

$query = "select * from train_ticket_master_entries where 1 ";
if($customer_id!=""){
	$query .=" and train_ticket_id in ( select train_ticket_id from train_ticket_master where customer_id='$customer_id' )";
}
if($ticket_id!=""){
	$query .=" and train_ticket_id='$ticket_id'";
}
$query .= " and train_ticket_id in (select train_ticket_id from train_ticket_master where delete_status='0')";
?>
<div class="row mg_tp_20"> <div class="col-md-12"> <div class="table-responsive">
	
<table class="table table-bordered cust_table" id="tbl_ticket_report" style="margin:20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Booking_ID</th>
			<th>Passenger_Name</th>
			<th>Birth_Date</th>
			<th>Adolescence</th>
			<th>Coach_No</th>
			<th>Seats_No</th>
			<th>Ticket_No</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$sq_ticket = mysqlQuery($query);
		while($row_ticket = mysqli_fetch_assoc($sq_ticket)){

			$sq_train_ticket = mysqli_fetch_assoc(mysqlQuery("select customer_id,created_at from train_ticket_master where train_ticket_id='$row_ticket[train_ticket_id]'"));

			$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_train_ticket[customer_id]'"));
			$date = $sq_train_ticket['created_at'];
			$yr = explode("-", $date);
			$year = $yr[0];

			$bg = ($row_ticket['status']=='Cancel') ? 'danger' : '';
			?>
			<tr class="<?= $bg ?>">
				<td><?= ++$count ?></td>
				<td><?= get_train_ticket_booking_id($row_ticket['train_ticket_id'],$year) ?></td>
				<td><?= $row_ticket['first_name']."  ".$row_ticket['last_name'] ?></td>
				<td><?= get_date_user($row_ticket['birth_date']) ?></td>
				<td><?= $row_ticket['adolescence'] ?></td>
				<td><?= $row_ticket['coach_number'] ?></td>
				<td><?= $row_ticket['seat_number'] ?></td>
				<td><?= $row_ticket['ticket_number'] ?></td>
			</tr>
			<?php
			}
		?>
	</tbody>
</table>
</div> </div> </div>
<script type="text/javascript">
$('#tbl_ticket_report').dataTable({
	"pagingType": "full_numbers"
});
</script>