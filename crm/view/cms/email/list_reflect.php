<?php
include "../../../model/model.php";

$active_flag = $_POST['active_flag'];
$email_for = $_POST['email_for'];
$email_type = $_POST['email_type'];
$email_to = isset($_POST['email_to']) ? $_POST['email_to'] : '';
$array_s = array();
$temp_arr = array();

$query = "select * from cms_master_entries where 1 ";

if($active_flag!=""){
	$query .=" and active_flag='$active_flag' ";
}
if($email_for!=""){
	$query .=" and id in(select id from cms_master where id='$email_for') ";
}
if($email_type!=""){
	$query .=" and id in(select id from cms_master where type_id='$email_type') ";
}
$count = 0;
$sq_cms = mysqlQuery($query);
while($row_cms = mysqli_fetch_assoc($sq_cms)){
	$sq_cms_name = mysqli_fetch_assoc(mysqlQuery("select * from cms_master where id='$row_cms[id]'"));

	if($sq_cms_name['type_id'] == '1'){ $type = 'Transactional'; }
	elseif($sq_cms_name['type_id'] == '2'){ $type = 'Reminder'; }

	$no_days = ($sq_cms_name['days'] != 0) ? ' ('.$sq_cms_name['days'].' days)' : '';
	$bg = ($row_cms['active_flag']=="Inactive") ? "danger" : "";
	$temp_arr = array ("data"=>array(
		$row_cms['entry_id'],
		$type,
		$sq_cms_name['draft_for'].$no_days,
		$sq_cms_name['draft_to'],
		'<button class="btn btn-info btn-sm" id="update-'.$row_cms['entry_id'].'" onclick="update_modal('.$row_cms['entry_id'].')" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>'),"bg"=>$bg
	);
	array_push($array_s,$temp_arr);
}
echo json_encode($array_s);
?>
	