<?php
include_once('../model.php');

$start_date=date('Y-m-d');
$sq_type = mysqli_fetch_assoc(mysqlQuery("select days from cms_master where id='74'"));
$days = $sq_type['days'];
$end_date = date('Y-m-d', strtotime('+ '.$days.' days', strtotime($start_date)));
$sq_tour_groups = mysqlQuery("SELECT * from tour_groups where from_date='$end_date' and status!='Cancel'");
$sq_count = mysqli_num_rows(mysqlQuery("SELECT * from  remainder_status where remainder_name = 'git_happy_journey' and date='$start_date' and status='Done'"));
if($sq_count==0)
{
	while($tour_detail=mysqli_fetch_assoc($sq_tour_groups))
	{
		$sq_tour = mysqli_fetch_assoc(mysqlQuery("SELECT * from tour_master where tour_id='$tour_detail[tour_id]'"));
		$tour_name = $sq_tour['tour_name'].'('.date('d-m-Y', strtotime($tour_detail['from_date'])).' to '.date('d-m-Y', strtotime($tour_detail['to_date'])).')';

		$sq_cus = mysqlQuery("select * from tourwise_traveler_details where tour_group_id='$tour_detail[group_id]' and tour_group_status != 'Cancel' and delete_status='0'");
		while($row_cus = mysqli_fetch_assoc($sq_cus)){
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select *from customer_master where customer_id='$row_cus[customer_id]'"));
			$contact_no =$sq_customer['contact_no'];
			$cust_id = $row_cus['customer_id'];
			$booking_id = $row_cus['id'];
		
			$row = mysqli_fetch_assoc(mysqlQuery("SELECT max(id) as max from remainder_status"));
			$max=$row['max']+1;
			employee_sign_up_sms($contact_no,$tour_name,$cust_id);
			journey_mail($tour_name,$booking_id,$cust_id,'Group Tour');
		}

		$sq_check_status=mysqlQuery("INSERT INTO `remainder_status`(`id`, `remainder_name`, `date`, `status`) VALUES ('$max','git_happy_journey','$start_date','Done')");
	}
}	

//***********************Package Booking Journey mail *******************************************************//
$sq_package = mysqlQuery("SELECT * from package_tour_booking_master where tour_from_date='$end_date'");
$sq_count1 = mysqli_num_rows(mysqlQuery("SELECT * from remainder_status where remainder_name = 'fit_happy_journey' and date='$start_date' and status='Done'"));
if($sq_count1==0)
{
	while($pkg_tour_date = mysqli_fetch_assoc($sq_package))
	{
		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select *from customer_master where customer_id='$pkg_tour_date[customer_id]'"));
		global $secret_key,$encrypt_decrypt;
		$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
		$cust_id = $pkg_tour_date['customer_id'];
	
		$booking_id = $pkg_tour_date['booking_id'];
		$tour_name = $pkg_tour_date['tour_name'].'('.date('d-m-Y', strtotime($pkg_tour_date['tour_from_date'])).' to '.date('d-m-Y', strtotime($pkg_tour_date['tour_to_date'])).')';

		
		$row= mysqli_fetch_assoc(mysqlQuery("SELECT max(id) as max from remainder_status"));
		$max1 = $row['max']+1;
		journey_mail($tour_name,$booking_id,$cust_id,'Package Tour');
		employee_sign_up_sms($contact_no,$tour_name,$cust_id);

	}
	$sq_check_status1=mysqlQuery("INSERT INTO `remainder_status`(`id`, `remainder_name`, `date`, `status`) VALUES ('$max1','fit_happy_journey','$start_date','Done')");
}

function journey_mail($tour_name,$booking_id,$cust_id,$tour_type)
{
		global $app_email_id, $app_name, $app_contact_no, $admin_logo_url, $app_website,$secret_key,$encrypt_decrypt;
		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$cust_id'"));
		$cust_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'].' '.$sq_customer['last_name'];
		
		$email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key); 
		$content = '
		<tr>
        	<td>
        		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">';

					if($tour_type=="Package Tour")
					{	
						$count=1;
						$sq_member=mysqlQuery("select * from package_travelers_details where booking_id='$booking_id' and status!='Cancel'");
						while($row_traveler = mysqli_fetch_assoc($sq_member))
						{
							$content.='<tr><td style="text-align:left;border: 1px solid #888888;">'.$count.'</td>   <td style="text-align:left;border: 1px solid #888888;">'.$row_traveler['m_honorific'].'.'.$row_traveler['first_name'].' '.$row_traveler['last_name'].'</td></tr>';
							$count++;
						}

						$row_tour=mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' and tour_status!='Cancel' and delete_status='0'"));

						$content.='<tr><td style="text-align:left;border: 1px solid #888888;">Tour Date </td>   <td style="text-align:left;border: 1px solid #888888;">'.get_date_user($row_tour['tour_from_date']).' To '.get_date_user($row_tour['tour_to_date']).'</td></tr>';
					}
					if($tour_type=="Group Tour")
					{
						$count=1;
						$sq_tour=mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$booking_id' and tour_group_status!='Cancel' and delete_status='0'"));
						$sq_member=mysqlQuery("select * from travelers_details where traveler_group_id='$sq_tour[traveler_group_id]' and status!='Cancel'");
						while($row_traveler = mysqli_fetch_assoc($sq_member))
						{
							
							$content.='<tr><td style="text-align:left;border: 1px solid #888888;">'.$count.'</td>   <td style="text-align:left;border: 1px solid #888888;">'.$row_traveler['m_honorific'].'.'.$row_traveler['first_name'].' '.$row_traveler['last_name'].'</td></tr>';
							$count++;

						}
						$sq_tour_group=mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$sq_tour[tour_group_id]' and status!='Cancel'"));
						
						$content.='<tr><td style="text-align:left;border: 1px solid #888888;"> Tour Name</td>   <td style="text-align:left;border: 1px solid #888888;">'.$tour_name.'</td></tr>';																
					}
				$content .='					
				</table>
			</td>
		</tr>';
		$subject = 'Happy Journey';
		global $model;

		$model->app_email_send('74',$cust_name,$email_id, $content,$subject,'1');
}
function employee_sign_up_sms($mobile_no,$tour_name,$cust_id)
{
	global $app_name,$app_contact_no;
	$sq_customer = mysqli_fetch_assoc(mysqlQuery("select *from customer_master where customer_id='$cust_id'"));


	$message =  "Your amazing time is starting soon. Best wishes for a safe, happy, healthy journey!  Regard :".$app_contact_no."";
	$message.=$tour_name;
	global $model;
	$model->send_message($mobile_no, $message);
}


?>