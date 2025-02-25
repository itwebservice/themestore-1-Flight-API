<?php
include "../../../../../../model/model.php";

$booking_id = $_POST['booking_id'];
$customer_id = $_SESSION['customer_id'];


?>

<div class="row mg_tp_20"> <div class="col-md-12"> <div class="table-responsive">
	
<table class="table table-bordered cust_table" id="tbl_refund_list" style="margin:20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Booking_ID</th>
			<th>Refund_Date</th>
			<th>Bank_Name</th>
			<th>Mode</th>
			<th>Cheque_No/ID</th>
			<th class="success">Amount</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$total_refund = 0;
		$query = "select * from car_rental_refund_master where 1";
		if($booking_id!=""){
			$query .=" and booking_id='$booking_id'";
		}
		$query .=" and booking_id in (select booking_id from car_rental_booking where customer_id='$customer_id')";
		$count = 0;
		$bg;
		$sq_pending_amount=0;
		$sq_paid_amount=0;
		$canceled_refund = 0;
		$sq_car_rental_refund = mysqlQuery($query);
		while($row_car_rental_refund = mysqli_fetch_assoc($sq_car_rental_refund)){

			$count++;
			$date = $row_car_rental_refund['refund_date'];
			$yr = explode("-", $date);
			$year =$yr[0];
			$total_refund = $total_refund+$row_car_rental_refund['refund_amount'];

			$sq_car_rental_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$row_car_rental_refund[booking_id]' and delete_status='0'"));
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_car_rental_info[customer_id]'"));

			if($row_car_rental_refund['clearance_status']=="Pending"){ $bg='warning';
				$sq_pending_amount = $sq_pending_amount + $row_car_rental_refund['refund_amount'];
			}

			if($row_car_rental_refund['clearance_status']=="Cleared"){ $bg='success';
				$sq_paid_amount = $sq_paid_amount + $row_car_rental_refund['refund_amount'];
			}
			if($row_car_rental_refund['clearance_status']=="Cancelled"){ 
				$bg = "danger"; 
				$canceled_refund = $canceled_refund + $row_car_rental_refund['refund_amount'];
			}
			if($row_car_rental_refund['clearance_status']==""){ $bg='';
				$sq_paid_amount = $sq_paid_amount + $row_car_rental_refund['refund_amount'];
			}

			$sq_car_rental_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$row_car_rental_refund[booking_id]' and delete_status='0'"));
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_car_rental_info[customer_id]'"));

			?>
			<tr class="<?= $bg;?>">			
				<td><?= $count ?></td>
				<td><?= get_car_rental_booking_id($row_car_rental_refund['booking_id'],$year); ?></td>
				<td><?= date('d-m-Y', strtotime($row_car_rental_refund['refund_date'])) ?></td>
				<td><?= $row_car_rental_refund['bank_name'] ?></td>
				<td><?= $row_car_rental_refund['refund_mode'] ?></td>
				<td><?= $row_car_rental_refund['transaction_id'] ?></td>
				<td class="text-right success"><?= $row_car_rental_refund['refund_amount'] ?></td>			
			</tr>
			<?php
		}
		?>
	</tbody>	
	<tfoot>
		<tr class="active">
			<th colspan="1" class="text-right info">Refund: <?= ($total_refund=='')?number_format(0,2): number_format($total_refund,2); ?></th>
			<th colspan="2" class="text-right warning">Pending : <?= ($sq_pending_amount=='')?number_format(0,2): number_format($sq_pending_amount,2);?></th>
			<th colspan="2" class="text-right danger">Cencelled: <?= ($canceled_refund=='')?number_format(0,2): number_format($canceled_refund,2); ?></th>
			<th colspan="2" class="text-right success">Total: <?= number_format(($total_refund-$sq_pending_amount- $canceled_refund),2);?></th>
		</tr>
	</tfoot>
</table>

</div> </div> </div>
<script>
$('#tbl_refund_list').dataTable({
	"pagingType": "full_numbers"
});
</script>