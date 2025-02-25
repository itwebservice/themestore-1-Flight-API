<?php
include '../../../../model/model.php';
include_once('../../../layouts/fullwidth_app_header.php');
$financial_year_id = $_SESSION['financial_year_id'];
$sq_finance = mysqli_fetch_assoc(mysqlQuery("select from_date,to_date from financial_year where financial_year_id='$financial_year_id'"));
$financial_from_date = $sq_finance['from_date'];
$financial_to_date = $sq_finance['to_date'];
?>
<div class="bk_tabs">
<div id="tab_1" class="bk_tab active">

<form id="frm_vendor_estimate_save">

	<div class="app_panel">  
	<!--=======Header panel======-->

	<div class="container mg_tp_10">

		<input type="hidden" id="unique_timestamp" name="unique_timestamp" value="<?= md5(time()) ?>">
		<input type="hidden" id="purchase_sc" name="purchase_sc">
		<input type="hidden" id="purchase_commission" name="purchase_commission">
		<input type="hidden" id="purchase_taxes" name="purchase_taxes">
		<input type="hidden" id="purchase_tds" name="purchase_tds">
		
		<div class="app_panel_content no-pad">

        	<div class="panel panel-default panel-body main_block bg_light">
				<legend>Purchase Information</legend>
				<div class="bg_white main_block panel-default-inner">

				<input type="hidden" id="branch_admin_id1" name="branch_admin_id1" value="<?= $branch_admin_id ?>" >
				<input type="hidden" id="financial_from_date" name="financial_from_date" value="<?= $financial_from_date ?>" >
				<input type="hidden" id="financial_to_date" name="financial_to_date" value="<?= $financial_to_date ?>" >
				<input type="hidden" id="emp_id" name="emp_id" value="<?= $emp_id ?>" >
				<input type="hidden" id="financial_year_id" name="financial_year_id" value="<?= $financial_year_id ?>" >

				<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20 mg_bt_10">
				<legend>Select Sale</legend>
					<div class="row">
						<div class="col-md-3 col-sm-3 col-xs-12 mg_bt_10">
							<select name="estimate_type" id="estimate_type" title="Purchase Type" onchange="get_purchase_flag('0',this.id);payment_for_data_load(this.value, 'div_payment_for_content');brule_for_all();">
								<option value="">*Purchase Type</option>
								<?php 
								$sq_estimate_type = mysqlQuery("select * from estimate_type_master order by id");
								while($row_estimate = mysqli_fetch_assoc($sq_estimate_type)){
									?>
									<option value="<?= $row_estimate['estimate_type'] ?>"><?= $row_estimate['estimate_type'] ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div id="div_payment_for_content"></div>
						<div class="col-md-6 col-sm-3 col-xs-12 mg_bt_10">
							<input type="text" id="purchase_flag" name="purchase_flag" placeholder="List of purchases already done" readonly/>
						</div>
					</div>
					<div class="row text-right">  
						<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10 text-left" id="currency_div">
						</div>
						<div class="col-xs-9">
							<button type="button" class="btn btn-info btn-sm ico_left" onclick="estimate_section_add()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Section</button>
						</div>
					</div>
					<input type="hidden" id="dynamic_estimate_count" name="dynamic_estimate_count" value="0">
					<input type="hidden" id="estimate_count" name="estimate_count">
					<div id="div_dynamic_estimate"></div>
					<div class="row">
					</div>
					<div class="row">
						<div class="col-xs-12 text-center">
							<button class="btn btn-sm btn-success" id="btn_save_estimate"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
						</div>
					</div>
	    		</div>
			</div>
		</div>
	</div>
</form>
</div>
</div>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="../../js/vendor_master.js"></script>
<script src="../js/calculation.js"></script>
<script>
$('#currency_code').select2();
var base_url = $('#base_url').val();
$.get(base_url + 'view/hotels/booking/inc/get_currency_dropdown.php', {}, function (data) {
    $('#currency_div').html(data);
});
function calculate_estimate_amount(offset='')
{
	var basic_cost = $('#basic_cost'+offset).val();
	var non_recoverable_taxes = $('#non_recoverable_taxes'+offset).val();
	var service_charge = $('#service_charge'+offset).val();
	var other_charges = $('#other_charges'+offset).val();
	var service_tax_subtotal = $('#service_tax_subtotal'+offset).val();
	var discount = $('#discount'+offset).val();
	var our_commission = $('#our_commission'+offset).val();
	var tds = $('#tds'+offset).val();

	if(basic_cost==""){ basic_cost = 0; }
	if(non_recoverable_taxes==""){ non_recoverable_taxes = 0; }
	if(service_charge==""){ service_charge = 0; }
	if(other_charges==""){ other_charges = 0; }
	if(service_tax==""){ service_tax = 0; }
	if(discount==""){ discount = 0; }
	if(our_commission==""){ our_commission = 0; }
	if(tds==""){ tds = 0; }

	var total_charge = parseFloat(service_charge) + parseFloat(other_charges);

	var service_tax_amount = 0;
    if(parseFloat(service_tax_subtotal) !== 0.00 && (service_tax_subtotal) !== ''){

		var service_tax_subtotal1 = service_tax_subtotal.split(",");
		for(var i=0;i<service_tax_subtotal1.length;i++){
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
    	}
    }

	var net_total = parseFloat(basic_cost) + parseFloat(non_recoverable_taxes) + parseFloat(total_charge) + parseFloat(service_tax_amount) - parseFloat(discount) + parseFloat(our_commission) + parseFloat(tds);
	net_total = parseFloat(net_total.toFixed(2));
	if(net_total < 0) net_total = 0.00;	
	var roundoff = Math.round(net_total)-net_total;
	$('#roundoff'+offset).val(roundoff.toFixed(2));
	$('#net_total'+offset).val(net_total+roundoff);
}

function estimate_section_add()
{
	var dynamic_estimate_count = $('#dynamic_estimate_count').val();
	dynamic_estimate_count = parseFloat(dynamic_estimate_count) + 1;
	$.post('dynamic_estimate_section.php', { dynamic_estimate_count : dynamic_estimate_count }, function(data){
		$('#div_dynamic_estimate').append(data);
		$('#dynamic_estimate_count').val(dynamic_estimate_count);
	});
}

function upload_invoice_pic_attch()
{	
    var dynamic_estimate_count = $('#dynamic_estimate_count').val();
	dynamic_estimate_count = parseFloat(dynamic_estimate_count) + 1
    var btnUpload=$('#id_upload_btn'+dynamic_estimate_count);

    $(btnUpload).find('span').text('Upload Invoice');

    $('#id_upload_url'+dynamic_estimate_count).val('');
    
    new AjaxUpload(btnUpload, {
		action: 'upload_invoice_proof.php',
		name: 'uploadfile',
		onSubmit: function(file, ext)
		{  
			if (! (ext && /^(jpg|png|jpeg|pdf)$/.test(ext))){ 
			error_msg_alert('Only JPG, PNG and PDF files are allowed');
			return false;
			}
			$(btnUpload).find('span').text('Uploading...');
		},
		onComplete: function(file, response){
			if(response==="error"){          
			error_msg_alert("File is not uploaded.");           
			$(btnUpload).find('span').text('Upload Again');
			}else
			{ 
			$(btnUpload).find('span').text('Uploaded');
			$('#id_upload_url'+dynamic_estimate_count).val(response);
			msg_alert('File uploaded!');
			}
		}
    });
}
estimate_section_add();

function brule_for_all(){
	var iteration =  parseInt($('#dynamic_estimate_count').val());
	
	while(iteration-- > 0)
		get_auto_values('purchase_date_s-'+(iteration+1),'basic_cost_s-'+(iteration+1),'payment_mode','service_charge_s-'+(iteration+1),'save','true','service_charge','discount_s-'+(iteration+1),'our_commission_s-'+(iteration+1), iteration+1);
}

function brule_for_one(id,charges_flag,tx_for){
	var offset = id.split('-')[1];
	get_auto_values('purchase_date_s-'+(offset),'basic_cost_s-'+(offset),'payment_mode','service_charge_s-'+(offset),'save',charges_flag,tx_for,'discount_s-'+(offset),'our_commission_s-'+(offset),offset);
}

function close_estimate(id){
	$('#'+id).remove();
	var dynamic_estimate_count = $('#dynamic_estimate_count').val();
	dynamic_estimate_count = parseFloat(dynamic_estimate_count) - 1;
	$('#dynamic_estimate_count').val(dynamic_estimate_count);
}

// New Customization ----start
$(document).ready(function(e){

		let searchParams = new URLSearchParams(window.location.search);
		if(searchParams.get('type')){

			var type = searchParams.get('type');
			$('#estimate_type').val(type);
			$('#estimate_type').trigger('change');
			setTimeout(() => {
				if(type == 'Hotel'){
					var purchase_id = 'booking_id';
				}
				else if(type=="Miscellaneous"){
					var purchase_id = 'misc_id';
				}
				else if(type=="Train"){
					var purchase_id = 'train_ticket_id';
				}
				else if(type=="Flight"){
					var purchase_id = 'ticket_id';
				}
				else if(type=="Visa"){
					var purchase_id = 'visa_id';
				}
				else if(type=="Activity"){
					var purchase_id = 'exc_id';
				}
				else if(type=="Car Rental"){
					var purchase_id = 'booking_id';
				}
				else if(type=="Bus"){
					var purchase_id = 'booking_id';
				}
				else if(type=="Package Tour"){
					var purchase_id = 'booking_id';
				}
				else{

				}
				if(type != "Package Tour" && type != "Hotel"){
					$('#basic_cost_s-1').val(searchParams.get('amount'));
					$('#basic_cost_s-1').trigger('change');
				}
				if(type == "Package Tour"){

					var booking_id = searchParams.get('booking_id');
					get_package_data(booking_id,'Package Tour');
					dynamic_section_add('', $('#dynamic_estimate_count').val()+1,'','','Transport Vendor');
				}
				else if(type == "Hotel"){

					var booking_id = searchParams.get('booking_id');
					get_package_data(booking_id,'Hotel');
				}

				if(type!="Group Tour"){
					$('#'+purchase_id).val(searchParams.get('booking_id'));
					setTimeout(() => {
						document.getElementById(purchase_id).selectedIndex = "1";
						$('#'+purchase_id).trigger('change');
					}, 1000);
				}
				$('#basic_cost_s-1').val(searchParams.get('amount'));
				$('#basic_cost_s-1').trigger('change');
			}, 1000);
		}
});
// New Customization ----end
$(function(){
	$('#frm_vendor_estimate_save').validate({
		rules:{
			estimate_type: { required: function(){ if($('#vendor_type').val()!='Other Vendor'){ return true }else{ return false; } } },
			basic_cost_s : { number : true},
			non_recoverable_taxes_s : { number : true},
			service_charge_s : { number : true},
			other_charges_s : { number : true},
			discount_s : { number : true},
			our_commission_s : { number : true},
			tds_s : { number : true},
		},
		submitHandler:function(form,e){

        	$('#btn_save_estimate').prop('disabled',true);
			var base_url = $('#base_url').val();
			
			var status = validate_estimate_vendor('estimate_type', 'null');
			var financial_from_date = $('#financial_from_date').val();
			var financial_to_date = $('#financial_to_date').val();
			var financial_year_id = $('#financial_year_id').val();
            var currency_code = $('#hcurrency_code').val();
			if(!status){ $('#btn_save_estimate').prop('disabled',false); return false; }
			
			var estimate_type = $('#estimate_type').val();
			var estimate_type_id = get_estimate_type_id('estimate_type');
			
			var vendor_type_arr = new Array();
			var vendor_type_id_arr = new Array();
			var basic_cost_arr = new Array();
			var non_recoverable_taxes_arr = new Array();
			var service_charge_arr = new Array();
			var other_charges_arr = new Array();
			var service_tax_subtotal_arr = new Array();
			var discount_arr = new Array();
			var our_commission_arr = new Array();
			var tds_arr = new Array();
			var net_total_arr = new Array();
			var roundoff_arr = new Array();
			var remark_arr = new Array();
			var invoice_id_arr = new Array();
			var payment_due_date_arr = new Array();
			var invoice_url_arr = new Array();
			var purchase_date_arr = new Array();
			var reflection_arr = [];

			var branch_admin_id = $('#branch_admin_id1').val();
			var emp_id = $('#emp_id').val();
			var purchase_sc = $('#purchase_sc').val();
			var purchase_commission = $('#purchase_commission').val();
			var purchase_taxes = $('#purchase_taxes').val();
			var purchase_tds = $('#purchase_tds').val();
			var reflections = [];
			reflections.push({
			'purchase_sc':purchase_sc,
			'purchase_commission':purchase_commission,
			'purchase_taxes':purchase_taxes,
			'purchase_tds':purchase_tds
			});
			var msg = "";
			var counter = 0;
			$('[name="vendor_type"]').each(function(){
				counter++;
				var id = $(this).attr('id');
				var offset = id.substring(11);
				var offset1 = id.substring(14);
				var t_offset = id.split('-')[1];
				var vendor_type = $('#vendor_type'+offset).val();
				
				var vendor_type_id = get_vendor_type_id(id, offset);
				var basic_cost = $('#basic_cost'+offset).val();
				var non_recoverable_taxes = $('#non_recoverable_taxes'+offset).val();
				var service_charge = $('#service_charge'+offset).val();
				var other_charges = $('#other_charges'+offset).val();
				var service_tax_subtotal = $('#service_tax_subtotal'+offset).val();
				var discount = $('#discount'+offset).val();
				var our_commission = $('#our_commission'+offset).val();
				var tds = $('#tds'+offset).val();
				var net_total = $('#net_total'+offset).val();
				var roundoff = $('#roundoff'+offset).val();
				var remark = $('#remark'+offset).val();
				var invoice_id = $('#invoice_id'+offset).val();
				var payment_due_date = $('#payment_due_date'+offset).val();
				var invoice_url = $('#id_upload_url'+offset1).val();
				var purchase_date = $('#purchase_date'+offset).val();
				
				var tax_apply_on = $('#tax_apply_on-'+t_offset).val();
				var tax_value = $('#tax_value-'+t_offset).val();
				if(vendor_type==""){ msg +=">Supplier type is required in vendor estimate-"+counter+"<br>"; }
				if(vendor_type_id==""){ msg +=">"+vendor_type+" is required in vendor estimate-"+counter+"<br>"; }
				if(estimate_type_id==""){ msg += ">"+estimate_type+" booking is required"+"<br>"; }
				if(basic_cost==""){ msg +=">Basic Amount is required in vendor estimate-"+counter+"<br>"; }
				if(net_total==""){ msg +=">Net total is required in vendor estimate-"+counter+"<br>"; }
				if(tax_apply_on==""){ msg +=">Tax apply on is required in vendor estimate-"+counter+"<br>"; }
				if(tax_value==""){ msg +=">Tax is required in vendor estimate-"+counter+"<br>"; }
				
				//Purchase date validation
				var dateFrom = financial_from_date;
				var dateTo = financial_to_date;
				var dateCheck = purchase_date;

				var d1 = dateFrom.split("-");
				var d2 = dateTo.split("-");
				var c = dateCheck.split("-");

				var from = new Date(d1[0], parseInt(d1[1])-1, d1[2]); // -1 because months are from 0 to 11
				var to   = new Date(d2[0], parseInt(d2[1])-1, d2[2]);
				var check = new Date(c[2], parseInt(c[1])-1, c[0]);

				vendor_type_arr.push(vendor_type);
				vendor_type_id_arr.push(vendor_type_id);
				basic_cost_arr.push(basic_cost);
				non_recoverable_taxes_arr.push(non_recoverable_taxes);
				service_charge_arr.push(service_charge);
				other_charges_arr.push(other_charges);
				service_tax_subtotal_arr.push(service_tax_subtotal);
				discount_arr.push(discount);
				our_commission_arr.push(our_commission);
				tds_arr.push(tds);
				net_total_arr.push(net_total);
				roundoff_arr.push(roundoff);
				remark_arr.push(remark);
				invoice_id_arr.push(invoice_id);
				payment_due_date_arr.push(payment_due_date);
				invoice_url_arr.push(invoice_url);
				reflection_arr.push([{'tax_apply_on':tax_apply_on,
					'tax_value':tax_value}]);

				if(check >= from && check <= to){
					purchase_date_arr.push(purchase_date);
				}
				else{
					msg += "The Purchase date does not match between selected Financial year in vendor estimate-"+counter+"<br>";
				}
			});

			if(msg!=""){
				error_msg_alert(msg);
				$('#btn_save_estimate').prop('disabled',false);
				return false;
			}
			$.post(base_url+'view/vendor/dashboard/estimate/get_incentive_basic_amount.php', { booking_id : estimate_type_id,net_total_arr:net_total_arr,estimate_type:estimate_type}, function(data){ 

				var basic_cost_arr = JSON.parse(data);
				for(var i=0; i<basic_cost_arr.length; i++){
					var basic_amount = basic_cost_arr[0]['basic_amount'];
					var emp_id = basic_cost_arr[0]['emp_id'];
					var booking_date = basic_cost_arr[0]['booking_date'];
					
				}
				var incentive_arr = [];
				incentive_arr.push({
					estimate_type:estimate_type,
					booking_id : estimate_type_id,
					emp_id : emp_id,
					basic_amount : basic_amount,
					financial_year_id : financial_year_id,
					booking_date:booking_date
				});
				var incentive_arr = JSON.stringify(incentive_arr);
				$.ajax({
				type:'post',
				url: base_url+'controller/booker_incentive/incentive_save.php',
				data:{ incentive_arr : incentive_arr },
				success:function(result){
				}
				});
			});

			$('#btn_save_estimate').button('loading');
			$.ajax({
				type:'post',
				url: base_url+'controller/vendor/dashboard/estimate/vendor_estimate_save.php',
				data:{ estimate_type : estimate_type, estimate_type_id : estimate_type_id, vendor_type_arr : vendor_type_arr, vendor_type_id_arr : vendor_type_id_arr, basic_cost_arr : basic_cost_arr, non_recoverable_taxes_arr : non_recoverable_taxes_arr, service_charge_arr : service_charge_arr, other_charges_arr : other_charges_arr, service_tax_subtotal_arr : service_tax_subtotal_arr, discount_arr : discount_arr, our_commission_arr : our_commission_arr, tds_arr : tds_arr, net_total_arr : net_total_arr, roundoff_arr : roundoff_arr,remark_arr : remark_arr, invoice_id_arr : invoice_id_arr, payment_due_date_arr : payment_due_date_arr , invoice_url_arr : invoice_url_arr,purchase_date_arr : purchase_date_arr,reflection_arr:reflection_arr, branch_admin_id : branch_admin_id , emp_id : emp_id, reflections : reflections,currency_code:currency_code},
				success:function(result){
					$('#btn_save_estimate').prop('disabled',false);
					$('#btn_save_estimate').button('reset');
					var msg = result.split('--');
					if(msg[0]=='error'){
						error_msg_alert(msg[1]);
						return false;
					}else{
						$('#vi_confirm_box').vi_confirm_box({
							false_btn: false,
							message: result,
							true_btn_text: 'Ok',
							callback: function (data1) {
								if (data1 == 'yes') {
									window.location.href = '../index.php';
								}
							}
						});
					}
				}
			});
		}
	});
});
$('#estimate_save_modal').on('hidden.bs.modal', function(){
	reset_form('frm_vendor_estimate_save');
});
function dynamic_section_add(item, index,hotel_ids,amount,vendor_type=''){

	dynamic_estimate_count = parseInt(index);
	$.post('package_tour_sections.php', { item: item, index: dynamic_estimate_count ,vendor_type:vendor_type,amount:amount,hotel_ids:hotel_ids}, function(data){
		$('#div_dynamic_estimate').append(data);
		$('#dynamic_estimate_count').val(dynamic_estimate_count);
	});

}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<?php 
include_once('../../../layouts/fullwidth_app_footer.php');
?>