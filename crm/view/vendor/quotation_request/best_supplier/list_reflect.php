<?php
include "../../../../model/model.php";
include_once('../../inc/vendor_generic_functions.php');
$quotation_for = $_POST['quotation_for'];
$enquiry_id = $_POST['enquiry_id'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status']; 
$role = $_SESSION['role'];
$array_s = array();
$temp_arr = array();
$query = "SELECT * FROM `vendor_reply_master` WHERE `total_cost` in (select min(`total_cost`) from vendor_reply_master group by `request_id`) ";
if($quotation_for!=""){
	$query .= " and quotation_for='$quotation_for'";	
}
if($enquiry_id!=""){
	$query .= " and request_id in(select request_id from vendor_request_master where enquiry_id='$enquiry_id') ";
}
$query .=" group by request_id ";

$count = 0;	
$total_cost = 0;  
$sq_req = mysqlQuery($query);
while($row_req = mysqli_fetch_assoc($sq_req)){

	$sq_request = mysqli_fetch_assoc(mysqlQuery("select city_id,enquiry_id,quotation_for,quotation_date from vendor_request_master where request_id='$row_req[request_id]'"));
	$booking_date = $sq_request['quotation_date'];
	$yr = explode("-", $booking_date);
	$year =$yr[0];
	if($sq_request['quotation_for'] == 'Hotel'){
		$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select city_id from hotel_master where hotel_id='$row_req[supplier_id]' "));
	}
	if($sq_request['quotation_for'] == 'DMC'){
		$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select city_id from dmc_master where dmc_id='$row_req[supplier_id]' "));
	}
	if($sq_request['quotation_for'] == 'Transport'){
		$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select city_id from transport_agency_master where transport_agency_id='$row_req[supplier_id]' "));
	}
	$sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$sq_hotel[city_id]'"));
	$vendor_type_val = get_vendor_name($row_req['quotation_for'], $row_req['supplier_id']);
	$total_cost += $row_req['total_cost'];

	$sq_currency1 = mysqli_fetch_assoc(mysqlQuery("select * from currency_name_master where id = '$row_req[currency_code]'"));
	$temp_arr = array( "data" => array(
		(int)(++$count),
		ge_vendor_request_id($row_req['request_id'],$year),
		$sq_city['city_name'],
		$vendor_type_val ,
		$row_req['total_cost'].' ('.$sq_currency1['currency_code'].')',
		'<button class="btn btn-info btn-sm" onclick="view_modal('. $row_req['supplier_id'] .',\''. $row_req['quotation_for'] .'\','. $sq_request['enquiry_id'] .',\''. $row_req['request_id'] .'\')" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></button>'
	), "bg" =>'');
array_push($array_s,$temp_arr); 
		

}
echo json_encode($array_s);		
?>
	
