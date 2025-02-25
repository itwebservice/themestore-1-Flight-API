<?php
$flag = true;
class exc_refund{

public function exc_refund_save()
{
	$exc_id = $_POST['exc_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$bank_name = $_POST['bank_name'];
	$transaction_id = $_POST['transaction_id'];	
	$bank_id = $_POST['bank_id'];
	$entry_id_arr = $_POST['entry_id_arr'];	

	$refund_date = date('Y-m-d', strtotime($refund_date));
	$created_at = date('Y-m-d H:i');
	
	if($refund_mode=="Cheque"){ 
		$clearance_status = "Pending"; } 
	else {  $clearance_status = ""; }	

	$financial_year_id = $_SESSION['financial_year_id'];
	$branch_admin_id = $_SESSION['branch_admin_id'];

	begin_t(); 

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(refund_id) as max from exc_refund_master"));
	$refund_id = $sq_max['max'] + 1;

	$sq_payment = mysqlQuery("insert into exc_refund_master (refund_id, exc_id, financial_year_id, refund_date, refund_amount, refund_mode, bank_name, transaction_id, bank_id, clearance_status, created_at) values ('$refund_id', '$exc_id', '$financial_year_id', '$refund_date', '$refund_amount', '$refund_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$created_at') ");

	if($refund_mode == 'Credit Note'){
		$sq_sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id'"));
		$customer_id = $sq_sq_exc_info['customer_id'];
		
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from credit_note_master"));
		$id = $sq_max['max'] + 1;

		$sq_payment = mysqlQuery("insert into credit_note_master (id, financial_year_id, module_name, module_entry_id, customer_id, payment_amount,refund_id,created_at,branch_admin_id) values ('$id', '$financial_year_id', 'Excursion Booking', '$exc_id', '$customer_id','$refund_amount','$refund_id','$refund_date','$branch_admin_id') ");
	}

	if(!$sq_payment){
		rollback_t();
		echo "error--Sorry, Refund not saved!";
		exit;
	}
	else{

		for($i=0; $i<sizeof($entry_id_arr); $i++){

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from exc_refund_entries"));
			$id= $sq_max['max'] + 1;;

			$sq_entry = mysqlQuery("insert into exc_refund_entries(id, refund_id, entry_id) values ('$id', '$refund_id', '$entry_id_arr[$i]')");
			if(!$sq_entry){
				$GLOBALS['flag'] = false;
				echo "error--Some entries not saved!";
				//exit;
			}

		}

		if($refund_mode != 'Credit Note'){
			//Finance save
	    	$this->finance_save($refund_id);
			//Bank and Cash Book Save
			$this->bank_cash_book_save($refund_id);
	    }
    	//refund mail
    	
		if($refund_amount!=0){
			$this->refund_mail_send($exc_id,$refund_amount,$refund_date,$refund_mode,$transaction_id);
		}
		if($GLOBALS['flag']){
			commit_t();
			echo "Refund has been successfully saved.";
			exit;	
		}
		else{
			rollback_t();
			exit;
		}

	}

}


public function finance_save($refund_id)
{
	$row_spec = 'sales';
	$exc_id = $_POST['exc_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$transaction_id = $_POST['transaction_id'];
	$bank_id = $_POST['bank_id'];	

	$refund_date = date('Y-m-d', strtotime($refund_date));
	$year1 = explode("-", $refund_date);
	$yr1 =$year1[0];

	global $transaction_master;

	$sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id' and delete_status='0'"));
  	$customer_id = $sq_exc_info['customer_id'];
	$year = explode("-", $sq_exc_info['created_at']);
	$yr =$year[0];

  	//Getting cash/Bank Ledger
    if($refund_mode == 'Cash') {  $pay_gl = 20; $type='CASH PAYMENT'; }
    else{ 
	    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
	    $pay_gl = $sq_bank['ledger_id'];
		$type='BANK PAYMENT';
     } 

  	//Getting customer Ledger
	  $sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
	  $cust_gl = $sq_cust['ledger_id'];

	$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_cust['type']== 'Corporate' || $sq_cust['type']== 'B2B'){
	  $cust_name = $sq_cust['company_name'];
	}else{
	  $cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	}
	$sq_exc = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_entries where exc_id='$exc_id'"));
	$sq_t = mysqli_fetch_assoc(mysqlQuery("select excursion_name from excursion_master_tariff where entry_id='$sq_exc[exc_name]'"));
	$service_name = $sq_t['excursion_name'];
	$pax = $sq_exc['total_adult']+$sq_exc['total_child'];
	
	$particular = 'Payment through '.$refund_mode.' against Invoice no '.get_exc_booking_id($exc_id,$yr1).' for the '.$service_name.' for '.$cust_name.' * '.$pax.' on Dt.'.get_date_user($sq_exc['exc_date']);

	////////Refund Amount//////
    $module_name = "Excursion Booking Refund Paid";
    $module_entry_id = $exc_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $pay_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

	////////Refund Amount//////
    $module_name = "Excursion Booking Refund Paid";
    $module_entry_id = $exc_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

}


public function bank_cash_book_save($refund_id)
{
	$exc_id = $_POST['exc_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$bank_name = $_POST['bank_name'];
	$transaction_id = $_POST['transaction_id'];	
	$bank_id = $_POST['bank_id'];
	$refund_date = date('Y-m-d', strtotime($refund_date));
	$year1 = explode("-", $refund_date);
	$yr1 =$year1[0];

	$sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id'"));
	$customer_id = $sq_exc_info['customer_id'];
	$year = explode("-", $sq_exc_info['created_at']);
	$yr =$year[0];

	$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_cust['type']== 'Corporate' || $sq_cust['type']== 'B2B'){
	  $cust_name = $sq_cust['company_name'];
	}else{
	  $cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	}
	$sq_exc = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_entries where exc_id='$exc_id'"));
	$sq_t = mysqli_fetch_assoc(mysqlQuery("select excursion_name from excursion_master_tariff where entry_id='$sq_exc[exc_name]'"));
	$service_name = $sq_t['excursion_name'];
	$pax = $sq_exc['total_adult']+$sq_exc['total_child'];
	
	$particular = 'Payment through '.$refund_mode.' against Invoice no '.get_exc_booking_id($exc_id,$yr1).' for the '.$service_name.' for '.$cust_name.' * '.$pax.' on Dt.'.get_date_user($sq_exc['exc_date']);

	global $bank_cash_book_master;

	$module_name = "Excursion Booking Refund Paid";
	$module_entry_id = $refund_id;
	$payment_date = $refund_date;
	$payment_amount = $refund_amount;
	$payment_mode = $refund_mode;
	$bank_name = $bank_name;
	$transaction_id = $transaction_id;
	$bank_id = $bank_id;
	$particular = $particular;
	$clearance_status = ($payment_mode=="Cheque") ? "Pending" : "";
	$payment_side = "Credit";
	$payment_type = ($payment_mode=="Cash") ? "Cash" : "Bank";
	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type);

}
public function refund_mail_send($exc_id,$refund_amount,$refund_date,$refund_mode,$transaction_id)
{

	global $model,$currency,$encrypt_decrypt,$secret_key;

	$sq_exc_info = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id='$exc_id'"));
	$date = $sq_exc_info['created_at'];
	$yr = explode("-", $date);
	$year =$yr[0];
	$cust_email = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_exc_info[customer_id]'"));
	if($cust_email['type']=='Corporate'||$cust_email['type'] == 'B2B'){
		$cust_name = $cust_email['company_name'];
	}else{
		$cust_name = $cust_email['first_name'].' '.$cust_email['last_name'];
	}
	$email_id = $encrypt_decrypt->fnDecrypt($cust_email['email_id'], $secret_key);

	$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from exc_payment_master where exc_id='$exc_id' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$sq_pay=mysqli_fetch_assoc(mysqlQuery("select sum(refund_amount) as sum from exc_refund_master where exc_id='$exc_id' and  clearance_status!='Cancelled'"));

	$sale_Amount = $sq_exc_info['exc_total_cost'];
	$paid_amount = $sq_paid_amount['sum'];
	$cancel_amount = $sq_exc_info['cancel_amount'];

	$sale_Amount1 = currency_conversion($currency,$sq_exc_info['currency_code'],$sale_Amount);
	$paid_amount1 = currency_conversion($currency,$sq_exc_info['currency_code'],$paid_amount);
	$cancel_amount1 = currency_conversion($currency,$sq_exc_info['currency_code'],$cancel_amount);
	$refund_amount1 = currency_conversion($currency,$sq_exc_info['currency_code'],$refund_amount);

	$remaining = $sq_exc_info['total_refund_amount']- $sq_pay['sum'];
	$remaining = currency_conversion($currency,$sq_exc_info['currency_code'],$remaining);

	$content = '
	<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
			<tr><td style="text-align:left;border: 1px solid #888888;">Service Type</td>   <td style="text-align:left;border: 1px solid #888888;">Activity Booking</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Selling Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sale_Amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Paid Amount</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$paid_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Cancellation Charges</td>   <td style="text-align:left;border: 1px solid #888888;">'.$cancel_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Mode</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_mode.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.get_date_user($refund_date).'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Pending Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$remaining.'</td></tr>
		</table>
	</tr>'; 
	$content .= '</tr>';

	$subject = 'Activity Cancellation Refund( '.get_exc_booking_id($sq_exc_info['exc_id'],$year).' )';
	$model->app_email_send('43',$cust_name,$email_id, $content,$subject);
	}

}
?>