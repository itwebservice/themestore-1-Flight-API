<?php
class quotation_email_send{
public function quotation_email()
{
	$quotation_id = $_POST['quotation_id'];
	$quotation_no = base64_encode($quotation_id);
	$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_quotation_master where quotation_id='$quotation_id'"));
	$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
	$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

	$quotation_date = $sq_quotation['quotation_date'];
	$yr = explode("-", $quotation_date);
	$year =$yr[0];
	
	if($sq_emp_info['first_name']==''){
		$emp_name = 'Admin';
	}
	else{
		$emp_name = $sq_emp_info['first_name'].' '.$sq_emp_info['last_name'];
	}

	global $app_cancel_pdf,$theme_color,$currency;

	if($app_cancel_pdf == ''){	$url =  BASE_URL.'view/package_booking/quotation/cancellaion_policy_msg.php'; }
	else{
		$url = explode('uploads', $app_cancel_pdf);
		$url = BASE_URL.'uploads'.$url[1];
	}	
	$quotation_cost = currency_conversion($currency,$sq_quotation['currency_code'],$sq_quotation['quotation_cost']);

	$content = '
	<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
			<tr><td style="text-align:left;border: 1px solid #888888;">Name</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sq_quotation['customer_name'].'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Destination Name</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$sq_quotation['tour_name'].'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Tour Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.date('d-m-Y', strtotime($sq_quotation['from_date'])).' To '.date('d-m-Y', strtotime($sq_quotation['to_date'])).'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Quotation Cost</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$quotation_cost.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Created By</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$emp_name.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">View Quotation</td>   <td style="text-align:left;border: 1px solid #888888;" ><a style="color: '.$theme_color.';text-decoration: none;" href="'.BASE_URL.'model/package_tour/quotation/group_tour/quotation_email_template.php?quotation='.$quotation_no.'">View</a></td></tr>
		</table>
		</tr>				
	';
	$subject = "New Quotation"."(".get_quotation_id($quotation_id,$year).")";
	global $model;
	$model->app_email_send('8',$sq_quotation['customer_name'],$sq_quotation['email_id'], $content,$subject,'1');
	echo "Quotation email successfully sent.";
	exit;
	
}
	public function quotation_whatsapp(){
		$quotation_id = $_POST['quotation_id'];
		$quotation_no = base64_encode($quotation_id);
		global $app_contact_no,$currency,$app_name;
		
		$all_message = "";
		$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_quotation_master where quotation_id='$quotation_id'"));
		
		$mobile_no = mysqli_fetch_assoc(mysqlQuery("SELECT landline_no FROM `enquiry_master` WHERE enquiry_id = ".$sq_quotation['enquiry_id']));
		$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
		$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

		if($sq_login['emp_id'] == 0){
			$contact = $app_contact_no;
		}
		else{
			$contact = $sq_emp_info['mobile_no'];
		}

		$quotation_cost = currency_conversion($currency,$sq_quotation['currency_code'],$sq_quotation['quotation_cost']);
		$whatsapp_msg = rawurlencode('Dear '.$sq_quotation['customer_name'].',
Hope you are doing great. This is group tour quotation details as per your request. We look forward to having you onboard with us.
*Destination Name* : '.$sq_quotation['tour_name'].'
*Duration* : '.($sq_quotation['total_days']).'N/'.($sq_quotation['total_days']+1).'D'.'
*Cost* : '.$quotation_cost.'
*Link* : '.BASE_URL.'model/package_tour/quotation/group_tour/quotation_email_template.php?quotation='.$quotation_no.'

Please contact for more details : '.$app_name.' '.$contact.'
Thank you.');
		$all_message .=$whatsapp_msg;
		$link = 'https://web.whatsapp.com/send?phone='.$sq_quotation['mobile_number'].'&text='.$all_message;
		echo $link;
	}
}
?>