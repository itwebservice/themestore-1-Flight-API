<?php
include "../../../../model/model.php";

$booking_id = $_POST['booking_id'];

$sq_booking_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$sq_payment_info = mysqli_fetch_array(mysqlQuery("SELECT sum(payment_amount) as sum from car_rental_payment where booking_id='$booking_id' AND clearance_status!='Pending' AND clearance_status!='Cancelled'"));
$booking_amt = intval($sq_booking_info['total_fees']) - intval($sq_booking_info['service_tax_subtotal']);
$basic_cost1 = intval($sq_booking_info['basic_amount']) + intval($sq_booking_info['driver_allowance']) + intval($sq_booking_info['permit_charges']) + intval($sq_booking_info['toll_and_parking']) + intval($sq_booking_info['state_entry_tax']) + intval($sq_booking_info['other_charges']);
$tax_show = '';
$newBasic = $basic_cost1;
$newSC = $sq_booking_info['service_charge'];
$service_charge = $sq_booking_info['service_charge'];
$bsmValues = json_decode($sq_booking_info['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$name = '';
if ($sq_booking_info['service_tax_subtotal'] !== 0.00 && ($sq_booking_info['service_tax_subtotal']) !== '') {
	$service_tax_subtotal1 = explode(',', $sq_booking_info['service_tax_subtotal']);
	for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
		$service_tax = explode(':', $service_tax_subtotal1[$i]);
		$service_tax_amount +=  $service_tax[2];
		$name .= $service_tax[0] . ' ';
		$percent = $service_tax[1];
	}
}
if ($bsmValues[0]->service != '') { //inclusive service charge
	$newBasic = $basic_cost1;
	$newSC = $service_tax_amount + $service_charge;
} else {
	$tax_show =  $name . $percent . ($service_tax_amount);
	$newSC = $service_charge;
}
////////////////////Markup Rules
$markupservice_tax_amount = 0;
if ($sq_booking_info['markup_cost_subtotal'] !== 0.00 && $sq_booking_info['markup_cost_subtotal'] !== "") {
	$service_tax_markup1 = explode(',', $sq_booking_info['markup_cost_subtotal']);
	for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
		$service_tax = explode(':', $service_tax_markup1[$i]);
		$markupservice_tax_amount += $service_tax[2];
	}
}
if ($bsmValues[0]->markup != '') { //inclusive markup
	$newBasic = $basic_cost1 + $sq_booking_info['markup_cost'] + $markupservice_tax_amount;
} else {
	$newBasic = $basic_cost1;
	$newSC = $service_charge + $sq_booking_info['markup_cost'];
	$tax_show = ' ( ' . $name . ' ) ' . $percent . ' ' . ($markupservice_tax_amount + $service_tax_amount);
}
////////////Basic Amount Rules
if ($bsmValues[0]->basic != '') { //inclusive markup
	$newBasic = $basic_cost1 + $service_tax_amount + $sq_booking_info['markup_cost'] + $markupservice_tax_amount;
}

?>

<div class="row mg_tp_20">
	<div class="col-sm-10 col-md-offset-1 col-xs-12 col-sm-offset-3">
		<div class="widget_parent-bg-img bg-img-red">
			<?php
			begin_widget();
			$title_arr = array("Basic Amount", "Serv. Charge", "Tax", "Markup", "Markup Tax", "Roundoff", "Total Amount", "Paid Amount");
			$content_arr = array(number_format($basic_cost1, 2), number_format($sq_booking_info['service_charge'], 2), number_format($service_tax_amount, 2), number_format($sq_booking_info['markup_cost'], 2), number_format($markupservice_tax_amount, 2), number_format($sq_booking_info['roundoff'], 2), number_format($sq_booking_info['total_fees'], 2), number_format($sq_payment_info['sum'], 2));
			$percent = ($sq_booking_info['total_fees']!='0') ? ($sq_payment_info['sum'] / $sq_booking_info['total_fees']) * 100 : 0;
			$percent = round($percent, 2);
			$label = "Car Rental Fee Paid In Percent";
			widget_element($title_arr, $content_arr, $percent, $label);
			end_widget(); ?>
			<input type="hidden" id="total_sale" name="total_sale" value="<?= $sq_booking_info['total_fees'] ?>">
			<input type="hidden" id="total_paid" name="total_paid" value="<?= $sq_payment_info['sum'] ?>">
		</div>
	</div>
	<hr class="main_block">
	<div class="col-sm-12">
		<form id="frm_refund" class="no-marg">
			<?php
			if($sq_booking_info['cancel_amount'] == "0.00"){
				$refund_amount = $sq_payment_info['sum'];
			}else{
				$refund_amount = $sq_booking_info['total_refund_amount'];
			}
			?>
			<div class="row">
				<div class="col-md-12 text-center mt-5 mb-5" style="margin-bottom: 20px;">
					<h4>Refund Estimate</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
					<input type="text" name="cancel_amount" id="cancel_amount" class="text-right" placeholder="*Cancel amount(Tax Incl)" title="Cancel amount(Tax Incl)" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_booking_info['cancel_amount'] ?>">
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
					<select title="Select Tax" id="tax_value" name="tax_value" class="form-control" onchange="calculate_total_refund();">
					<?php
					if($sq_booking_info['cancel_flag'] == 0){ ?>
						<option value="">*Select Tax</option>
						<?php get_tax_dropdown('Income') ?>
					<?php }else{
						?>
						<option value="<?= $sq_booking_info['tax_value'] ?>"><?= $sq_booking_info['tax_value'] ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
					<input type="text" title="Tax Subtotal" id="tour_service_tax_subtotal" name="tour_service_tax_subtotal" value="<?= $sq_booking_info['tax_amount'] ?>" readonly>
					<input type="hidden" id="ledger_posting" />
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
					<input type="text" name="cancel_amount_exc" id="cancel_amount_exc" class="text-right" placeholder="*Cancellation Charges" title="Cancellation Charges" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_booking_info['cancel_amount_exc'] ?>">
				</div>
				<div class="col-md-3 col-xs-12 mg_tp_10 mg_bt_20">
					<input type="text" name="total_refund_amount" id="total_refund_amount" class="amount_feild_highlight text-right" placeholder="Total Refund" title="Total Refund" readonly value="<?= $refund_amount ?>">
				</div>
				<?php if ($sq_booking_info['cancel_flag'] == 0) { ?>
					<div class="col-md-3 mg_tp_10">
						<button id="btn_refund_save" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
					</div>
				<?php  } ?>
			</div>
		</form>
	</div>
</div>
<hr>
<?php

$sq_car_rental_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$sq_payment_info = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from car_rental_payment where booking_id='$booking_id' and clearance_status!='Pending' AND clearance_status!='Cancelled'"));
$sq_refund_info = mysqli_fetch_assoc(mysqlQuery("select sum(refund_amount) as sum from car_rental_refund_master where booking_id='$booking_id' and clearance_status!='Pending' AND clearance_status!='Cancelled'"));

$sale_amount = $sq_car_rental_info['total_fees'];
$paid_amount = $sq_payment_info['sum'];
$cancel_amount = $sq_car_rental_info['cancel_amount'];
$refund_amount = $sq_car_rental_info['total_refund_amount'];
$remaining_pay = $refund_amount - $sq_refund_info['sum'];
?>
<input type="hidden" id="refund_amount_to_give" name="refund_amount_to_give" value="<?php echo $refund_amount ?>">
<div class="row mg_tp_20 mg_bt_10">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12 mg_bt_10_xs">
		<div class="widget_parent-bg-img bg-green">
			<div class="widget_parent">
				<div class="stat_content main_block">
					<span class="main_block content_span" data-original-title="" title="">
						<span class="stat_content-tilte pull-left" data-original-title="" title="">Total Sale</span>
						<span class="stat_content-amount pull-right" data-original-title="" title=""><?= ($sale_amount == '') ? '0.00' : number_format($sale_amount, 2) ?></span>
					</span>
					<span class="main_block content_span" data-original-title="" title="">
						<span class="stat_content-tilte pull-left" data-original-title="" title="">Paid Amount</span>
						<span class="stat_content-amount pull-right" data-original-title="" title=""> <?= ($paid_amount == '') ? '0.00' : $paid_amount ?></span>
					</span>
					<span class="main_block content_span" data-original-title="" title="">
						<span class="stat_content-tilte pull-left" data-original-title="" title="">Cancellation Amount</span>
						<span class="stat_content-amount pull-right" data-original-title="" title=""><?= number_format($cancel_amount, 2); ?></span>
					</span>
					<span class="main_block content_span" data-original-title="" title="">
						<span class="stat_content-tilte pull-left" data-original-title="" title="">Refund Amount</span>
						<span class="stat_content-amount pull-right" data-original-title="" title=""><?php echo number_format($refund_amount, 2); ?></span>
					</span>
					<span class="main_block content_span" data-original-title="" title="">
						<span class="stat_content-tilte pull-left" data-original-title="" title="">Pending Refund</span>
						<span class="stat_content-amount pull-right" data-original-title="" title=""><?php echo number_format($remaining_pay, 2); ?></span>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<hr>
<?php if ($sq_booking_info['total_refund_amount'] != "") { ?>
	<div class="row text-right mg_bt_10">
		<div class="col-xs-12">
			<button class="btn btn-info btn-sm ico_left" data-toggle="modal" data-target="#save_modal"><i class="fa fa-plus"></i>Refund</button>
		</div>
	</div>
<?php } ?>
<?php include_once('save_refund_modal.php'); ?>


<div class="row">
	<div class="col-xs-12 no-pad">
		<div class="table-responsive">
			<table class="table table-bordered" id="tbl_carrefund_list">
				<thead>
					<tr class="table-heading-row">
						<th>S_No</th>
						<th>Refund_Date</th>
						<th class="text-right">Amount</th>
						<th>Mode</th>
						<th>Bank_Name</th>
						<th>Cheque_No/ID</th>
						<th>Voucher</th>
					</tr>
				</thead>
				<tbody>
					<?php
					global $currency;
					$total_refund = 0;
					$query = "select * from car_rental_refund_master where booking_id='$booking_id' and refund_amount!=0";
					$count = 0;
					$sq_car_rental_refund = mysqlQuery($query);
					while ($row_car_rental_refund = mysqli_fetch_assoc($sq_car_rental_refund)) {

						$count++;

						if ($row_car_rental_refund['clearance_status'] == "Pending") {
							$bg = "warning";
						} else if ($row_car_rental_refund['clearance_status'] == "Cancelled") {
							$bg = "danger";
						} else if ($row_car_rental_refund['clearance_status'] == "Cleared") {
							$bg = "success";
						} else {
							$bg = "";
						}

						$sq_car_rental_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$row_car_rental_refund[booking_id]' and delete_status='0'"));
						$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_car_rental_info[customer_id]'"));
						if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
							$cust_name = $sq_customer['company_name'];
						}else{
							$cust_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
						}

						$date = $row_car_rental_refund['refund_date'];
						$yr = explode("-", $date);
						$year = $yr[0];

						$v_voucher_no = get_car_rental_booking_refund_id($row_car_rental_refund['refund_id'], $year);
						$v_refund_date = $row_car_rental_refund['refund_date'];
						$v_refund_to = $cust_name;
						$v_service_name = "Car Rental Booking";
						$v_refund_amount = $row_car_rental_refund['refund_amount'];
						$v_payment_mode = $row_car_rental_refund['refund_mode'];
						$customer_id = $sq_car_rental_info['customer_id'];
						$refund_id = $row_car_rental_refund['refund_id'];

						$url = BASE_URL . "model/app_settings/generic_refund_voucher_pdf.php?v_voucher_no=$v_voucher_no&booking_id=".get_car_rental_booking_id($booking_id,$year)."&v_refund_date=$v_refund_date&v_refund_to=$v_refund_to&v_service_name=$v_service_name&v_refund_amount=$v_refund_amount&v_payment_mode=$v_payment_mode&customer_id=$customer_id&refund_id=$refund_id&currency_code=$currency";

					?>
						<tr class="<?= $bg ?>">
							<td><?= $count ?></td>
							<td><?= get_date_user($row_car_rental_refund['refund_date']) ?></td>
							<td class="text-right"><?= number_format($row_car_rental_refund['refund_amount'], 2) ?></td>
							<td><?= $row_car_rental_refund['refund_mode'] ?></td>
							<td><?= $row_car_rental_refund['bank_name'] ?></td>
							<td><?= $row_car_rental_refund['transaction_id'] ?></td>
							<td><a href="<?= $url ?>" class="btn btn-danger btn-sm" target="_blank" title="Voucher"><i class="fa fa-file-pdf-o"></i></a></td>
						</tr>
					<?php
					}
					?>
				</tbody>
				<tfoot>
					<?php
					$sq_total_pay = mysqli_fetch_array(mysqlQuery("SELECT sum(refund_amount) as sum from car_rental_refund_master where booking_id='$booking_id'"));
					$sq_pending_pay = mysqli_fetch_array(mysqlQuery("SELECT sum(refund_amount) as sum from car_rental_refund_master where booking_id='$booking_id' AND clearance_status='Pending'"));
					$sq_cancel_pay = mysqli_fetch_array(mysqlQuery("SELECT sum(refund_amount) as sum from car_rental_refund_master where booking_id='$booking_id' AND clearance_status='Cancelled'"));
					?>
					<tr class="active">
						<th colspan="2" class="text-right info">Refund : <?= ($sq_total_pay['sum'] == "") ? number_format(0, 2) : number_format($sq_total_pay['sum'], 2) ?></th>
						<th colspan="2" class="text-right warning">Pending : <?= ($sq_pending_pay['sum'] == "") ? number_format(0, 2) : number_format($sq_pending_pay['sum'], 2) ?></th>
						<th colspan="1" class="text-right danger">Cancelled : <?= number_format((($sq_cancel_pay['sum'] == "") ? number_format(0, 2) : $sq_cancel_pay['sum']), 2); ?></th>
						<th colspan="2" class="text-right success">Total_Refund : <?= number_format(($sq_total_pay['sum'] - $sq_pending_pay['sum'] - $sq_cancel_pay['sum']), 2) ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<input type="hidden" id="ref_amt" value="<?= ($sq_total_pay['sum'] == "") ? 0 : $sq_total_pay['sum'] ?>">
<script>
	$('#tbl_carrefund_list').dataTable({
		"pagingType": "full_numbers"
	});

	function calculate_total_refund() {
		var total_refund_amount = 0;
		var applied_taxes = '';
		var ledger_posting = '';
		var cancel_amount = $('#cancel_amount').val();
		var total_sale = $('#total_sale').val();
		var total_paid = $('#total_paid').val();
		var tax_value = $('#tax_value').val();

		if (cancel_amount == "") {
			cancel_amount = 0;
		}
		if (total_paid == "") {
			total_paid = 0;
		}

		if (parseFloat(cancel_amount) > parseFloat(total_sale)) {
			error_msg_alert("Cancel amount can not be greater than Sale amount");
		}
		if(tax_value!=""){
			var service_tax_subtotal1 = tax_value.split("+");
			for(var i=0;i<service_tax_subtotal1.length;i++){
				var service_tax_string = service_tax_subtotal1[i].split(':');
				if(parseInt(service_tax_string.length) > 0){
					var service_tax_string1 = service_tax_string[1] && service_tax_string[1].split('%');
					service_tax_string1[0] = service_tax_string1[0] && service_tax_string1[0].replace('(','');
					service_tax = service_tax_string1[0];
				}

				service_tax_string[2] = service_tax_string[2].replace('(','');
				service_tax_string[2] = service_tax_string[2].replace(')','');
				service_tax_amount = (( parseFloat(cancel_amount) * parseFloat(service_tax) ) / 100).toFixed(2);
				if(applied_taxes==''){
					applied_taxes = service_tax_string[0] +':'+ service_tax_string[1] + ':' + service_tax_amount;
					ledger_posting = service_tax_string[2];
				}else{
					applied_taxes += ', ' + service_tax_string[0] +':'+ service_tax_string[1] + ':' + service_tax_amount;
					ledger_posting += ', ' + service_tax_string[2];
				}
			}
		}
		$('#tour_service_tax_subtotal').val(applied_taxes);
		var service_tax_subtotal = $('#tour_service_tax_subtotal').val();
		if (service_tax_subtotal == "") {
			service_tax_subtotal = '';
		}
		var service_tax_amount = 0;
		if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== '') {
			var service_tax_subtotal1 = service_tax_subtotal.split(',');
			for (var i = 0; i < service_tax_subtotal1.length; i++) {
				var service_tax = service_tax_subtotal1[i].split(':');
				service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
			}
		}
		
		var cancel_amount_exc = parseFloat(cancel_amount) - parseFloat(service_tax_amount);
		var total_refund_amount = parseFloat(total_paid) - parseFloat(cancel_amount);

		if (parseFloat(total_refund_amount) < 0) {
			total_refund_amount = 0;
		}
		$('#cancel_amount_exc').val(cancel_amount_exc);
		$('#ledger_posting').val(ledger_posting);
		$('#total_refund_amount').val(total_refund_amount.toFixed(2));
	}

	$(function() {
		$('#frm_refund').validate({
			rules: {
				booking_id: {
					required: true
				},
				refund_amount1: {
					required: true,
					number: true
				},
				tax_value: { required: true }
			},
			submitHandler: function(form) {

				var booking_id = $('#booking_id').val();
				var cancel_amount = $('#cancel_amount').val();
				var total_refund_amount = $('#total_refund_amount').val();
				var total_sale = $('#total_sale').val();
				var total_paid = $('#total_paid').val();
                var tax_value = $('#tax_value').val();
                var tour_service_tax_subtotal = $('#tour_service_tax_subtotal').val();
                var cancel_amount_exc = $('#cancel_amount_exc').val();
                var ledger_posting = $('#ledger_posting').val();
				if (parseFloat(cancel_amount) > parseFloat(total_sale)) {
					error_msg_alert("Cancel amount can not be greater than Sale amount");
					return false;
				}

				var base_url = $('#base_url').val();
				$('#vi_confirm_box').vi_confirm_box({
					message: 'Are you sure?',
					callback: function(data1) {
						if (data1 == "yes") {

							$('#btn_refund_estimate').button('loading');

							$.ajax({
								type: 'post',
								url: base_url + 'controller/car_rental/refund/refund_estimate_update.php',
								data: {
									booking_id: booking_id,
									cancel_amount: cancel_amount,
									total_refund_amount: total_refund_amount,tax_value:tax_value,tour_service_tax_subtotal:tour_service_tax_subtotal,cancel_amount_exc:cancel_amount_exc,ledger_posting:ledger_posting
								},
								success: function(result) {
									msg_alert(result);
									$('#btn_refund_estimate').button('reset');
									refund_content_reflect();
								}
							});

						}
					}
				});

			}

		});

	});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>