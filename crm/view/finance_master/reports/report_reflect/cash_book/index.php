<?php
include "../../../../../model/model.php";
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

<div class="row text-right mg_bt_10">
	<div class="col-md-12">
      <button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3">
			<input type="text" name="from_date_filter" id="from_date_filter" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date_filter');">
		</div>
		<div class="col-md-3">
			<input type="text" name="to_date_filter" id="to_date_filter" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date_filter','to_date_filter')">
		</div>		
		<div class="col-md-3">
			<button class="btn btn-sm btn-info ico_right" onclick="report_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</div>

<div id="div_report" class="main_block">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="cash_book" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div>
</div>

<script type="text/javascript">
$('#from_date_filter, #to_date_filter').datetimepicker({ timepicker:false,format:'d-m-Y' });
var column = [	
{ title : "S_No."},
	{ title : "Booking_For"},
	{ title : "Payment_ID"},
	{ title : "Collected_By"},
	{ title : "Amount"},
	{ title : "Payment_Date"},
	{ title : "Particular"},
	{ title : "Debit", className: "danger"},
	{ title : "Credit", className : "text-left success"},
	{ title : "Balance", className : "warning"},
];
function report_reflect()
{
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();

	$.post('report_reflect/cash_book/report_reflect.php', { from_date : from_date, to_date : to_date,branch_status : branch_status, role : role,branch_admin_id : branch_admin_id }, function(data){
	    var table = pagination_load(data, column, true, true, 20, 'cash_book');
		$('.loader').remove();
	});
}
report_reflect();

function excel_report()
{
    var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();

    window.location = 'report_reflect/cash_book/excel_report.php?from_date='+from_date+'&to_date='+to_date+'&branch_status='+branch_status+'&branch_admin_id='+branch_admin_id+'&role='+role;
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>