<?php
//Sale
$sale_total_amount=$sq_visa_info['ticket_total_cost']+$charge;
if($sale_total_amount==""){  $sale_total_amount = 0 ;  }

//Cancel
$cancel_amount=$sq_visa_info['cancel_amount'];
$pass_count = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$sq_visa_info[ticket_id]'"));
$cancel_count = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$sq_visa_info[ticket_id]' and status='Cancel'"));

//Paid
$query = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum from ticket_payment_master where ticket_id='$ticket_id' and clearance_status != 'Pending' and clearance_status != 'Cancelled'"));
$paid_amount = $query['sum']+$charge;
$paid_amount = ($paid_amount == '')?'0':$paid_amount;

if($sq_visa_info['cancel_type'] == '1'){
	if($paid_amount > 0){
		if($cancel_amount >0){
			if($paid_amount > $cancel_amount){
				$balance_amount = 0;
			}else{
				$balance_amount = $cancel_amount - $paid_amount + $charge;
			}
		}else{
			$balance_amount = 0;
		}
	}
	else{
		$balance_amount = $cancel_amount;
	}
}else if($sq_visa_info['cancel_type'] == '2'||$sq_visa_info['cancel_type'] == '3'){
		$cancel_estimate_data = json_decode($sq_visa_info['cancel_estimate']);
		$cancel_estimate = (!isset($cancel_estimate_data)) ? 0 : $cancel_estimate_data[0]->ticket_total_cost;
		$balance_amount = (($sale_total_amount - floatval($cancel_estimate)) + $cancel_amount) - $paid_amount;
}
else{
	$balance_amount = $sale_total_amount - $paid_amount;
}
$balance_amount = ($balance_amount < 0) ? 0 : $balance_amount;

include "../../../../../model/app_settings/generic_sale_widget.php";
?>
<div class="row mg_tp_30">

<div class="col-xs-12">

	    <h3 class="editor_title">Summary</h3>

	    <div class="table-responsive">

	        <table class="table table-hover table-bordered no-marg" id="tbl_payment_list">

				<thead>

					<tr class="table-heading-row">

						<th>S_No</th>

						<th>Date</th>

						<th>Mode</th>

						<th>Bank_Name</th>

						<th>Cheque No/ID</th>

						<th class="text-right">Amount</th>

					</tr>

				</thead>

				<tbody>

					<?php
					$query = "select * from ticket_payment_master where ticket_id='$ticket_id' and payment_amount!='0'";		

					$count = 0;
					$sq_pending_amount=0;
					$sq_cancel_amount=0;
					$sq_paid_amount=0;
					$total_payment=0;
					$sq_ticket_payment = mysqlQuery($query);
					while($row_ticket_payment = mysqli_fetch_assoc($sq_ticket_payment)){

						$count++;
						$sq_ticket_info = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$row_ticket_payment[ticket_id]' and delete_status='0'"));
						$bg='';
						$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_ticket_info[customer_id]'"));

						if($row_ticket_payment['clearance_status']=="Pending"){ 
							$bg='warning';
						}

						else if($row_ticket_payment['clearance_status']=="Cancelled"){ 
							$bg='danger';
						}
						else if($row_ticket_payment['clearance_status']=="Cleared"){ 
							$bg='success';
						}

						else{ $bg=''; }
						?>
						<tr class="<?= $bg?>">
							<td><?= $count ?></td>

							<td><?= get_date_user($row_ticket_payment['payment_date']) ?></td>

							<td><?= $row_ticket_payment['payment_mode'] ?></td>

							<td><?= $row_ticket_payment['bank_name'] ?></td>

							<td><?= $row_ticket_payment['transaction_id'] ?></td>

							<td class="text-right"><?= number_format($row_ticket_payment['payment_amount']+ $row_ticket_payment['credit_charges'],2) ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				</table>

		</div>

</div>

</div>

