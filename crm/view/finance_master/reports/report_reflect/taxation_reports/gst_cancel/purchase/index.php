<?php include "../../../../../../../model/model.php"; 

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='finance_master/reports/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" name="branch_status" value="<?= $branch_status ?>" id="branch_status">
<input type="hidden" name="role" value="<?= $role ?>" id="role">
<input type="hidden" name="branch_admin_id" value="<?= $branch_admin_id ?>" id="branch_admin_id">

<div class="row mg_bt_10">
	<div class="col-xs-12 text-right">
		<button class="btn btn-excel btn-sm pull-right" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-2 mg_bt_10_xs">
			<input type="text" name="from_date_filter" id="from_date_filter" placeholder="From Date" title="From Date"onchange="get_to_date(this.id,'to_date_filter')">
		</div>
		<div class="col-md-2 mg_bt_10_xs">
			<input type="text" name="to_date_filter" id="to_date_filter" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date_filter','to_date_filter')">
		</div>
		<div class="col-md-3">
			<button class="btn btn-sm btn-info ico_right" onclick="report_reflect_itc()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>	
	</div>
</div>

<hr>

<!-- <div id="div_report_itc" class="main_block loader_parent"></div> -->
<div class="row mg_tp_10 main_block">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="purchase_cancel" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div></div>
<script type="text/javascript">
$('#from_date_filter, #to_date_filter').datetimepicker({ timepicker:false, format:'d-m-Y' });
var column = [
{ title : "S_No."},
{ title:"Service_Name"},
{ title : "SAC/HSN_Code"},
{ title : "Supplier_Name"},
{ title : "Tax_No"},
{ title : "Account_State"},
{ title:"Purchase_ID"},
{ title : "Purchase_Date"},
{ title : "Type_of_Supplies"},
{ title : "Place_of_Supply"},
{ title : "Net_Amount"},
{ title : "Taxable_Amount"},
{ title : "Tax_%"},
{ title : "Tax_Amount"},
{ title : "Cess%"},
{ title : "Cess_Amount"},
{ title : "ITC_Eligibility"},
{ title : "Reverse_Charge"}
];
function report_reflect_itc()
{
	$('#div_report_itc').append('<div class="loader"></div>');
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();

	$.post('report_reflect/taxation_reports/gst_cancel/purchase/report_reflect.php',{ from_date : from_date, to_date : to_date , branch_status : branch_status  , role : role,branch_admin_id : branch_admin_id   }, function(data){
		pagination_load(data, column, true, true, 20, 'purchase_cancel');
	});
}
report_reflect_itc();

 function excel_report()
{
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val(); 
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();
	window.location = 'report_reflect/taxation_reports/gst_cancel/purchase/excel_report.php?from_date='+from_date+'&to_date='+to_date+'&branch_status='+branch_status+'&branch_admin_id='+branch_admin_id+'&role='+role;
}
</script>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>