<?php
include "../../../model/model.php";

$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$exc_id = $_POST['exc_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_status = $_POST['branch_status'];

$query = "select * from excursion_master where financial_year_id='$financial_year_id' and delete_status='0' ";
if($customer_id!=""){
	$query .=" and customer_id='$customer_id'";
}
if($exc_id!=""){
	$query .=" and exc_id='$exc_id'";
}
if($from_date!="" && $to_date!=""){
	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date' and '$to_date'";
}
if($cust_type != ""){
	$query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
if($company_name != ""){
	$query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
include "../../../model/app_settings/branchwise_filteration.php";
// $query .= " order by exc_id desc";
?>
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	
<table class="table table-bordered" id="tbl_exc_report" style="margin: 20px 0 !important;">
	<thead>
	    <tr class="table-heading-row">
	    	<th>S_No.</th>
			<th>Booking_ID</th>
			<th>Customer_Name</th>
			<th>Activity_datetime</th>
			<th>City_name</th>
			<th>Activity_name</th>
			<th>transfer_option</th>
			<th>total_guest</th>
			<th>amount</th>
	    </tr>
		
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$total = 0;
		$sq_exc = mysqlQuery($query);
		while($row_exc = mysqli_fetch_assoc($sq_exc)){			
			$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_exc[customer_id]'"));
			if($sq_customer_info['type']=='Corporate'||$sq_customer_info['type'] == 'B2B'){
				$customer_name = $sq_customer_info['company_name'];
			}else{
				$customer_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
			}
			$date = $row_exc['created_at'];
			$yr = explode("-", $date);
			$year =$yr[0];
			$sq_entry = mysqlQuery("select * from excursion_master_entries where exc_id='$row_exc[exc_id]'");
			while($row_entry = mysqli_fetch_assoc($sq_entry)){

				$bg = ($row_entry['status']=="Cancel") ? "danger" : "";
       			$sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_entry[city_id]'"));
				$sq_exc1 = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_entry[exc_name]'"));    
					?>
					<tr class="<?= $bg ?>">
						<td><?php echo ++$count; ?></td>
						<td><?php echo get_exc_booking_id($row_exc['exc_id'],$year); ?> </td>
						<td><?php echo $customer_name; ?> </td>
						<td><?php echo get_datetime_user($row_entry['exc_date']); ?> </td>
						<td><?php echo $sq_city['city_name']; ?> </td>
						<td><?php echo $sq_exc1['excursion_name']; ?> </td>
						<td><?php echo $row_entry['transfer_option']; ?> </td>
						<td><?php echo $row_entry['total_adult'] + $row_entry['total_child'] + $row_entry['total_infant']; ?></td>
						<td><?php echo $row_entry['total_cost']; ?> </td>
					</tr>
				<?php
			    }  ?>
			<?php
			}
			?>
	</tbody>
</table>
</div> </div> </div>
<script>
	$('#tbl_exc_report').dataTable({
		"pagingType": "full_numbers",
		order: [[0, 'desc']],
	});
</script>