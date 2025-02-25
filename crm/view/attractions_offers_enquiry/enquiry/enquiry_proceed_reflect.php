<?php
include "../../../model/model.php";
global $show_entries_switch;
$branch_admin_id = $_SESSION['branch_admin_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$login_id = $_SESSION['login_id'];
$emp_id = $_SESSION['emp_id'];
$array_s = array();
$temp_arr = array();
$financial_year_id = $_POST['financial_year_id'];
$enquiry_type = $_POST['enquiry_type'];
$enquiry = $_POST['enquiry'];
$enquiry_status_filter = $_POST['enquiry_status'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$emp_id_filter = isset($_POST['emp_id_filter']) ? $_POST['emp_id_filter'] : '';;
$branch_status = $_POST['branch_status'];
$reference_id_filter=$_POST['reference_id_filter'];

//////////Calculate no.of .enquiries Start///////////////////
$enq_count = "SELECT * FROM `enquiry_master` left join enquiry_master_entries as ef on enquiry_master.entry_id = ef.entry_id where enquiry_master.status!='Disabled'";

if($financial_year_id!=""){
	$enq_count .=" and financial_year_id='$financial_year_id'";
}
if($emp_id_filter!=""){
	$enq_count .=" and assigned_emp_id='$emp_id_filter'";
}
elseif($branch_status=='yes' && $role=='Branch Admin'){
	$enq_count .= " and branch_admin_id='$branch_admin_id'";
}
if($enquiry!="" && $enquiry!=='undefined'){
	$enq_count .=" and enquiry='$enquiry' ";
}
if($enquiry_type!=""){
	$enq_count .=" and enquiry_type='$enquiry_type' ";
}
if($reference_id_filter!=""){
	$enq_count .=" and reference_id='$reference_id_filter' ";
}
if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$enq_count .=" and (enquiry_date between '$from_date' and '$to_date')";
}
if($branch_status=='yes' && $role!='Admin'){
	$enq_count .= " and branch_admin_id = '$branch_admin_id'";
}
if($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
	if($role !='Admin' && $role!='Branch Admin')
	{
		if($show_entries_switch == 'No'){
			$enq_count .= " and assigned_emp_id='$emp_id' and enquiry_master.status!='Disabled' ";  
		}
		else{
			if($role == 'Backoffice'){
				$enq_count .=" and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id')";
			}else{
				$enq_count .= " and assigned_emp_id='$emp_id' and enquiry_master.status!='Disabled' ";
			}
		}
		if($enquiry_type!=""){
			$enq_count .=" and enquiry_type='$enquiry_type' ";
		}
		if($reference_id_filter!=""){
			$enq_count .=" and reference_id='$reference_id_filter' ";
		}
		if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$enq_count .=" and (enquiry_date between '$from_date' and '$to_date')";
		}
		if($enquiry!=""){
			$enq_count .=" and enquiry='$enquiry' ";
		}
	}   
}
if($enquiry_status_filter!='')
{
	$enq_count .= " and ef.followup_status='$enquiry_status_filter'";
}
$enq_count .= " ORDER BY enquiry_master.enquiry_id DESC ";
$enquiry_count = mysqli_num_rows(mysqlQuery($enq_count));
//////////Calculate no.of .enquiries End///////////////////

///////////////////Enquiry table data start///////////////
$query = "SELECT * FROM `enquiry_master` left join enquiry_master_entries as ef on enquiry_master.entry_id=ef.entry_id where enquiry_master.status!='Disabled'";

if($financial_year_id!=""){
	$query .=" and financial_year_id='$financial_year_id'";
}
if($emp_id_filter!=""){
	$query .=" and assigned_emp_id='$emp_id_filter'";
}
if($branch_status=='yes' && $role=='Branch Admin'){
	$query .=" and branch_admin_id = '$branch_admin_id'";
}	
if($enquiry!="" && $enquiry!=='undefined'){
    $query .=" and enquiry='$enquiry' ";
}		
if($enquiry_type!=""){
	$query .=" and enquiry_type='$enquiry_type' ";
}
if($reference_id_filter!=""){
	$query .=" and reference_id='$reference_id_filter' ";
}
if($from_date!='' && $to_date!=""){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .=" and (enquiry_date between '$from_date' and '$to_date')";
}
if($branch_status=='yes' && $role!='Admin'){
	$query .= " and branch_admin_id = '$branch_admin_id'";
}
if($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
	
	if($show_entries_switch == 'No'){
		$query .= " and assigned_emp_id='$emp_id'";
	}
	else{
		if($role == 'Backoffice'){
			
			$query .=" and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id')";
		}else{
			$query .= " and assigned_emp_id='$emp_id'";
		}
	}
	if($enquiry_type!=""){
		$query .=" and enquiry_type='$enquiry_type' ";
	}
	if($reference_id_filter!=""){
		$query .=" and reference_id='$reference_id_filter' ";
	}
	if($from_date!='' && $from_date!='undefined' && $to_date!="" && $to_date!='undefined'){
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$query .=" and (enquiry_date between '$from_date' and '$to_date')";
	}
	if($enquiry!=""){
		$query .=" and enquiry='$enquiry' ";
	}
}
if($enquiry_status_filter!=''){
	$query .= " and ef.followup_status='$enquiry_status_filter'";
}
// echo $query;
$query .= " ORDER BY enquiry_master.enquiry_id DESC";
//////////Enquiry table data End//////////
$count = 0;
$sq_enquiries=mysqlQuery($query);

while($row = mysqli_fetch_assoc($sq_enquiries)){
	
	$cust_user_name = '';
	if($row['user_id'] != 0){ 
		$row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$row[user_id]'"));
		$cust_user_name = ' ('.$row_user['name'].')';
	}

	$actions_string = "";
	$enquiry_id = $row['enquiry_id'];
	$assigned_emp_id = $row['assigned_emp_id'];
	$sq_emp = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from emp_master where emp_id='$assigned_emp_id'"));
	$allocated_to = ($assigned_emp_id != 0)?$sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

	$enquiry_content = $row['enquiry_content'];
	$enquiry_content_arr1 = json_decode($enquiry_content, true);

	$enquiry_status1 = mysqli_fetch_assoc(mysqlQuery("select followup_date,followup_reply,followup_status from enquiry_master_entries where enquiry_id='$row[enquiry_id]' order by entry_id DESC"));
	$followup_date1 = $enquiry_status1['followup_date'];
	$followup_status=$enquiry_status1['followup_status'];

	if($followup_status == 'Converted'){
		$bg = 'success';
	}
	elseif($followup_status == 'Dropped'){
		$bg = 'danger';
	}
	else{
		$bg = '';
	}
	$date = $row['enquiry_date'];
	$yr = explode("-", $date);
	$year =$yr[0];

	$temp_arr = array ( "data" => array(
		(int)(++$count),
		get_enquiry_id($enquiry_id,$year),
		$row['name'].$cust_user_name,
		$row['mobile_no'],
		$row['enquiry_type'],
		get_date_user($row['enquiry_date']),
		get_datetime_user($followup_date1)
		)
	);
	if($followup_status != 'Dropped'){
		if($row['enquiry_type'] == "Package Booking" || $row['enquiry_type'] == "Group Booking" || $row['enquiry_type'] == "Hotel" || $row['enquiry_type'] == "Car Rental" || $row['enquiry_type'] == "Flight Ticket"){
			
			if($row['enquiry_type'] == "Hotel"){
				$link = "hotel_quotation/save";
			}
			else if($row['enquiry_type'] == "Car Rental" || $row['enquiry_type'] == "Flight Ticket"){

				$link1 = ($row['enquiry_type'] == "Car Rental") ? "car_rental" : "flight";
				$link = "package_booking/quotation/car_flight/".$link1;
			}else{
				$link1 = ($row['enquiry_type'] == "Package Booking") ? "home/save" : "group_tour";
				$link = "package_booking/quotation/".$link1;
			}
			$form_add = '<form style="display:inline-block" action="'. BASE_URL.'view/'.$link.'/index.php" target="_blank" id="frm_booking_1" method="GET">
				<input type="hidden" id="enquiry_id" name="enquiry_id" value="'.$row['enquiry_id'].'">
				<button style="display:inline-block" data-toggle="tooltip" class="btn btn-info btn-sm" title="Create Quick Quotation"><i class="fa fa-plus"></i></button>
			</form>';
			$actions_string .= $form_add;
		}
	}
	
	$temp_arr1 = '<button style="display:inline-block" data-toggle="tooltip" class="btn btn-info btn-sm" onclick="followup_modal('.$row['enquiry_id'].');btnDisableEnable(this.id)" id="followup_modal_add-'.$row['enquiry_id'].'" title="Add New Followup Details"><i class="fa fa-reply-all"></i></button>';
	$actions_string .= $temp_arr1;

	array_push($temp_arr['data'],$allocated_to);
	$temp_arr2 = array(
		'<button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" onclick="update_modal('.$row['enquiry_id'].');btnDisableEnable(this.id)" id="enq_modal_update-'.$row['enquiry_id'].'" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>',
		'<button data-toggle="tooltip" style="display:inline-block" class="btn btn-info btn-sm" onclick="view_modal('.$row['enquiry_id'] .');btnDisableEnable(this.id)" id="enq_modal_view-'.$row['enquiry_id'].'" title="View Details"><i class="fa fa-eye"></i></button>'
	);
	foreach($temp_arr2 as $vals) $actions_string .= $vals;
	if($role=="Admin" || $role=='Branch Admin'){
		
		$temp_arr3= '<button data-toggle="tooltip" style="display:inline-block" class="btn btn-danger btn-sm" onclick="enquiry_status_disable('.$row['enquiry_id'] .')" title="Delete Enquiry"><i class="fa fa-trash"></i></button>';
		$actions_string .= $temp_arr3;
	}
	array_push($temp_arr['data'] , $actions_string);
	$temp_arr['bg'] = $bg;
	array_push($array_s,$temp_arr); 
}
echo json_encode($array_s);
?>