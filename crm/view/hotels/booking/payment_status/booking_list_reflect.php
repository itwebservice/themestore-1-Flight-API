<?php
global $currency;
include "../../../../model/model.php";
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$booking_id = $_POST['booking_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$booker_id = $_POST['booker_id'];
$branch_id = $_POST['branch_id'];

$query = "select * from hotel_booking_master where 1 and delete_status='0' ";
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
if($booker_id!=""){
	$query .= " and emp_id='$booker_id'";
}
if($branch_id!=""){
	$query .= " and emp_id in(select emp_id from emp_master where branch_id = '$branch_id')";
}
include "../../../../model/app_settings/branchwise_filteration.php";
// $query .= " order by booking_id desc";
$array_s = array();
$temp_arr = array();
$count = 0;
$total_balance=0;
$total_refund=0;		
$cancel_total =0;
$sale_total = 0;
$paid_total = 0;
$balance_total = 0;

$sq_booking = mysqlQuery($query);
while($row_booking = mysqli_fetch_assoc($sq_booking)){

$pass_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));
$cancel_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]' and status='Cancel'"));
if($pass_count==$cancel_count){
	$bg="danger";
}
else {
	$bg="#fff";
}

$date = $row_booking['created_at'];
$yr = explode("-", $date);
$year =$yr[0];
$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
$email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key);
if($sq_customer['type']=='Corporate'||$sq_customer['type'] == 'B2B'){
	$customer_name = $sq_customer['company_name'];
}else{
	$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
}

$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_booking[emp_id]'"));
if($sq_emp['first_name'] == '') { $emp_name='Admin';}
else{ $emp_name = $sq_emp['first_name'].' '.$sq_emp['last_name']; }

$sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$sq_emp[branch_id]'"));
$branch_name = $sq_branch['branch_name']==''?'NA':$sq_branch['branch_name'];
$sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from hotel_booking_entries where booking_id = '$row_booking[booking_id]'"));

$due_date = ($row_booking['due_date'] == '1970-01-01') ? 'NA' : get_date_user($row_booking['due_date']);
$sq_payment_total = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum ,sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_booking[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

$sq_hotel_info = mysqli_fetch_assoc(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));

$total_paid =  $sq_payment_total['sum'];  
$total_paid = ($total_paid == '') ? '0' : $total_paid;

$sale_bal = $row_booking['total_fee'];
$paid_amount = $sq_payment_total['sum'];
$total_bal = $sale_bal - $paid_amount;
if($paid_amount==""){ $paid_amount = 0; }

$sale_amount=$row_booking['total_fee']-$row_booking['cancel_amount'];
$canc_amount=$row_booking['cancel_amount'];

$total_fee = $row_booking['total_fee'];


if($canc_amount=="") { $canc_amount = 0; }

$total_amount1 = $row_booking['total_fee'] - $canc_amount;
$total_bal = $sale_bal - $canc_amount;  

if($pass_count == $cancel_count){
	if($paid_amount > 0){
		if($canc_amount >0){
			if($paid_amount > $canc_amount){
				$bal = 0;
			}else{
				$bal = $canc_amount - $paid_amount;
			}
		}else{
			$bal = 0;
		}
	}
	else{
		$bal = $canc_amount;
	}
}
else{
	$bal = $total_fee - $paid_amount;
}

//Footer
$cancel_total = $cancel_total + $canc_amount;
$sale_total = $sale_total + $total_bal;
$paid_total = $paid_total + $paid_amount;
$balance_total = $balance_total + $bal;

/////// Purchase ////////
$total_purchase = 0;
$purchase_amt = 0;
$i=0;
$p_due_date = '';
$sq_purchase_count = mysqli_num_rows(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_booking[booking_id]' and delete_status='0'"));
if($sq_purchase_count == 0){  $p_due_date = 'NA'; }
$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_booking[booking_id]' and delete_status='0'");
while($row_purchase = mysqli_fetch_assoc($sq_purchase)){
	if($row_purchase['purchase_return'] == 0){
		$total_purchase += $row_purchase['net_total'];
	}
	else if($row_purchase['purchase_return'] == 2){
		$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
		$p_purchase = ($row_purchase['net_total'] - floatval($cancel_estimate[0]->net_total));
		$total_purchase += $p_purchase;
	}
}
$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_booking[booking_id]' and delete_status='0'"));	
$vendor_name1 = ($sq_purchase_count > 0) ? get_vendor_name_report($sq_purchase1['vendor_type'], $sq_purchase1['vendor_type_id']) : 'NA';

$invoice_no = get_hotel_booking_id($row_booking['booking_id'],$year);
$booking_id = $row_booking['booking_id'];
$invoice_date = date('d-m-Y',strtotime($row_booking['created_at']));
$customer_id = $row_booking['customer_id'];
$service_name = "Hotel Invoice";
//**Service Tax
$service_tax1 = $row_booking['service_tax_subtotal'];
//**Basic Cost
$basic_cost = $row_booking['sub_total'] + $row_booking['cancel_amount'];
$service_charge = $row_booking['service_charge'];
$credit_card_charges = $sq_payment_total['sumc'];

//**Net Amount
$net_amount = $row_booking['total_fee'] - $row_booking['cancel_amount'];;
$sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='Hotel / Accommodation'"));   
$sac_code = $sq_sac['hsn_sac_code'];
//// Calculate Service Tax//////
$service_tax_amount = 0;
if($row_booking['service_tax_subtotal'] !== 0.00 && ($row_booking['service_tax_subtotal']) !== ''){
$service_tax_subtotal1 = explode(',',$row_booking['service_tax_subtotal']);
for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
	$service_tax = explode(':',$service_tax_subtotal1[$i]);
	$service_tax_amount +=  $service_tax[2];
	}
}

//// Calculate Markup Tax//////
$markupservice_tax_amount = 0;
if($row_booking['markup_tax'] !== 0.00 && $row_booking['markup_tax'] !== ""){
$service_tax_markup1 = explode(',',$row_booking['markup_tax']);
for($i=0;$i<sizeof($service_tax_markup1);$i++){
	$service_tax = explode(':',$service_tax_markup1[$i]);
	$markupservice_tax_amount += $service_tax[2];

	}
}
if($app_invoice_format == 4)
$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/tax_invoice_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&service_tax=$service_tax1&net_amount=$net_amount&service_charge=$service_charge&total_paid=$total_paid&balance_amount=$total_bal&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&pass_count=$pass_count&credit_card_charges=$credit_card_charges";
else
$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/hotel_body_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&service_tax=$service_tax1&net_amount=$net_amount&service_charge=$service_charge&total_paid=$total_paid&balance_amount=$total_bal&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&credit_card_charges=$credit_card_charges&canc_amount=$canc_amount&bg=$bg";

$sq_incentive_count = mysqli_num_rows(mysqlQuery("select * from booker_sales_incentive where booking_id='$row_booking[booking_id]' and service_type='Hotel Booking'"));
$sq_incentive = mysqli_fetch_assoc(mysqlQuery("select * from booker_sales_incentive where booking_id='$row_booking[booking_id]' and service_type='Hotel Booking'"));
$incentive_amount = ($sq_incentive_count>0)?$sq_incentive['incentive_amount']:0;
// Currency conversion
$currency_amount1 = currency_conversion($currency,$row_booking['currency_code'],$net_amount);
if($row_booking['currency_code'] !='0' && $currency != $row_booking['currency_code']){
	$currency_amount = ' ('.$currency_amount1.')';
}else{
	$currency_amount = '';
}
$temp_arr = array( "data" => array(

	(int)(++$count),
	get_hotel_booking_id($row_booking['booking_id'],$year),
	$customer_name,
	$contact_no,
	$email_id,
	$sq_total_member,
	get_date_user($row_booking['created_at']),
	'<button class="btn btn-info btn-sm" id="packagev_btn-'. $row_booking['booking_id'] .'" onclick="hotel_view_modal('. $row_booking['booking_id'] .')" data-toggle="tooltip" title="View Details" id="view-'. $row_booking['booking_id'] .'"><i class="fa fa-eye" aria-hidden="true"></i></button>',
	number_format(($row_booking['sub_total']),2),
	number_format($row_booking['service_charge']+$row_booking['markup'],2),
	number_format($service_tax_amount + $markupservice_tax_amount,2),
	number_format($row_booking['tcs_tax'],2),
	number_format($sq_payment_total['sumc'],2),
	number_format($row_booking['discount'],2),
	number_format($row_booking['tds'],2),
	number_format($row_booking['total_fee'],2),
	number_format($canc_amount, 2),
	number_format($net_amount, 2).$currency_amount,
	number_format($paid_amount, 2),
	'<button class="btn btn-info btn-sm" id="paymentv_btn-'. $row_booking['booking_id'] .'" onclick="payment_view_modal('.$row_booking['booking_id'] .')"  data-toggle="tooltip" title="View Details" id="pview-'. $row_booking['booking_id'] .'"><i class="fa fa-eye" aria-hidden="true"></i></button>',
	number_format($bal, 2),
	$due_date,
	number_format($total_purchase,2),
	'<button class="btn btn-info btn-sm" id="supplierv_btn-'. $row_booking['booking_id'] .'" onclick="supplier_view_modal('. $row_booking['booking_id'] .')" data-toggle="tooltip" title="View Details" id="sview-'. $row_booking['booking_id'] .'"><i class="fa fa-eye" aria-hidden="true"></i></button>',
	$branch_name,
	$emp_name,
	number_format($incentive_amount,2),
	), "bg" =>$bg);
	array_push($array_s,$temp_arr);
}
$footer_data = array("footer_data" => array(
	'total_footers' => 6,
	'foot0' => "",
	'col0' =>14,
	'class0' =>"",

	'foot1' => "TOTAL CANCEL : ".number_format($cancel_total,2),
	'col1' => 3,
	'class1' =>"danger text-right",
	
	'foot2' => "TOTAL SALE :".number_format($sale_total,2),
	'col2' => 3,
	'class2' =>"info text-right",

	'foot3' => "TOTAL PAID : ".number_format($paid_total,2),
	'col3' => 2,
	'class3' =>"success text-right",

	'foot4' => "TOTAL BALANCE : ".number_format($balance_total,2),
	'col4' => 2,
	'class4' =>"warning text-right",

	'foot5' => "",
	'col5' => 4,
	'class5' =>"",
	'foot6' => "",
	'col6' => 5,
	'class6' =>""

	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);
?>