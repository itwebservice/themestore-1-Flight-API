<?php
include "../../../../model/model.php";
$branch_status = $_POST['branch_status'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$emp_id = $_SESSION['emp_id'];
?>
<div class="row mg_bt_20">
	<div class="col-md-12 text-right">
	   <button class="btn btn-excel btn-sm pull-right" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>
	</div>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="cust_type_filter" id="cust_type_filter" style="width:100%" onchange="dynamic_customer_load(this.value,'company_filter');company_name_reflect();" title="Customer Type">
				<?php get_customer_type_dropdown(); ?>
			</select>
	    </div>
	    <div id="company_div" class="hidden">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" id="customer_div">    
        </div> 
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="visa_id_filter" id="visa_id_filter" style="width:100%" title="Booking ID">
		        <option value="">Booking ID</option>
		        <?php 
		        $query = "select * from visa_master where 1 and delete_status='0'";
	            include "../../../../model/app_settings/branchwise_filteration.php";
	            $query .= " order by visa_id desc";
	            $sq_visa = mysqlQuery($query);
		        while($row_visa = mysqli_fetch_assoc($sq_visa)){

		        $date = $row_visa['created_at'];
				      $yr = explode("-", $date);
				      $year =$yr[0];
		          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_visa[customer_id]'"));
				  if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){
					$customer_name = $sq_customer['company_name'];
					}else{
						$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
					}
		          ?>
		          <option value="<?= $row_visa['visa_id'] ?>"><?= get_visa_booking_id($row_visa['visa_id'],$year).' : '.$customer_name ?></option>
		          <?php
		        }
		        ?>
		    </select>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="from_date" name="from_date" class="form-control" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date');">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<input type="text" id="to_date" name="to_date" class="form-control" onchange="validate_validDate('from_date','to_date');" placeholder="To Date" title="To Date">
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<button class="btn btn-sm btn-info ico_right" onclick="visa_report_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</div>
<div id="div_visa_report_list" class="main_block loader_parent"></div>

<?php //include('filter.php') ?>

<script>	
	if (typeof dynamic_customer_load === 'function') {
		dynamic_customer_load('','');
	}

	$('#from_date, #to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
	$('#customer_id_filter, #visa_id_filter, #cust_type_filter').select2();
	function visa_report_list_reflect()
	{
		$('#div_visa_report_list').append('<div class="loader"></div>');
		var customer_id = $('#customer_id_filter').val();
		var visa_id = $('#visa_id_filter').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var cust_type = $('#cust_type_filter').val();
		var company_name = $('#company_filter').val();
		var branch_status = $('#branch_status').val();
		$.post(base_url()+'view/visa_passport_ticket/visa/report/visa_report_list_reflect.php', { customer_id : customer_id, visa_id : visa_id, from_date : from_date, to_date : to_date, cust_type : cust_type, company_name : company_name, branch_status : branch_status  }, function(data){
			$('#div_visa_report_list').html(data);
		});
	}
	visa_report_list_reflect();
	
	function excel_report()
	{
		var customer_id = $('#customer_id_filter').val();
		var visa_id = $('#visa_id_filter').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var cust_type = $('#cust_type_filter').val();
		var company_name = $('#company_filter').val();
		var branch_status = $('#branch_status').val();
		window.location = base_url()+'view/visa_passport_ticket/visa/report/excel_report.php?customer_id='+customer_id+'&visa_id='+visa_id+'&from_date='+from_date+'&to_date='+to_date+'&cust_type='+cust_type+'&company_name='+company_name+'&branch_status='+branch_status;
	}
	
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>