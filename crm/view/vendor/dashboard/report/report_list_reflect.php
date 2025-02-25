<?php
include_once('../../../../model/model.php');

include_once('../../inc/vendor_generic_functions.php');
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_status = $_POST['branch_status']; 
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$estimate_type = isset($_POST['estimate_type']) ? $_POST['estimate_type'] : '';
$vendor_type = isset($_POST['vendor_type']) ? $_POST['vendor_type'] : '';
$estimate_type_id = isset($_POST['estimate_type_id']) ? $_POST['estimate_type_id'] : '';
$vendor_type_id = isset($_POST['vendor_type_id']) ? $_POST['vendor_type_id'] : '';
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$array_s = array();
$temp_arr = array();

$query = "select estimate_type, estimate_type_id, vendor_type, vendor_type_id,status, purchase_date as date, net_total as credit, '' as debit,purchase_return as purchase_return1,cancel_estimate as cancel_estimate1,estimate_id from vendor_estimate where financial_year_id='$financial_year_id' and delete_status='0' and status!='Cancel'";
if($estimate_type!=""){
	$query .= " and estimate_type='$estimate_type' ";
}
if($vendor_type!=""){
	$query .= " and vendor_type='$vendor_type' ";
}
if($estimate_type_id!=""){
	$query .= " and estimate_type_id='$estimate_type_id' ";
}
if($vendor_type_id!=""){
	$query .= " and vendor_type_id='$vendor_type_id' ";
}
$side = 'Cr';
$opening_bal = 0;
if($vendor_type!="" && $vendor_type_id!=""){
	$data = get_opening_bal($vendor_type , $vendor_type_id);
	$opening_bal = $data['opening_balance'];
	$side = $data['side'];
}
if($from_date!="" && $to_date!=""){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .= " and purchase_date between '$from_date' and '$to_date'";
}

include "../../../../model/app_settings/branchwise_filteration.php";
$query .= " union all ";

$query .= "select estimate_type, estimate_type_id, vendor_type, vendor_type_id,'' as status, payment_date as date1, '' as credit1, payment_amount as debit1,'' as purchase_return2,'' as cancel_estimate2,estimate_id from vendor_payment_master where clearance_status!='Pending' AND clearance_status!='Cancelled' and financial_year_id='$financial_year_id' and delete_status='0' and payment_amount!='0'";
if($vendor_type!=""){
	$query .= " and vendor_type='$vendor_type' ";
}
if($vendor_type_id!=""){
	$query .= " and vendor_type_id='$vendor_type_id' ";
} 
if($estimate_type!=""){
	$query .= " and estimate_type='$estimate_type'";
}
if($estimate_type_id!=""){
	$query .= " and estimate_type_id='$estimate_type_id'";
}
if($from_date!="" && $to_date!=""){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .= " and payment_date between '$from_date' and '$to_date'";
}
include "../../../../model/app_settings/branchwise_filteration.php";

$sq_estimate = mysqlQuery($query);
$total_estimate_amt = 0;
$total_paid_amt = 0;
$count = 0;
$total_arr = array (0 => array("data" => array("","","","","","","","<strong>Opening_Balance(".$side.")</strong>", number_format($opening_bal,2)), "bg" => "warning"));
while($row_report = mysqli_fetch_assoc($sq_estimate)){

	$bg = ($row_report['status'] == 'Cancel') ? 'danger' : '';
	if($row_report['purchase_return1'] == 0){
		$actual_purchase = $row_report['credit'];
	}
	else if($row_report['purchase_return1'] == 2){
		$cancel_estimate = json_decode($row_report['cancel_estimate1']);
		$p_purchase = ($row_report['credit'] - floatval($cancel_estimate[0]->net_total));
		$actual_purchase = $p_purchase;
	}else{
		$actual_purchase = 0;
	}

	$total_estimate_amt = $total_estimate_amt + floatval($actual_purchase);
	$vendor_type_val = get_vendor_name($row_report['vendor_type'], $row_report['vendor_type_id']);

	if($side == 'Cr'){
		$total_amount = ($total_estimate_amt);
	}else{
		$total_amount = ($total_estimate_amt);
	}
	if($row_report['debit1'] != ''){
		$sq_pay1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_payment_master where estimate_id='$row_report[estimate_id]'"));
		$estimate_type_val = get_estimate_type_name($sq_pay1['estimate_type'], $sq_pay1['estimate_type_id']);
		$estimate_type = $sq_pay1['estimate_type']; 
	}
	else{
		$estimate_type_val = get_estimate_type_name($row_report['estimate_type'], $row_report['estimate_type_id']);
		$estimate_type = $row_report['estimate_type'];
	}
	$total_paid_amt += floatval($row_report['debit']);
	if($total_paid_amt==""){ $total_paid_amt = 0; }
	
	$vendor_type_val = get_vendor_name($row_report['vendor_type'], $row_report['vendor_type_id']);
	$estimate_type_val = get_estimate_type_name($row_report['estimate_type'], $row_report['estimate_type_id']);
	$date = $row_report['date'];
	$yr = explode("-", $date);
	$year = $yr[0];
	$estimate_id = get_vendor_estimate_id($row_report['estimate_id'],$year)." : ".$vendor_type_val."(".$row_report['vendor_type'].") : ".$estimate_type_val;

	$temp_arr = array( "data" => array(
		(int)(++$count),
		$estimate_id,
		($estimate_type == '') ?'NA': $estimate_type,
		($estimate_type_val == '') ? 'NA' : $estimate_type_val,
		$row_report['vendor_type'],
		$vendor_type_val,
		date('d-m-Y', strtotime($row_report['date'])),
		number_format(floatval($actual_purchase),2),
		$row_report['debit']
		), "bg" =>$bg);
	array_push($total_arr,$temp_arr); 
}
if($total_estimate_amt >= $total_paid_amt){
	$side1='(Cr)';
}
else {	
	$side1='(Dr)';
}
if($side == 'Credit'){
	$total_amount = $total_amount + $opening_bal - $total_paid_amt;
}else{
	$total_amount = $total_amount - $opening_bal - $total_paid_amt;
}
if($total_amount <= 0) {
	$total_amount = ($total_amount) - ($total_amount) - ($total_amount);
}

$footer_data = array("footer_data" => array(
	'total_footers' => 3,

	'foot0' => "Total Costing : ".number_format($total_estimate_amt, 2),
	'col0' => 4,
	'class0' => "text-right info",
	
	'foot1' => "Total Paid : ".number_format($total_paid_amt, 2),
	'col1' => 2,
	'class1' => "text-right success",

	'foot2' => "Closing Balance :".number_format($total_amount, 2),
	'col2' => 3,
	'class2' => "text-right warning"
));
array_push($total_arr, $footer_data);
echo json_encode($total_arr);
?>
