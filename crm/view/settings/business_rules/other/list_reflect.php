<?php
include "../../../../model/model.php";
$status = $_POST['status'];
$tax_filter = isset($_POST['tax_filter']) ? $_POST['tax_filter'] : '';
$array_s = array();
$temp_arr = array();
$query = "select * from other_master_rules where 1 ";
if($tax_filter != ''){
	$query .= " and entry_id='$tax_filter'";
}
if($status != ''){
	$query .= " and status='$status'";
}
$query .= " order by created_at desc";
$count = 0;
$sq_taxes = mysqlQuery($query);
while($row_taxes = mysqli_fetch_assoc($sq_taxes)){

	$bg = ($row_taxes['status']=="Inactive") ? "danger" : "";
	$validity = ($row_taxes['validity'] == "Period") ? get_date_user($row_taxes['from_date']).' To '.get_date_user($row_taxes['to_date']): $row_taxes['validity'];
	$sq_ledger = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where ledger_id='$row_taxes[ledger_id]'"));
	$sq_other = mysqli_fetch_assoc(mysqlQuery("select * from other_charges_master where entry_id='$row_taxes[rule_for]'"));
	
	$temp_arr = array("data" =>array(
		(int)($row_taxes['rule_id']),
		$sq_other['name'],
		$row_taxes['name'],
		$sq_ledger['ledger_name'],
		$validity,
		$row_taxes['travel_type'],
		'<div class="table-actions-btn"><button class="btn btn-info btn-sm" onclick="update_modal('.$row_taxes['rule_id'] .');" id="updateo_rule-'.$row_taxes['rule_id'] .'" data-toggle="tooltip" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>
		<button class="btn btn-warning btn-sm" onclick="copy_rule('.$row_taxes['rule_id'] .');" data-toggle="tooltip" title="Copy Other Rule"><i class="fa fa-files-o"></i></button></div>'), "bg" => $bg
	);
	array_push($array_s,$temp_arr); 
}
echo json_encode($array_s);
?>