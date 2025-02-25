<?php
include "../../../../model/model.php";
global $currency;
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$booking_id = $_POST['booking_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_POST['financial_year_id'];
$branch_status = $_POST['branch_status'];

$query = "select * from hotel_booking_master where financial_year_id='$financial_year_id' and delete_status='0' ";
if($customer_id!=""){
	$query .=" and customer_id='$customer_id'";
}
if($booking_id!=""){
	$query .=" and booking_id='$booking_id'";
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
if($role == "B2b"){
	$query .= " and emp_id='$emp_id'";
}
include "../../../../model/app_settings/branchwise_filteration.php";
$query .= " order by booking_id desc";
$total_sale = 0;
$total_cancelation_amount = 0;
$total_balance = 0;
$available_bal=0;
$pending_bal=0;
$array_s = array();
$temp_arr = array();
$footer_data = array();
$sq_booking = mysqlQuery($query);
while($row_booking = mysqli_fetch_assoc($sq_booking)){

	$sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_booking[emp_id]'"));
	$emp_name = ($row_booking['emp_id'] != 0) ? $sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

	$pass_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]' and status='Cancel'"));
	if($pass_count == $cancel_count){
		$bg="danger";
		$update_btn = '';
		$delete_btn = '';
		$serv_voucher = '';
	}
	else {
		$bg="";
		$update_btn = '<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="booking_update_modal('.$row_booking['booking_id'] .')" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>';
		$delete_btn = '<button class="'.$delete_flag.' btn btn-danger btn-sm" onclick="delete_entry('.$row_booking['booking_id'].')" title="Delete Entry"><i class="fa fa-trash"></i></button>';
		$serv_voucher = '<button data-toggle="tooltip" title="Download Service Voucher" class="btn btn-info btn-sm" onclick="voucher_display('.$row_booking['booking_id'] .')" id="edith-'.$row_booking['booking_id'] .'" title="Update Details"><i class="fa fa-print"></i></button>';
	}
	$date = $row_booking['created_at'];
	$yr = explode("-", $date);
	$year =$yr[0];

	$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
	if($sq_customer['type']=='Corporate' || $sq_customer['type'] == 'B2B'){
		$customer_name = $sq_customer['company_name'];
	}else{
		$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
	}

	$sq_payment_total = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from hotel_booking_payment where booking_id='$row_booking[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

	$sq_hotel_info = mysqli_fetch_assoc(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));

	$sq_credit = mysqli_fetch_assoc(mysqlQuery("SELECT sum(`credit_charges`) as sumc FROM `hotel_booking_payment` WHERE `booking_id`='$row_booking[booking_id]' and clearance_status != 'Pending' and `clearance_status`!='Cancelled'"));
	$credit_card_charges = $sq_credit['sumc'];
	$sale_bal = $row_booking['total_fee'] - $row_booking['cancel_amount'];
	$paid_amount = $sq_payment_total['sum'];
	$total_bal = $sale_bal - $paid_amount;
	
	if($paid_amount==""){ $paid_amount = 0; }
	$tot_amt=$row_booking['total_fee']+ $credit_card_charges;
	$sale_amount=$tot_amt-$row_booking['cancel_amount'];

	$canc_amount=$row_booking['cancel_amount'];
	if($canc_amount=="") {$canc_amount = 0; }

	$total_amount1 = $tot_amt - $canc_amount;

	$total_cancelation_amount = $total_cancelation_amount+$canc_amount;
	$total_balance = $total_balance + $sale_amount ;

	$invoice_no = get_hotel_booking_id($row_booking['booking_id'],$year);
	$booking_id = $row_booking['booking_id'];
	$invoice_date = date('d-m-Y',strtotime($row_booking['created_at']));
	$customer_id = $row_booking['customer_id'];
	$service_name = "Hotel Invoice";
	//**Service Tax
	$service_tax = $row_booking['service_tax_subtotal'];
	//**Basic Cost
	$basic_cost = $row_booking['sub_total'];
	$service_charge = $row_booking['service_charge'];
	//**Net Amount
	$hotel_total_cost=$row_booking['total_fee'] + $credit_card_charges;
	$net_amount = $hotel_total_cost;
	$sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='Hotel / Accommodation'"));   
	$sac_code = $sq_sac['hsn_sac_code'];

	if($app_invoice_format == 4)
	$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/tax_invoice_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&net_amount=$net_amount&service_charge=$service_charge&total_paid=$paid_amount&balance_amount=$total_bal&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&pass_count=$pass_count&credit_card_charges=$credit_card_charges";
	else
	$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/hotel_body_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&net_amount=$net_amount&service_charge=$service_charge&total_paid=$paid_amount&balance_amount=$total_bal&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&credit_card_charges=$credit_card_charges&canc_amount=$canc_amount&bg=$bg";
	$total_sale = $total_sale+$hotel_total_cost;

	// Currency conversion
	$currency_amount1 = currency_conversion($currency,$row_booking['currency_code'],$total_amount1);
	if($row_booking['currency_code'] !='0' && $currency != $row_booking['currency_code']){
		$currency_amount = ' ('.$currency_amount1.')';
	}else{
		$currency_amount = '';
	}
	$pass_name = (($sq_customer['type']=='Corporate' || $sq_customer['type'] == 'B2B') && $row_booking['pass_name'] != '') ? ' ('.$row_booking['pass_name'].')' : '';
	
	$temp_arr = array( "data" => array(
		$row_booking['invoice_pr_id'],
		get_hotel_booking_id($row_booking['booking_id'],$year),
		$customer_name.$pass_name,
		number_format($row_booking['total_fee']+$credit_card_charges,2),
		$canc_amount,
		number_format($total_amount1, 2).'<br/>'.$currency_amount,
		$emp_name,
		$invoice_date,
		'<a onclick="loadOtherPage(\''. $url1 .'\')" class="btn btn-info btn-sm" title="Download Invoice"><i class="fa fa-print"></i></a>

		'.$serv_voucher.'

		'.$update_btn.'

		<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="booking_display_modal('. $row_booking['booking_id'] .')" title="View Details" id="view-'.$row_booking['booking_id'] .'"><i class="fa fa-eye"></i></button>'.$delete_btn
	), "bg" =>$bg);
	array_push($array_s,$temp_arr); 
}
$footer_data = array("footer_data" => array(
	'total_footers' => 5,
	'foot0' => "Total",
	'col0' => 3,
	'class0' => "text-right",
	'foot1' => number_format($total_sale, 2),
	'col1' => 0,
	'class1' => "info",
	'foot2' =>  number_format($total_cancelation_amount, 2),
	'col2' => 0,
	'class2' => "danger",
	'foot3' => number_format($total_balance, 2),
	'col3' => 0,
	'class3' => "success",
	'foot4' => "",
	'col4' => 4,
	'class4' => "",
	)
);
array_push($array_s, $footer_data);	
echo json_encode($array_s);		
?>