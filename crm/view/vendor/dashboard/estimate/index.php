<?php
include_once('../../../../model/model.php');
$role = $_SESSION['role'];
$emp_id= $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$q = "select * from branch_assign where link='vendor/dashboard/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<div class="row text-right mg_bt_10">
	<div class="col-xs-12">
    	<button class="btn btn-excel btn-sm pull-right" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
		<form action="estimate/estimate_save_modal.php" method="POST">
			<button class="btn btn-info btn-sm ico_left mg_bt_10"><i class="fa fa-plus"></i>&nbsp;&nbsp;Purchase Costing</button>&nbsp;&nbsp;
		</form>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
	
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="vendor_type2" id="vendor_type2" title="Supplier Type" onchange="vendor_type_data_load(this.value, 'div_vendor_type_content2', '2')">
				<option value="">Supplier Type</option>
				<?php 
				$sq_vendor = mysqlQuery("select * from vendor_type_master order by vendor_type");
				while($row_vendor = mysqli_fetch_assoc($sq_vendor)){
					?>
					<option value="<?= $row_vendor['vendor_type'] ?>"><?= $row_vendor['vendor_type'] ?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div id="div_vendor_type_content2"></div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="estimate_type2" id="estimate_type2" title="Purchase Type" onchange="payment_for_data_load(this.value, 'div_payment_for_content2', '2')">
				<option value="">Purchase Type</option>
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
		<div id="div_payment_for_content2"></div>
        <div class="col-md-3 col-sm-6">
            <select name="financial_year_id_filter" id="financial_year_id_filter" title="Select Financial Year">
                <?php
                $sq_fina = mysqli_fetch_assoc(mysqlQuery("select * from financial_year where financial_year_id='$financial_year_id'"));
                $financial_year = get_date_user($sq_fina['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($sq_fina['to_date']);
                ?>
                <option value="<?= $sq_fina['financial_year_id'] ?>"><?= $financial_year  ?></option>
                <?php echo get_financial_year_dropdown_filter($financial_year_id); ?>
            </select>
        </div>
    </div>
	<div class="row mg_tp_10">
		<div class="col-md-3 col-sm-6 col-xs-12">
			<button class="btn btn-sm btn-info ico_right" onclick="vendor_estimate_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</div>
<div id="div_quotation_list_reflect" class="main_block loader_parent">
	<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
		<table id="estimate" class="table table-hover" style="margin: 20px 0 !important;">         
		</table>
	</div></div></div>
</div>
<div id="div_vendor_estimate_list" class="main_block loader_parent"></div>
<div id="div_vendor_estimate_update"></div>
<div id="div_vendor_payment_content"></div>
<style>
.action_width{
	width : 250px;
	padding : 0;
}
</style>

<script src="js/calculation.js"></script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
var column = [
	{ title : "S_No."},
	{ title:"Purchase_Date"},
	{ title:"Purchase_Type"},
	{ title : "Purchase_ID"},
	{ title : "Supplier_Type"},
	{ title : "Supplier_Name"},
	{ title : "Remark"},
	{ title : "Amount", className:"Info"},
	{ title : "cncl_Amount", className:"action_width danger"},
	{ title : "Total_Amount", className:"action_width Info"},
	{ title : "Created_by"},
	{ title : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Actions&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", className : "text-center"}
];

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
function estimate_save_modal(){

	$.post('estimate/estimate_save_modal.php', {}, function(data){
		console.log(data);
		$('#div_vendor_estimate_save').html(data);
	});
}

function vendor_estimate_list_reflect()
{
	$('#div_vendor_estimate_list').append('<div class="loader"></div>');
	var estimate_type = $('#estimate_type2').val();
	var vendor_type = $('#vendor_type2').val();
	var branch_status = $('#branch_status').val();
	var estimate_type_id = get_estimate_type_id('estimate_type2', '2');
	var vendor_type_id = get_vendor_type_id('vendor_type2', '2');
    var financial_year_id_filter = $('#financial_year_id_filter').val();

	$.post('estimate/vendor_estimate_list_reflect.php', { estimate_type : estimate_type, estimate_type_id : estimate_type_id, vendor_type : vendor_type, vendor_type_id : vendor_type_id , branch_status : branch_status,financial_year_id:financial_year_id_filter}, function(data){
		pagination_load(data, column, true, true, 20, 'estimate',true);
		$('.loader').remove();
	});
}
vendor_estimate_list_reflect();

function vendor_estimate_update_modal(estimate_id)
{
    $('#update_btn-'+estimate_id).button('loading');
    $('#update_btn-'+estimate_id).prop('disabled',true);
	$.post('estimate/vendor_estimate_update_modal.php', { estimate_id : estimate_id }, function(data){
		$('#div_vendor_estimate_update').html(data);
		vendor_estimate_list_reflect();
		$('#update_btn-'+estimate_id).button('reset');
		$('#update_btn-'+estimate_id).prop('disabled',false);
	});
}
function vendor_payment_modal(estimate_id)
{
	$.post('payment/vendor_payment_modal.php', { estimate_id : estimate_id }, function(data){
		$('#div_vendor_payment_content').html(data);
	});
}
function vendor_estimate_cancel(estimate_id)
{
	$('#vi_confirm_box').vi_confirm_box({
	message: 'Are you sure?',
		callback: function(data1){
		if(data1=="yes"){
			$.ajax({
				type: 'post',
				url: base_url()+'controller/vendor/dashboard/estimate/cancel_estimate.php',
				data:{ estimate_id : estimate_id },
				success: function(result){
				msg_alert(result);
				vendor_estimate_list_reflect();
				}
			});
		}
		}
	});
}

function excel_report()
{
	var estimate_type = $('#estimate_type2').val();
	var vendor_type = $('#vendor_type2').val();
	var vendor_type_id = get_vendor_type_id('vendor_type2', '2');
	var estimate_type_id = get_estimate_type_id('estimate_type2', '2');
	var branch_status = $('#branch_status').val();

	window.location = 'estimate/excel_report.php?estimate_type='+estimate_type+'&vendor_type='+vendor_type+'&vendor_type_id='+vendor_type_id+'&estimate_type_id='+estimate_type_id+'&branch_status='+branch_status;


}
function purchase_delete_entry(estimate_id)
{
	$('#vi_confirm_box').vi_confirm_box({
		callback: function(data1){
			if(data1=="yes"){
				var branch_status = $('#branch_status').val();
				var base_url = $('#base_url').val();
				$.post(base_url+'controller/vendor/dashboard/estimate/vendor_estimate_delete.php',{ estimate_id : estimate_id }, function(data){
					success_msg_alert(data);
					vendor_estimate_list_reflect();
				});
			}
		}
	});
}
$('#estimate_save_modal').on('shown.bs.modal', function(){
	$('input[name=service_tax_subtotal_s]').val('');
});
$(function () {
    $("[data-toggle='tooltip']").tooltip({placement: 'bottom'});
});
</script>