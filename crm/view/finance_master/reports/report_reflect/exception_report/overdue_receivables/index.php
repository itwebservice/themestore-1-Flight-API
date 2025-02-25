<?php
include "../../../../../../model/model.php";
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
		<button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3 col-sm-4 col-xs-12">
           <select class="form-control" id="party_name" name="party_name" onchange="report_reflect_r()" style="width: 100%" title="Select Customer" >
              <?php get_customer_dropdown($role,$branch_admin_id,$branch_status); ?>
          </select>
        </div>
		<div class="col-md-9 text-right">
			
		</div>
	</div>
</div>

<hr>

<!-- <div id="div_report1" class="main_block loader_parent"></div> -->
<div class="row mg_tp_10 main_block">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="pay_age" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div></div>
<script type="text/javascript">
$('#party_name').select2();
var column = [
{ title : "S_No."},
{ title:"Customer_Name"},
{ title:"Booking_type"},
{ title : "Overdue_amount", className: "info text-right"},
{ title : "Overdue_from"}
];
function report_reflect_r()
{
	$('#div_report1').append('<div class="loader"></div>');
	var base_url = $('#base_url').val();
	var party_name = $('#party_name').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();
	$.post(base_url+'view/finance_master/reports/report_reflect/exception_report/overdue_receivables/get_customer_overdue.php',{ party_name : party_name, branch_status : branch_status , role : role,branch_admin_id : branch_admin_id}, function(data){
	// console.log(data);
	pagination_load(data, column, true, true, 20, 'pay_age');
	});
}
report_reflect_r();
function excel_report()
{
	var party_name = $('#party_name').val();
	var branch_status = $('#branch_status').val();
	var branch_admin_id = $('#branch_admin_id').val();
	var role = $('#role').val();
  window.location = 'report_reflect/exception_report/overdue_receivables/excel_report.php?party_name='+party_name+'&branch_status='+branch_status+'&branch_admin_id='+branch_admin_id+'&role='+role;
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>