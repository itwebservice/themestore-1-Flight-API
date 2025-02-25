<?php
include "../../../../../model/model.php";

$ticket_id = $_POST['ticket_id'];
$from_date = $_POST['payment_from_date'];
$to_date = $_POST['payment_to_date'];

$query = "select * from ticket_refund_master where 1 ";
if ($ticket_id != "") {
	$query .= " and ticket_id='$ticket_id'";
}
if ($from_date != '' && $to_date != '') {
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .= " and refund_date between '$from_date' and '$to_date'";
}
?>
<div class="row mg_tp_20">
	<div class="col-xs-12 no-pad">
		<div class="table-responsive">

			<table class="table table-bordered table-hover" id="tbl_refund" style="margin: 20px 0 !important;">
				<thead>
					<tr class="table-heading-row">
						<th>S_No.</th>
						<th>Booking_ID</th>
						<th>Refund_To</th>
						<th>Refund_ID</th>
						<th>Refund_Date</th>
						<th>Mode</th>
						<th>Bank_Name</th>
						<th>Cheque_No/ID</th>
						<th class="text-right">Refund_Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = $total_refund =  $sq_pending_amount = $sq_cancel_amount = 0;
					$sq_refund = mysqlQuery($query);
					while ($row_refund = mysqli_fetch_assoc($sq_refund)) {

						$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id=(select customer_id from ticket_master where ticket_id='$row_refund[ticket_id]')"));
						if ($sq_cust['type'] == 'Corporate' || $sq_cust['type'] == 'B2B') {
							$cust_name = $sq_cust['company_name'];
						} else {
							$cust_name = $sq_cust['first_name'] . ' ' . $sq_cust['last_name'];
						}
						$sq_refund_entries = mysqlQuery("select * from ticket_refund_entries where refund_id='$row_refund[refund_id]'");
						while ($row_refund_entry = mysqli_fetch_assoc($sq_refund_entries)) {
							$sq_entry_date = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$row_refund[ticket_id]' and delete_status='0'"));
							$date = $sq_entry_date['created_at'];
							$yr = explode("-", $date);
							$year = $yr[0];
						}
						$date = $row_refund['refund_date'];
						$yr1 = explode("-", $date);
						$year1 = $yr1[0];
						$total_refund = $total_refund + $row_refund['refund_amount'];
						if ($row_refund['clearance_status'] == "Pending") {
							$bg = 'warning';
							$sq_pending_amount = $sq_pending_amount + $row_refund['refund_amount'];
						} else if ($row_refund['clearance_status'] == "Cancelled") {
							$bg = 'danger';
							$sq_cancel_amount = $sq_cancel_amount + $row_refund['refund_amount'];
						} else if ($row_refund['clearance_status'] == "Cleared") {
							$bg = 'success';
						} else {
							$bg = '';
						}
					?>
						<tr class="<?= $bg ?>">
							<td><?= ++$count ?></td>
							<td><?= get_ticket_booking_id($row_refund['ticket_id'], $year); ?></td>
							<td><?= $cust_name ?></td>
							<td><?= get_ticket_booking_refund_id($row_refund['refund_id'], $year1); ?></td>
							<td><?= date('d-m-Y', strtotime($row_refund['refund_date'])) ?></td>
							<td><?= $row_refund['refund_mode'] ?></td>
							<td><?= $row_refund['bank_name'] ?></td>
							<td><?= $row_refund['transaction_id'] ?></td>
							<td class="text-right success"><?= $row_refund['refund_amount'] ?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr class="active">
						<th class="text-right info" colspan="3">Refund Amount :<?= number_format($total_refund, 2) ?></th>
						<th class="text-right warning" colspan="2">Pending Clearance :<?= ($sq_pending_amount == "") ? number_format(0, 2) : number_format($sq_pending_amount, 2) ?></th>
						<th class="text-right danger" colspan="2">Cancelled :<?= ($sq_cancel_amount == "") ? number_format(0, 2) : number_format($sq_cancel_amount, 2) ?></th>
						<th class="text-right success" colspan="2">Total_Refund :<?= number_format(($total_refund - $sq_pending_amount - $sq_cancel_amount), 2) ?></th>
					</tr>
				</tfoot>
			</table>

		</div>
	</div>
</div>
<script>
	$('#tbl_refund').dataTable({
		"pagingType": "full_numbers"
	});
</script>