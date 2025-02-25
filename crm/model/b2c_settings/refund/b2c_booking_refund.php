<?php
$flag = true;
class b2c_refund{

public function refund_save()
{
	$booking_id = $_POST['booking_id'];
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

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(refund_id) as max from b2c_booking_refund_master"));
	$refund_id = $sq_max['max'] + 1;

	$sq_payment = mysqlQuery("insert into b2c_booking_refund_master (refund_id, booking_id, financial_year_id, refund_date, refund_amount, refund_mode, bank_name, transaction_id, bank_id, clearance_status, created_at) values ('$refund_id', '$booking_id', '$financial_year_id', '$refund_date', '$refund_amount', '$refund_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$created_at') ");

	if($refund_mode == 'Credit Note'){
		
		$sq_b2c_info = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
		$customer_id = $sq_b2c_info['customer_id'];
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from credit_note_master"));
		$id = $sq_max['max'] + 1;

		$sq_payment = mysqlQuery("insert into credit_note_master (id, financial_year_id, module_name, module_entry_id, customer_id, payment_amount,refund_id,created_at,branch_admin_id) values ('$id', '$financial_year_id', 'B2C Booking', '$booking_id', '$customer_id','$refund_amount','$refund_id','$refund_date','$branch_admin_id') ");
	}

	if(!$sq_payment){
		rollback_t();
		echo "error--Sorry, Refund not saved!";
		exit;
	}
	else{

		for($i=0; $i<sizeof($entry_id_arr); $i++){

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from b2c_booking_refund_entries"));
			$id= $sq_max['max'] + 1;;

			$sq_entry = mysqlQuery("insert into b2c_booking_refund_entries(id, refund_id, entry_id) values ('$id', '$refund_id', '$entry_id_arr[$i]')");
			if(!$sq_entry){
				$GLOBALS['flag'] = false;
				echo "error--Some entries not saved!";
				//exit;
			}

		}

		if($refund_mode != 'Credit Note'){
			//Finance save
	    	$this->finance_save($refund_id);

	    }
    	//Bank and Cash Book Save
		$this->bank_cash_book_save($refund_id);
		//refund email to customer
		
		if($refund_amount!=0){
			$this->refund_mail_send($booking_id,$refund_amount,$refund_date,$refund_mode,$transaction_id);
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
	$booking_id = $_POST['booking_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$transaction_id = $_POST['transaction_id'];
	$bank_id = $_POST['bank_id'];	

	$refund_date = date('Y-m-d', strtotime($refund_date));
	$year2 = explode("-", $refund_date);
	$yr1 =$year2[0];

	global $transaction_master;

	$sq_b2c_info = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
	$customer_id = $sq_b2c_info['customer_id'];
	$year = explode("-", $sq_b2c_info['created_at']);
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

	////////Refund Amount//////
    $module_name = "B2C Booking Refund Paid";
    $module_entry_id = $booking_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = get_refund_paid_particular(get_b2c_booking_id($booking_id,$yr), $refund_date, $refund_amount, $refund_mode,get_b2c_booking_refund_id($refund_id,$yr1));
    $ledger_particular = '';
    $gl_id = $pay_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

	////////Refund Amount//////
    $module_name = "B2C Booking Refund Paid";
    $module_entry_id = $booking_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = get_refund_paid_particular(get_b2c_booking_id($booking_id,$yr), $refund_date, $refund_amount, $refund_mode,get_b2c_booking_refund_id($refund_id,$yr1));
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

}


public function bank_cash_book_save($refund_id)
{
	$booking_id = $_POST['booking_id'];
	$refund_charges = $_POST['refund_charges'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$bank_name = $_POST['bank_name'];
	$transaction_id = $_POST['transaction_id'];	
	$bank_id = $_POST['bank_id'];
	$refund_date = date('Y-m-d', strtotime($refund_date));
	$year2 = explode("-", $refund_date);
	$yr1 =$year2[0];

	$sq_b2c_info = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
	$year = explode("-", $sq_b2c_info['created_at']);
	$yr =$year[0];

	global $bank_cash_book_master;

	$module_name = "B2C Booking Refund Paid";
	$module_entry_id = $refund_id;
	$payment_date = $refund_date;
	$payment_amount = $refund_amount;
	$payment_mode = $refund_mode;
	$bank_name = $bank_name;
	$transaction_id = $transaction_id;
	$bank_id = $bank_id;
	$particular = get_refund_paid_particular(get_b2c_booking_id($booking_id,$yr), $refund_date, $refund_amount, $refund_mode, get_b2c_booking_refund_id($refund_id,$yr1));
	$clearance_status = ($payment_mode=="Cheque") ? "Pending" : "";
	$payment_side = "Credit";
	$payment_type = ($payment_mode=="Cash") ? "Cash" : "Bank";
	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type);

}

public function refund_mail_send($booking_id,$refund_amount,$refund_date,$refund_mode,$transaction_id){
	
	global $encrypt_decrypt,$secret_key,$currency;
	
	$sq_b2c_info = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
	$costing_data = json_decode($sq_b2c_info['costing_data']);
	$final_total = $costing_data[0]->net_total;

	$cust_email = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_b2c_info[customer_id]'"));
	$email_id = $encrypt_decrypt->fnDecrypt($cust_email['email_id'], $secret_key);
	$date = $sq_b2c_info['created_at'];
	$yr = explode("-", $date);
	$year =$yr[0];

	$sq_ref_pay_total = mysqli_fetch_assoc(mysqlQuery("select sum(refund_amount) as sum from b2c_booking_refund_master where booking_id='$booking_id' and clearance_status!='Cancelled'"));

	$refund_paid_amount = $sq_ref_pay_total['sum'];
	
	$sq_payment_info = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum from b2c_payment_master where booking_id='$booking_id' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

	$paid_amount = $sq_payment_info['sum'];
	$sale_amount = $final_total;
	$cancel_amount = $sq_b2c_info['cancel_amount'];
	$remaining = $sq_b2c_info['total_refund_amount'] - $refund_paid_amount;
	
	$sale_amount1 = currency_conversion($currency,$currency,$sale_amount);
	$paid_amount1 = currency_conversion($currency,$currency,$paid_amount);
	$cancel_amount1 = currency_conversion($currency,$currency,$cancel_amount);
	$refund_amount1 = currency_conversion($currency,$currency,$refund_amount);
	$remaining = currency_conversion($currency,$currency,$remaining);

	$content = '
	<tr>
	<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
			<tr><td style="text-align:left;border: 1px solid #888888;">Selling Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sale_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Paid Amount</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$paid_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Cancellation Charges</td>   <td style="text-align:left;border: 1px solid #888888;">'.$cancel_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_amount1.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Mode</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_mode.'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Refund Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.get_date_user($refund_date).'</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;">Pending Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$remaining.'</td></tr>
	</table>
	</tr>';

	$content .= '</tr>';
	$subject = 'B2C Cancellation Refund ( '.get_b2c_booking_id($booking_id,$year).' )';
	global $model;
	$model->app_email_send('39',$sq_b2c_info['name'],$email_id, $content,$subject);
	}
}
?>