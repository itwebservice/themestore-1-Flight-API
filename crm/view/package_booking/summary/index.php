<?php 
include "../../../model/model.php";
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='package_booking/booking/index.php'"));
$branch_status_r = $sq['branch_status'];
?>
<input type="hidden" id="branch_status_r" name="branch_status_r" value="<?= $branch_status_r ?>" >
<div class="row mg_bt_10">
	<div class="col-md-12 text-right">
		<button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>
<div class="app_panel_content Filter-panel">
<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="cust_type_filter" id="cust_type_filter" style="width: 100%" onchange="dynamic_customer_load(this.value,'company_filter'); company_name_reflect();" title="Customer Type">
				<?php get_customer_type_dropdown(); ?>
			</select>
	    </div>
	    <div id="company_div" class="hidden mg_bt_10">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" id="customer_div">    
	    </div> 
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="package_id_filter" id="package_id_filter" title="Booking ID" style="width: 100%">
		    <?php   get_package_booking_dropdown($role, $branch_admin_id, $branch_status_r,$emp_id); ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="from_date" name="from_date" class="form-control" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date');">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="to_date" name="to_date" class="form-control" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date','to_date');">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="booker_id_filter" id="booker_id_filter" title="User Name" style="width: 100%">
		        <?php  get_user_dropdown($role, $branch_admin_id, $branch_status_r,$emp_id) ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="branch_id_filter" id="branch_id_filter" title="Branch Name" style="width: 100%">
		        <?php get_branch_dropdown($role, $branch_admin_id, $branch_status_r)  ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<button class="btn btn-sm btn-info ico_right" onclick="list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
 </div>	
	

</div>	
<div id="div_list" class="main_block">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="package_tour_report" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div>
</div>
<div id="div_package_content_display"></div>

<script>
$('#customer_id_filter, #package_id_filter, #cust_type_filter,#booker_id_filter,#branch_id_filter').select2();
$('#from_date,#to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
dynamic_customer_load('','');

var column = [
	{ title : "S_No."},
	{ title:"Booking_ID"},
	{ title : "Customer_Name"},
	{ title : "Mobile"},
	{ title : "EMAIL_ID"},
	{ title : "Total_Pax"},
	{ title : "Booking_Date"},
	{ title : "View"},
	{ title : "Tour_name"},
	{ title : "Tour_date&nbsp;&nbsp;&nbsp;"},
	{ title : "Basic_amount&nbsp;&nbsp;&nbsp;", className : "info"},
	{ title : "Service_charge", className:"info text-right"},
	{ title : "tax&nbsp;&nbsp;&nbsp;", className : "info"},
	{ title : "TCS&nbsp;&nbsp;&nbsp;", className : "info"},
	{ title : "TDS&nbsp;&nbsp;&nbsp;", className : "info"},
	{ title : "Credit_card_charges", className:"info text-right"},
	{ title : "Sale", className:"info text-right"},
	{ title : "Cancel", className:"danger text-right"},
	{ title : "Total", className:"info text-right"},
	{ title : "Paid", className:"success text-right"},
	{ title : "View"},
	{ title : "Outstanding_Balance", className:"warning text-right"},
	{ title : "Due_Date"},
	{ title : "Purchase"},
	{ title : "Purchased_From"},
	{ title : "Branch"},
	{ title : "Booked_By"},
	{ title : "Incentive" }
	
];
function list_reflect()
{
	var customer_id = $('#customer_id_filter').val();
	var booking_id = $('#package_id_filter').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var cust_type = $('#cust_type_filter').val();
	var company_name = $('#company_filter').val();
	var booker_id = $('#booker_id_filter').val();
	var branch_id = $('#branch_id_filter').val();
	var base_url = $('#base_url').val();
	var branch_status_r = $('#branch_status_r').val();
	$.post(base_url+'view/package_booking/summary/list_reflect.php', { customer_id : customer_id, booking_id : booking_id, from_date : from_date, to_date : to_date, cust_type : cust_type, company_name : company_name,booker_id:booker_id,branch_id : branch_id , branch_status : branch_status_r}, function(data){
		pagination_load(data, column, true, true, 20, 'package_tour_report',true);
	});
}
list_reflect();

function excel_report()
{
	var customer_id = $('#customer_id_filter').val()
	var booking_id = $('#package_id_filter').val()
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var cust_type = $('#cust_type_filter').val();
	var company_name = $('#company_filter').val();
	var booker_id = $('#booker_id_filter').val();
	var branch_id = $('#branch_id_filter').val();
	var base_url = $('#base_url').val();
	var branch_status_r = $('#branch_status_r').val();
	window.location = base_url+'view/package_booking/summary/excel_report.php?customer_id='+customer_id+'&booking_id='+booking_id+'&from_date='+from_date+'&to_date='+to_date+'&cust_type='+cust_type+'&company_name='+company_name+'&booker_id='+booker_id+'&branch_id='+branch_id+'&branch_status='+branch_status_r;
}
//*******************Get Dynamic Customer Name Dropdown**********************//
function dynamic_customer_load(cust_type, company_name)
{
  var cust_type = $('#cust_type_filter').val();
  var company_name = $('#company_filter').val();
  var branch_status_r = $('#branch_status_r').val();
  var base_url = $('#base_url').val();
    $.get(base_url+"view/package_booking/booking/inc/get_customer_dropdown.php", { cust_type : cust_type , company_name : company_name, branch_status : branch_status_r}, function(data){
    $('#customer_div').html(data);
  });   
}
function company_name_reflect()
{  
	var cust_type = $('#cust_type_filter').val();
    var base_url = $('#base_url').val();
     var branch_status_r = $('#branch_status_r').val();
  	$.post(base_url+'view/package_booking/booking/company_name_load.php', { cust_type : cust_type, branch_status : branch_status_r }, function(data){
  		if(cust_type=='Corporate'||cust_type=='B2B'){
	  		$('#company_div').addClass('company_class');	
	    }
	    else
	    {
	    	$('#company_div').removeClass('company_class');		
	    }
	    $('#company_div').html(data);
    });
}

function customer_booking_dropdown_load()
{
	var customer_id = $('#customer_id_filter').val();
	var base_url = $('#base_url').val();
	$.post(base_url+'view/package_booking/booking/inc/customer_booking_dropdown_load.php', { customer_id : customer_id }, function(data){
        $('#booking_id_filter').html(data);
    });
}
function package_view_modal(booking_id)
{
	$('#packagev_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#packagev_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/index.php', { booking_id : booking_id }, function(data){
		$('#div_package_content_display').html(data);
		$('#packagev_btn-'+booking_id).prop('disabled',false);
    	$('#packagev_btn-'+booking_id).button('reset');
	});
}
function supplier_view_modal(booking_id)
{
	$('#supplierv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#supplierv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/supplier_view_modal.php', { booking_id : booking_id }, function(data){
		$('#div_package_content_display').html(data);
		$('#supplierv_btn-'+booking_id).prop('disabled',false);
    	$('#supplierv_btn-'+booking_id).button('reset');
	});
}
function payment_view_modal(booking_id)
{
	$('#paymentv_btn-'+booking_id).prop('disabled',true);
	var base_url = $('#base_url').val();
    $('#paymentv_btn-'+booking_id).button('loading');
	$.post(base_url+'view/package_booking/summary/view/payment_view_modal.php', { booking_id : booking_id }, function(data){	
		$('#div_package_content_display').html(data);
		$('#paymentv_btn-'+booking_id).prop('disabled',false);
    	$('#paymentv_btn-'+booking_id).button('reset');
	});
}
$(function () {
    $("[data-toggle='tooltip']").tooltip({placement: 'bottom'});
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>