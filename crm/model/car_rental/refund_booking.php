<?php
$flag = true;
class refund_booking{

public function refund_booking_save(){
	$booking_id = $_POST['booking_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$bank_name = $_POST['bank_name'];
	$transaction_id = $_POST['transaction_id'];	
	$bank_id = $_POST['bank_id'];

	$refund_date = date('Y-m-d', strtotime($refund_date));
	$created_at = date('Y-m-d H:i');

	
	if($refund_mode=="Cheque"){ 
		$clearance_status = "Pending"; } 
	else {  $clearance_status = ""; }	 
    
	$financial_year_id = $_SESSION['financial_year_id'];  
	$branch_admin_id = $_SESSION['branch_admin_id'];

	begin_t();

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(refund_id) as max from car_rental_refund_master"));
	$refund_id = $sq_max['max'] + 1;

	$sq_payment = mysqlQuery("insert into car_rental_refund_master (refund_id, booking_id, financial_year_id, refund_date, refund_amount, refund_mode, bank_name, transaction_id, bank_id, clearance_status, created_at) values ('$refund_id', '$booking_id', '$financial_year_id', '$refund_date', '$refund_amount', '$refund_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$created_at') ");

	if($refund_mode == 'Credit Note'){
		$sq_car_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id'"));
		$customer_id = $sq_car_info['customer_id'];
		
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from credit_note_master"));
		$id = $sq_max['max'] + 1;

		$sq_payment = mysqlQuery("insert into credit_note_master (id, financial_year_id, module_name, module_entry_id, customer_id, payment_amount,refund_id,created_at,branch_admin_id) values ('$id', '$financial_year_id', 'Car Rental Booking', '$booking_id', '$customer_id','$refund_amount','$refund_id','$refund_date','$branch_admin_id') ");
	}
	if(!$sq_payment){
		rollback_t();
		echo "error--Sorry, Refund not saved!";
		exit;
	}
	else{
		if($refund_mode != 'Credit Note'){
			//Finance save
	    	$this->finance_save($refund_id);
			//Bank and Cash Book Save
			$this->bank_cash_book_save($refund_id);
	    }
		//Refund cancellation mail
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
	$year1 = explode("-", $refund_date);
	$yr1 =$year1[0];

	global $transaction_master;

	$sq_car_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id'"));
  	$customer_id = $sq_car_info['customer_id'];

	//Particular
	$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_cust['type']== 'Corporate' || $sq_cust['type']== 'B2B'){
	  $cust_name = $sq_cust['company_name'];
	}else{
	  $cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	}
	$sq_book = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id'"));
	$particular = 'Payment through '.$refund_mode.' for '.get_car_rental_booking_id($booking_id,$yr1).' for the charge of '.$cust_name.' on '.$sq_book['vehicle_name'].' Dt.'.get_date_user($sq_book['created_at']);

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
    $module_name = "Car Rental Booking Refund Paid";
    $module_entry_id = $booking_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $pay_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

	////////Refund Amount//////
    $module_name = "Car Rental Booking Refund Paid";
    $module_entry_id = $booking_id;
    $transaction_id = $transaction_id;
    $payment_amount = $refund_amount;
    $payment_date = $refund_date;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,'',$ledger_particular,$type);  

}


public function bank_cash_book_save($refund_id)
{
	$booking_id = $_POST['booking_id'];
	$refund_date = $_POST['refund_date'];
	$refund_amount = $_POST['refund_amount'];
	$refund_mode = $_POST['refund_mode'];
	$bank_name = $_POST['bank_name'];
	$transaction_id = $_POST['transaction_id'];	
	$bank_id = $_POST['bank_id'];
	
	$refund_date = date('Y-m-d', strtotime($refund_date));
	$year1 = explode("-", $refund_date);
	$yr1 =$year1[0];

	//Particular
	$sq_car_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id'"));
	$customer_id = $sq_car_info['customer_id'];
	$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_cust['type']== 'Corporate' || $sq_cust['type']== 'B2B'){
	  $cust_name = $sq_cust['company_name'];
	}else{
	  $cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	}
	$particular = 'Payment through '.$refund_mode.' for '.get_car_rental_booking_id($booking_id,$yr1).' for the charge of '.$cust_name.' on '.$sq_car_info['vehicle_name'].' Dt.'.get_date_user($sq_car_info['created_at']);

	global $bank_cash_book_master;

	$module_name = "Car Rental Booking Refund Paid";
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

public function refund_mail_send($booking_id,$refund_amount,$refund_date,$refund_mode,$transaction_id){

	global $encrypt_decrypt,$secret_key,$currency_logo,$currency;
	
	$sq_sq_train_info = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id'"));
	$date = $sq_sq_train_info['created_at'];
	$yr = explode("-", $date);
	$year =$yr[0];
	$cust_email = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_sq_train_info[customer_id]'"));
	$email_id = $encrypt_decrypt->fnDecrypt($cust_email['email_id'], $secret_key);
	if($cust_email['type']=='Corporate'||$cust_email['type'] == 'B2B'){
		$customer_name = $cust_email['company_name'];
	}else{
		$customer_name = $cust_email['first_name'].' '.$cust_email['last_name'];
	}
	$sq_payment_info = mysqli_fetch_array(mysqlQuery("SELECT sum(payment_amount) as sum from car_rental_payment where booking_id='$booking_id' AND clearance_status!='Pending' AND clearance_status!='Cancelled'"));

	$sq_pay = mysqli_fetch_assoc(mysqlQuery("select sum(refund_amount) as sum from car_rental_refund_master where booking_id='$booking_id' AND clearance_status!='Cancelled'"));
	$remaining = $sq_sq_train_info['total_refund_amount']- $sq_pay['sum'];

	$sale_amount = currency_conversion($currency,$currency,$sq_sq_train_info['total_fees']);
	$paid_amount = currency_conversion($currency,$currency,$sq_payment_info['sum']);
	$cancel_amount = currency_conversion($currency,$currency,$sq_sq_train_info['cancel_amount']);
	$refund_amount = currency_conversion($currency,$currency,$refund_amount);
	$remaining = currency_conversion($currency,$currency,$remaining);

	$content = '
	<tr>
	<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
		<tr><td style="text-align:left;border: 1px solid #888888;">Service Type</td>   <td style="text-align:left;border: 1px solid #888888;">Car Rental Booking</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Selling Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sale_amount.'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Paid Amount</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$paid_amount.'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Cancellation Charges</td>   <td style="text-align:left;border: 1px solid #888888;">'.$cancel_amount.'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_amount.'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Refund Mode</td>   <td style="text-align:left;border: 1px solid #888888;">'.$refund_mode.'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Refund Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.get_date_user($refund_date).'</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;">Pending Refund Amount</td>   <td style="text-align:left;border: 1px solid #888888;">'.$remaining.'</td></tr>
	</table>
	</tr>';	 
			$content .= '</tr>';
	$subject = 'Car rental Cancellation Refund( '.get_car_rental_booking_id($sq_sq_train_info['booking_id'],$year).' )';
	global $model;
	
	$model->app_email_send('41',$customer_name,$email_id, $content, $subject);
}

}

?>