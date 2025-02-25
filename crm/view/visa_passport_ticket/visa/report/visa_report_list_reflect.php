<?php
include "../../../../model/model.php";
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
$visa_id = $_POST['visa_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';

$financial_year_id = $_SESSION['financial_year_id'];

$query = "select * from visa_master where financial_year_id ='$financial_year_id' and delete_status='0'";
if($customer_id!=""){
	$query .=" and customer_id='$customer_id'";
}
if($visa_id!=""){
	$query .=" and visa_id='$visa_id'";
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
include "../../../../model/app_settings/branchwise_filteration.php";
// $query .= " order by visa_id desc";
?>
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	
<table class="table table-bordered" id="tbl_visa_report" style="margin: 20px 0 !important;">
	<thead>
	    <tr class="table-heading-row">
	    	<th>S_No.</th>
			<th>Booking_ID</th>
			<th>Customer_Name</th>
			<th>Passenger_Name</th>
			<th>Birth_date</th>
			<th>Country</th>
			<th>Visa_Type</th>
			<th>Nationality </th>
	    </tr>
		
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$sq_visa = mysqlQuery($query);
		while($row_visa =mysqli_fetch_assoc($sq_visa)){


			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_visa[customer_id]'"));
			if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){
				$customer_name = $sq_customer['company_name'];
			}else{
				$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
			}
			$date = $row_visa['created_at'];
			$yr = explode("-", $date);
			$year = $yr[0];

			$sq_entry = mysqlQuery("select * from visa_master_entries where visa_id='$row_visa[visa_id]'");
			while($row_entry = mysqli_fetch_assoc($sq_entry)){

				$bg = ($row_entry['status']=="Cancel") ? "danger" : "";
				?>
				<tr class="<?= $bg ?>">
					<td><?= ++$count ?></td>
					<td><?= get_visa_booking_id($row_visa['visa_id'],$year) ?></td>
					<td><?= $customer_name ?></td>
					<td><?= $row_entry['first_name'].' '.$row_entry['last_name'] ?></td></td>
					<td><?= date('d-m-Y', strtotime($row_entry['birth_date'])) ?></td>
					<td><?= $row_entry['visa_country_name'] ?></td>
					<td><?= $row_entry['visa_type'] ?></td>
					<td><?= $row_entry['nationality'] ?></td>
				</tr>
				<?php
			}

		}
		?>
	</tbody>
</table>
</div> </div> </div>
<script>
	$('#tbl_visa_report').dataTable({
		"pagingType": "full_numbers",
		order: [[0, 'desc']],
	});
</script>