<?php
include "../../../../model/model.php";

$exc_id = $_POST['exc_id'];
?>

<div class="row mg_tp_20">
	<div class="col-md-6 col-md-offset-0 col-sm-10  col-xs-12 mg_tp_10">
		
	</div>
</div>
<div class="row mg_bt_20">

	<div class="col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-2 col-xs-12 mg_bt_20_xs">
		<div class="widget_parent-bg-img bg-img-red">
			<?php
				$sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id' and delete_status='0'"));
				$sq_payment_info = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from exc_payment_master where exc_id='$exc_id' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
				$sq_refund_info = mysqli_fetch_assoc(mysqlQuery("select sum(refund_amount) as sum from exc_refund_master where exc_id='$exc_id' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
				
				if($sq_exc_info['exc_issue_amount'] == ''){ $exc_amount = 0; }
				else{ $exc_amount = $sq_exc_info['exc_issue_amount']; }
				if($sq_exc_info['service_charge'] == ''){ $service_charge = 0; }
				else{ $service_charge = $sq_exc_info['service_charge']; }
				if($sq_exc_info['service_tax_subtotal'] == ''){ $subtotal_amount = 0; }
				else{ $subtotal_amount = $sq_exc_info['service_tax_subtotal']; }

				$tax_show = '';
				$newBasic = 0;
				$name = '';
				$basic_cost1=$exc_amount;
				//////////////////Service Charge Rules
				$service_tax_amount = 0;
				if($sq_exc_info['service_tax_subtotal'] !== 0.00 && ($sq_exc_info['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$sq_exc_info['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
					$service_tax = explode(':',$service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
					$name .= $service_tax[0]  . $service_tax[1] .', ';
				}
				}
				$bsmValues = json_decode($sq_exc_info['bsm_values']);
				if($bsmValues[0]->service != ''){   //inclusive service charge
				$newBasic = $basic_cost1;
				$newSC = $service_tax_amount + $service_charge;

				}
				else{
				$tax_show =  rtrim($name, ', ').' : ' . ($service_tax_amount);
				$newSC = $service_charge;
				}
				////////////////////Markup Rules
				$markupservice_tax_amount = 0;
				if($sq_exc_info['service_tax_markup'] !== 0.00 && $sq_exc_info['service_tax_markup'] !== ""){
				$service_tax_markup1 = explode(',',$sq_exc_info['service_tax_markup']);
				for($i=0;$i<sizeof($service_tax_markup1);$i++){
					$service_tax = explode(':',$service_tax_markup1[$i]);
					$markupservice_tax_amount += $service_tax[2];

				}
				}

				if($bsmValues[0]->markup != ''){ //inclusive markup
				$newBasic = $basic_cost1 + $sq_exc_info['markup'] + $markupservice_tax_amount;

				}
				else{
				$newBasic = $basic_cost1;
				$newSC = $service_charge + $sq_exc_info['markup'];
				$tax_show = rtrim($name, ', ') .' : ' . ($markupservice_tax_amount + $service_tax_amount);
				}
				////////////Basic Amount Rules
				if($bsmValues[0]->basic != ''){ //inclusive markup
				//$newBasic = $basic_cost1 + $service_tax_amount;
				$newBasic = $basic_cost1 + $service_tax_amount + $sq_exc_info['markup'] + $markupservice_tax_amount;
				$tax_show = '';
				}

				begin_widget();
				$title_arr = array("Basic Amount","Serv. Charge", " Tax","Markup","Markup Tax","Roundoff","Total Amount","Paid Amount");
				$content_arr = array( number_format($exc_amount,2) ,number_format($service_charge,2), number_format($service_tax_amount,2),number_format($sq_exc_info['markup'],2),number_format($markupservice_tax_amount,2),number_format($sq_exc_info['roundoff'],2), number_format($sq_exc_info['exc_total_cost'],2),number_format($sq_payment_info['sum'],2));
				$percent = ($sq_exc_info['exc_total_cost']!='0')?($sq_payment_info['sum']/$sq_exc_info['exc_total_cost'])*100 : 0;
				$percent = round($percent, 2);
				$label = "Activity Fee Paid In Percent";
				widget_element($title_arr, $content_arr, $percent, $label);
				end_widget();
	        ?>
		<input type="hidden" id="total_sale" name="total_sale" value="<?= $sq_exc_info['exc_total_cost']?>">	        
		<input type="hidden" id="total_paid" name="total_paid" value="<?= $sq_payment_info['sum']?>">	  
		</div>
	</div>
	</div>
	<div class="row">

	<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12"> <div class="table-responsive">
	<input type="checkbox" id="check_all" name="check_all" onClick="select_all_check(this.id,'traveler_names')">&nbsp;&nbsp;&nbsp;<span style="text-transform: initial;">Check All</span>
		<table class="table table-hover table-bordered mg_bt_0" style="margin: 0 !important;">
			<thead>
				<tr class="table-heading-row">
					<th>S_No.</th>
					<th>Activity_Name</th>
					<th>Cancel</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$count = 0;
			$disabled_count =0;
			$sq_exc_entries = mysqlQuery("select * from excursion_master_entries where exc_id='$exc_id'");
			while($row_entry = mysqli_fetch_assoc($sq_exc_entries)){
				$checked = '';
				$sq_exc = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id = '$row_entry[exc_name]'"));
				if($row_entry['status']=="Cancel"){
					$bg = "danger";
					$checked = "checked disabled";
					++$disabled_count;
				}
				?>
				<tr class="<?= $bg ?>">
					<td><?= ++$count ?></td>
					<td><?= $sq_exc['excursion_name'] ?></td>
					<td>
						<input type="checkbox" id="chk_entry_id_<?= $count ?>" class="traveler_names" name="chk_entry_id" <?= $checked ?> value="<?= $row_entry['entry_id'] ?>">
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
			<input type="hidden" id="pass_count" name="pass_count" value="<?= $count ?>">
			<input type="hidden" id="disabled_count" name="disabled_count" value="<?= $disabled_count ?>">
			
			<?php
			if($count != $disabled_count){ ?>
				<div class="panel panel-default panel-body text-center">
					<button class="btn btn-danger btn-sm ico_left" id="cancel_booking1" onclick="cancel_booking()"><i class="fa fa-times"></i>&nbsp;&nbsp;Cancel Booking</button>
				</div>
				<div class="note"><span style="color: red;line-height: 35px;" data-original-title="" title=""><?= $cancel_feild_note ?></span></div>
			<?php } ?>
		</div> </div> 

</div>
<hr>
<?php 
$sq_cancel_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$exc_id' and status='Cancel'"));
if($sq_cancel_count>0){

	$sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id' and delete_status='0'"));
	if($sq_exc_info['cancel_amount'] == "0.00"){
		$refund_amount = $sq_payment_info['sum'];
	}else{
		$refund_amount = $sq_exc_info['total_refund_amount'];
	}
?>
<form id="frm_refund" class="mg_bt_150">
<div class="row">
		<div class="col-md-12 text-center mt-5 mb-5" style="margin-bottom: 20px;">
			<h4>Refund Estimate</h4>
		</div>
	</div>
	<div class="row text-center">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
			<input type="text" name="cancel_amount" id="cancel_amount" class="text-right" placeholder="*Cancel amount(Tax Incl)" title="Cancel amount(Tax Incl)" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_exc_info['cancel_amount'] ?>">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
			<select title="Select Tax" id="tax_value" name="tax_value" class="form-control" onchange="calculate_total_refund();">
			<?php
			if($sq_exc_info['cancel_flag'] == 0){ ?>
				<option value="">*Select Tax</option>
				<?php get_tax_dropdown('Income') ?>
			<?php }else{
				?>
				<option value="<?= $sq_exc_info['tax_value'] ?>"><?= $sq_exc_info['tax_value'] ?></option>
			<?php } ?>
			</select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
			<input type="text" title="Tax Subtotal" id="tour_service_tax_subtotal" name="tour_service_tax_subtotal" value="<?= $sq_exc_info['tax_amount'] ?>" readonly>
			<input type="hidden" id="ledger_posting" />
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
			<input type="text" name="cancel_amount_exc" id="cancel_amount_exc" class="text-right" placeholder="*Cancellation Charges" title="Cancellation Charges" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_exc_info['cancel_amount_exc'] ?>">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_tp_10 mg_bt_10_xs">
			<input type="text" name="total_refund_amount" id="total_refund_amount" class="amount_feild_highlight text-right" placeholder="Total Refund" title="Total Refund" readonly value="<?= $refund_amount ?>">
		</div>
	</div>
	<?php if($sq_exc_info['cancel_flag'] == 0){ ?>
	<div class="row mg_tp_20">
		<div class="col-md-6 col-md-offset-3 text-center">
			<button id="btn_refund_save" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
		</div>
	</div>
	<?php } ?>

</form>
<?php
}
?>

<script>
function cancel_booking(){

	var entry_id_arr = new Array();
	$('input[name="chk_entry_id"]:checked').each(function(){
		entry_id_arr.push($(this).val());
	});

	//Validaion to select complete tour cancellation 
	var pass_count = $('#pass_count').val();
	var disabled_count = $('#disabled_count').val();
	var len = $('input[name="chk_entry_id"]:checked').length;
	if(len!=pass_count){
		error_msg_alert('Please select all activities for cancellation.');
	}
	else if(pass_count == disabled_count){
		error_msg_alert('All the activities have been already cancelled');
	}
	else
	{
		$('#vi_confirm_box').vi_confirm_box({
			message: 'Are you sure?',
			callback: function(data1){
				if(data1=="yes"){

					var base_url = $('#base_url').val();
					$('#cancel_booking1').button('loading');

					$.ajax({
					type: 'post',
					url: base_url+'controller/excursion/cancel/cancel_booking.php',
					data:{ entry_id_arr : entry_id_arr },
					success: function(result){
						msg_alert(result);
						$('#cancel_booking1').button('reset');
						exc_entries_reflect();
					}
					});

				}

			}

		});
	}
}

function calculate_total_refund()
{
	var total_refund_amount = 0;
	var applied_taxes = '';
	var ledger_posting = '';
	var cancel_amount = $('#cancel_amount').val();
	var total_sale = $('#total_sale').val();
	var total_paid = $('#total_paid').val();
	var tax_value = $('#tax_value').val();

	if(cancel_amount==""){ cancel_amount = 0; }
	if(total_paid==""){ total_paid = 0; }

	if(parseFloat(cancel_amount) > parseFloat(total_sale)) { error_msg_alert("Cancel amount can not be greater than Sale amount"); }
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
	
	if(parseFloat(total_refund_amount) < 0){ 
		total_refund_amount = 0;
	}
	$('#cancel_amount_exc').val(cancel_amount_exc);
	$('#ledger_posting').val(ledger_posting);
	$('#total_refund_amount').val(total_refund_amount.toFixed(2));
}
$(function(){
	$('#frm_refund').validate({
		rules:{
				exc_id : { required: true },
				cancel_amount : { required : true, number : true },
				total_refund_amount : { required : true, number : true },
				tax_value: { required: true }
		},
		submitHandler:function(form){

				var exc_id = $('#exc_id').val();
				var cancel_amount = $('#cancel_amount').val();
				var total_refund_amount = $('#total_refund_amount').val();
				var total_sale = $('#total_sale').val();
				var total_paid = $('#total_paid').val();
                var tax_value = $('#tax_value').val();
                var tour_service_tax_subtotal = $('#tour_service_tax_subtotal').val();
                var cancel_amount_exc = $('#cancel_amount_exc').val();
                var ledger_posting = $('#ledger_posting').val();

				if(parseFloat(cancel_amount) > parseFloat(total_sale)) { error_msg_alert("Cancel amount can not be greater than Sale amount"); return false; }

				var base_url = $('#base_url').val();

				$('#vi_confirm_box').vi_confirm_box({
					message: 'Are you sure?',
					callback: function(data1){
						if(data1=="yes"){

							$('#btn_refund_save').button('loading');

							$.ajax({
							type:'post',
							url: base_url+'controller/excursion/cancel/refund_estimate_update.php',
							data:{ exc_id : exc_id,cancel_amount : cancel_amount, total_refund_amount : total_refund_amount,tax_value:tax_value,tour_service_tax_subtotal:tour_service_tax_subtotal,cancel_amount_exc:cancel_amount_exc,ledger_posting:ledger_posting  },
							success:function(result){
								msg_alert(result);
								exc_entries_reflect();
								$('#btn_refund_save').button('reset');
							},
							error:function(result){
								console.log(result.responseText);
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