<?php
$flag = true;
class cash_master{
////////////////////////////// CASH DEPOSIT START ///////////////////////////
public function cash_deposit_save()
{
	$bank_id = $_POST['bank_id'];
	$payment_amount = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$payment_evidence_url = $_POST['payment_evidence_url'];
	$branch_admin_id = $_POST['branch_admin_id'];
    $emp_id = $_POST['emp_id']; 
    $financial_year_id = $_SESSION['financial_year_id'];

	$created_at = date('Y-m-d H:i');
    $payment_date = date('Y-m-d', strtotime($payment_date));

	begin_t();

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(deposit_id) as max from cash_deposit_master"));
	$deposit_id = $sq_max['max'] + 1;
	$sq_deposit = mysqlQuery("insert into cash_deposit_master (deposit_id, bank_id, amount, evidence_url, transaction_date, branch_admin_id, emp_id, financial_year_id, created_at) values ('$deposit_id', '$bank_id', '$payment_amount', '$payment_evidence_url', '$payment_date', '$branch_admin_id','$emp_id' ,'$financial_year_id','$created_at') ");

	if($sq_deposit){
		//Finance save
    	$this->finance_save($deposit_id,$branch_admin_id);
		//Bank and Cash Book Save
		$this->bank_cash_book_save($deposit_id);

	    if($GLOBALS['flag']){
			commit_t();
			echo "Cash Deposit has been successfully saved.";
			exit;
	    }
	    else{
			rollback_t();
			exit;
	    }
	}
	else{
		rollback_t();
		echo "error--Cash Deposit Not Done!";
		exit;
	}
}
function cash_deposit_delete(){

	global $delete_master,$transaction_master,$bank_cash_book_master;
	$row_spec = 'bank';
	$deposit_id = $_POST['deposit_id'];
	$branch_admin_id = $_SESSION['branch_admin_id'];
	$deleted_date = date('Y-m-d');

	$sq_deposit = mysqli_fetch_assoc(mysqlQuery("select * from cash_deposit_master where deposit_id='$deposit_id'"));
	$sq_banks = mysqli_fetch_assoc(mysqlQuery("select bank_name,branch_name from bank_master where bank_id='$sq_deposit[bank_id]'"));
	$bank_id = $sq_deposit['bank_id'];

	$trans_id = $deposit_id.' : '.$sq_banks['bank_name'].' ('.$sq_banks['branch_name'].')';
	$transaction_master->updated_entries('Cash Deposit',$deposit_id,$trans_id,$sq_deposit['amount'],0);

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

	$delete_master->delete_master_entries('Bank Receipt','Cash Deposit',$deposit_id,$deposit_id,$sq_banks['bank_name'].' ('.$sq_banks['branch_name'].')',$sq_deposit['amount']);

	//Finance Master
    ////////////Basic deposit Amount/////////////
    $module_name = "Cash Deposit";
    $module_entry_id = $deposit_id;
    $transaction_id = "";
    $payment_amount = 0;
    $payment_date = $deleted_date;
    $payment_particular = get_cash_deposit_particular($bank_id,$deleted_date);    
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $old_gl_id = $gl_id = '20';
    $payment_side = "Credit";
    $clearance_status = "";
	$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec, $ledger_particular,'BANK RECEIPT');

    /////////Bank////////
    $module_name = "Cash Deposit";
    $module_entry_id = $deposit_id;
    $transaction_id = "";
    $payment_amount = 0;
    $payment_date = $deleted_date;
    $payment_particular = get_cash_deposit_particular($bank_id,$deleted_date);
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $old_gl_id = $gl_id = $pay_gl;
    $payment_side = "Debit";
    $clearance_status = "";
	$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec, $ledger_particular,'BANK RECEIPT');

	// Bank Cash Book Master
	$module_name = "Cash Deposit";
	$module_entry_id = $deposit_id;
	$payment_date = $deleted_date;
	$payment_amount = 0;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_deposit_particular($bank_id,$deleted_date);
	$clearance_status = "";
	$payment_side = "Credit";
	$payment_type = "Cash";

	$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$module_name = "Cash Deposit";
	$module_entry_id = $deposit_id;
	$payment_date = $deleted_date;
	$payment_amount = 0;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_deposit_particular($bank_id,$deleted_date);
	$clearance_status = "";
	$payment_side = "Debit";
	$payment_type = "Bank";

	$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$sq_delete = mysqlQuery("update cash_deposit_master set amount = '0',delete_status = '1' where deposit_id='$deposit_id'");
	if($sq_delete){
		echo 'Entry deleted successfully!';
		exit;
	}
}

public function finance_save($deposit_id,$branch_admin_id)
{
	$row_spec = 'bank';
	$branch_admin_id = $_POST['branch_admin_id'];
	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = $_POST['branch_admin_id'];

    $payment_date1 = date('Y-m-d', strtotime($payment_date));

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

    global $transaction_master;

    ////////////Basic deposit Amount/////////////
    $module_name = "Cash Deposit";
    $module_entry_id = $deposit_id;
    $transaction_id = "";
    $payment_amount = $payment_amount1;
    $payment_date = $payment_date1;
    $payment_particular = get_cash_deposit_particular($bank_id,$payment_date1);    
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $gl_id = '20';
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK RECEIPT');

    /////////bank////////
    $module_name = "Cash Deposit";
    $module_entry_id = $deposit_id;
    $transaction_id = "";
    $payment_amount = $payment_amount1;
    $payment_date = $payment_date1;
    $payment_particular = get_cash_deposit_particular($bank_id,$payment_date1);
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $gl_id = $pay_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK RECEIPT');

}

public function bank_cash_book_save($payment_id)
{
	global $bank_cash_book_master;

	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = $_POST['branch_admin_id'];

	$payment_date = date("Y-m-d", strtotime($payment_date));

	$module_name = "Cash Deposit";
	$module_entry_id = $payment_id;
	$payment_date = $payment_date;
	$payment_amount = $payment_amount1;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_deposit_particular($bank_id,$payment_date);
	$clearance_status = "";
	$payment_side = "Credit";
	$payment_type = "Cash";

	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$module_name = "Cash Deposit";
	$module_entry_id = $payment_id;
	$payment_date = $payment_date;
	$payment_amount = $payment_amount1;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = $transaction_id;
	$bank_id = $bank_id;
	$particular = get_cash_deposit_particular($bank_id,$payment_date);
	$clearance_status = "";
	$payment_side = "Debit";
	$payment_type = "Bank";

	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);
}

public function cash_deposit_update()
{
	$bank_id = $_POST['bank_id'];
	$deposit_id = $_POST['deposit_id'];
	$payment_amount = $_POST['payment_amount'];
	$payment_evidence_url = $_POST['payment_evidence_url'];
	$payment_old_amount = $_POST['payment_old_amount'];

	begin_t();
	$sq_deposit_u = mysqlQuery("update cash_deposit_master set amount = '$payment_amount', evidence_url = '$payment_evidence_url' where deposit_id = '$deposit_id'");

	if($sq_deposit_u){
		//Finance save
    	$this->finance_update($deposit_id);
		//Bank and Cash Book Save
		$this->bank_cash_book_update($deposit_id);

		if($payment_old_amount != $payment_amount){

			global $transaction_master;
			$sq_package = mysqli_fetch_assoc(mysqlQuery("select bank_name,branch_name from bank_master where bank_id='$bank_id'"));
		
			$trans_id = $deposit_id.' : '.$sq_package['bank_name'].' ('.$sq_package['branch_name'].')';
			$transaction_master->updated_entries('Cash Deposit',$deposit_id,$trans_id,$payment_old_amount,$payment_amount);
		}

	    if($GLOBALS['flag']){
			commit_t();
			echo "Cash Deposit has been successfully updated.";
			exit;
		}
		else{
			rollback_t();
			exit;
	    }
	}
	else{
		rollback_t();
		echo "error--Deposit not updated!";
		exit;
	}
}

public function finance_update($deposit_id)
{
	$row_spec = 'bank';
	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$payment_old_amount = $_POST['payment_old_amount'];
	$branch_admin_id = $_SESSION['branch_admin_id'];
    $payment_date1 = date('Y-m-d', strtotime($payment_date));

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

    global $transaction_master;

    if($payment_amount1 == '0' && ($payment_old_amount != $payment_amount1))
    {
	    ////////////Basic deposit /////////////
	    $module_name = "Cash Deposit";
	    $module_entry_id = $deposit_id;
	    $transaction_id = "";
	    $payment_amount = $payment_old_amount;
	    $payment_date = $payment_date1;
	    $payment_particular = get_cash_deposit_particular($bank_id,$payment_date1);
	    $ledger_particular = get_ledger_particular('By','Cash/Bank');
	    $gl_id = '20';
	    $payment_side = "Debit";
	    $clearance_status = "";
	    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK RECEIPT');

	    /////////bank////////
	    $module_name = "Cash Deposit";
	    $module_entry_id = $deposit_id;
	    $transaction_id = "";
	    $payment_amount = $payment_old_amount;
	    $payment_date = $payment_date1;
	    $payment_particular = get_cash_deposit_particular($bank_id,$payment_date1);
	    $ledger_particular = get_ledger_particular('By','Cash/Bank');
	    $gl_id = $pay_gl;
	    $payment_side = "Credit";
	    $clearance_status = "";
	    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK RECEIPT');

    }
}

public function bank_cash_book_update($payment_id)
{
	global $bank_cash_book_master;

	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = isset($_POST['branch_admin_id']) ? $_POST['branch_admin_id'] : '';
	$payment_old_amount = $_POST['payment_old_amount'];

	$payment_date = date("Y-m-d", strtotime($payment_date));

    if($payment_amount1 == '0' && ($payment_old_amount != $payment_amount1))
    {
		$module_name = "Cash Deposit";
		$module_entry_id = $payment_id;
		$payment_date = $payment_date;
		$payment_amount = $payment_amount1;
		$payment_mode = '';
		$bank_name = '';
		$transaction_id = '';
		$bank_id = $bank_id;
		$particular = get_cash_deposit_particular($bank_id,$payment_date);
		$clearance_status = "";
		$payment_side = "Credit";
		$payment_type = "Cash";

		$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

		$module_name = "Cash Deposit";
		$module_entry_id = $payment_id;
		$payment_date = $payment_date;
		$payment_amount = $payment_amount1;
		$payment_mode = '';
		$bank_name = '';
		$transaction_id = '';
		$bank_id = $bank_id;
		$particular = get_cash_deposit_particular($bank_id,$payment_date);
		$clearance_status = "";
		$payment_side = "Debit";
		$payment_type = "Bank";

		$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);
	}
}
///////////////////////////// CASH DEPOSIT END/////////////////////////

///////////////////////////// CASH WITHDRAW START/////////////////////////
public function cash_withdraw_save()
{
	$bank_id = $_POST['bank_id'];
	$payment_amount = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$payment_evidence_url = $_POST['payment_evidence_url'];
	$branch_admin_id = $_POST['branch_admin_id'];
    $emp_id = $_POST['emp_id']; 
    $financial_year_id = $_SESSION['financial_year_id'];

	$created_at = date('Y-m-d H:i');
    $payment_date = date('Y-m-d', strtotime($payment_date));

	begin_t();

	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(withdraw_id) as max from cash_withdraw_master"));
	$withdraw_id = $sq_max['max'] + 1;
	$sq_deposit = mysqlQuery("insert into cash_withdraw_master (withdraw_id, bank_id, amount, evidence_url, transaction_date, branch_admin_id, emp_id, financial_year_id, created_at) values ('$withdraw_id', '$bank_id', '$payment_amount', '$payment_evidence_url', '$payment_date', '$branch_admin_id','$emp_id' ,'$financial_year_id','$created_at') ");

	if($sq_deposit){
		//Finance save
		$this->finance_save_withdraw($withdraw_id,$branch_admin_id);
		$this->cbank_cash_book_save($withdraw_id);

	    if($GLOBALS['flag']){
	      commit_t();
	      echo "Cash Withdrawal has been successfully saved.";
	      exit;
	    }
	    else{
	      rollback_t();
	      exit;
	    }
	}
	else{
		rollback_t();
		echo "error--Cash Withdrawal Not Done!";
		exit;
	}
}

function cash_withdrawal_delete(){

	global $delete_master,$transaction_master,$bank_cash_book_master;
	$row_spec = 'bank';
	$withdraw_id = $_POST['withdraw_id'];
	$branch_admin_id = $_SESSION['branch_admin_id'];
	$deleted_date = date('Y-m-d');

	$sq_withdraw = mysqli_fetch_assoc(mysqlQuery("select * from cash_withdraw_master where withdraw_id='$withdraw_id'"));
	$sq_banks = mysqli_fetch_assoc(mysqlQuery("select bank_name,branch_name from bank_master where bank_id='$sq_withdraw[bank_id]'"));
	$bank_id = $sq_withdraw['bank_id'];

	$trans_id = $withdraw_id.' : '.$sq_banks['bank_name'].' ('.$sq_banks['branch_name'].')';
	$transaction_master->updated_entries('Cash Withdraw',$withdraw_id,$trans_id,$sq_withdraw['amount'],0);

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

	$delete_master->delete_master_entries('Bank Payment','Cash Withdrawal',$withdraw_id,$withdraw_id,$sq_banks['bank_name'].' ('.$sq_banks['branch_name'].')',$sq_withdraw['amount']);

	//Finance Master
    ////////////Basic deposit Amount/////////////
    $module_name = "Cash Withdraw";
    $module_entry_id = $withdraw_id;
    $transaction_id = "";
    $payment_amount = 0;
    $payment_date = $deleted_date;
    $payment_particular = get_cash_withdraw_particular($bank_id,$deleted_date);    
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $old_gl_id = $gl_id = '20';
    $payment_side = "Debit";
    $clearance_status = "";
	$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec, $ledger_particular,'BANK PAYMENT');

    /////////Bank////////
    $module_name = "Cash Withdraw";
    $module_entry_id = $withdraw_id;
    $transaction_id = "";
    $payment_amount = 0;
    $payment_date = $deleted_date;
    $payment_particular = get_cash_withdraw_particular($bank_id,$deleted_date);
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $old_gl_id = $gl_id = $pay_gl;
    $payment_side = "Credit";
    $clearance_status = "";
	$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec, $ledger_particular,'BANK PAYMENT');

	// Bank Cash Book Master
	$module_name = "Cash Withdraw";
	$module_entry_id = $withdraw_id;
	$payment_date = $deleted_date;
	$payment_amount = 0;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_withdraw_particular($bank_id,$deleted_date);
	$clearance_status = "";
	$payment_side = "Debit";
	$payment_type = "Cash";

	$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$module_name = "Cash Withdraw";
	$module_entry_id = $withdraw_id;
	$payment_date = $deleted_date;
	$payment_amount = 0;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_withdraw_particular($bank_id,$deleted_date);
	$clearance_status = "";
	$payment_side = "Credit";
	$payment_type = "Bank";

	$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$sq_delete = mysqlQuery("update cash_withdraw_master set amount = '0',delete_status = '1' where withdraw_id='$withdraw_id'");
	if($sq_delete){
		echo 'Entry deleted successfully!';
		exit;
	}
}

public function finance_save_withdraw($withdraw_id,$branch_admin_id)
{
	$row_spec = 'bank';
	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = $_POST['branch_admin_id'];
    $payment_date1 = date('Y-m-d', strtotime($payment_date));

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

    global $transaction_master;

    ////////////Basic deposit Amount/////////////

    $module_name = "Cash Withdraw";
    $module_entry_id = $withdraw_id;
    $transaction_id = "";
    $payment_amount = $payment_amount1;
    $payment_date = $payment_date1;
    $payment_particular = get_cash_withdraw_particular($bank_id,$payment_date1);    
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $gl_id = '20';
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK PAYMENT');

    /////////bank////////
    $module_name = "Cash Withdraw";
    $module_entry_id = $withdraw_id;
    $transaction_id = "";
    $payment_amount = $payment_amount1;
    $payment_date = $payment_date1;
    $payment_particular = get_cash_withdraw_particular($bank_id,$payment_date1);
    $ledger_particular = get_ledger_particular('By','Cash/Bank');
    $gl_id = $pay_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK PAYMENT');

}

public function cbank_cash_book_save($payment_id)
{
	global $bank_cash_book_master;

	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = $_POST['branch_admin_id'];

	$payment_date = date("Y-m-d", strtotime($payment_date));

	$module_name = "Cash Withdraw";
	$module_entry_id = $payment_id;
	$payment_date = $payment_date;
	$payment_amount = $payment_amount1;
	$payment_mode = '';
	$bank_name = '';
	$transaction_id = '';
	$bank_id = $bank_id;
	$particular = get_cash_withdraw_particular($bank_id,$payment_date);
	$clearance_status = "";
	$payment_side = "Debit";
	$payment_type = "Cash";

	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

	$module_name = "Cash Withdraw";
	$module_entry_id = $payment_id;
	$payment_date = $payment_date;
	$payment_amount = $payment_amount1;
	$payment_mode = '';
	$bank_name = $bank_name;
	$transaction_id = $transaction_id;
	$bank_id = $bank_id;
	$particular = get_cash_withdraw_particular($bank_id,$payment_date);
	$clearance_status = "";
	$payment_side = "Credit";
	$payment_type = "Bank";

	$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);
}

public function cash_withdraw_update()
{
	$bank_id = $_POST['bank_id'];
	$withdraw_id = $_POST['withdraw_id'];
	$payment_amount = $_POST['payment_amount'];
	$payment_evidence_url = $_POST['payment_evidence_url'];
	$payment_old_amount = $_POST['payment_old_amount'];

	begin_t();
	$sq_deposit_u = mysqlQuery("update cash_withdraw_master set amount='$payment_amount',evidence_url='$payment_evidence_url' where withdraw_id='$withdraw_id'");

	if($sq_deposit_u){
		//Finance save
		$this->finance_update_withdraw($withdraw_id);
		$this->cbank_cash_book_update($withdraw_id);

		if($payment_old_amount != $payment_amount){

			global $transaction_master;
			$sq_package = mysqli_fetch_assoc(mysqlQuery("select bank_name,branch_name from bank_master where bank_id='$bank_id'"));
		
			$trans_id = $withdraw_id.' : '.$sq_package['bank_name'].' ('.$sq_package['branch_name'].')';
			$transaction_master->updated_entries('Cash Withdraw',$withdraw_id,$trans_id,$payment_old_amount,$payment_amount);
		}
	    if($GLOBALS['flag']){
	      commit_t();
	      echo "Cash Withdrawal has been successfully updated.";
	      exit;
	    }
	    else{
	      rollback_t();
	      exit;
	    }
	}
	else{
		rollback_t();
		echo "error--Cash Withdrawal not updated!";
		exit;
	}

}


public function finance_update_withdraw($withdraw_id)
{
	$row_spec = 'bank';
	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$payment_old_amount = $_POST['payment_old_amount'];
	$branch_admin_id = $_SESSION['branch_admin_id'];

    $payment_date1 = date('Y-m-d', strtotime($payment_date));

    //BANK Ledger
    $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
    $pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';

    global $transaction_master;

    if($payment_amount1 == '0' && ($payment_old_amount != $payment_amount1))
    {
	    ////////////Basic deposit /////////////
	    $module_name = "Cash Withdraw";
	    $module_entry_id = $withdraw_id;
	    $transaction_id = "";
	    $payment_amount = $payment_old_amount;
	    $payment_date = $payment_date1;
	    $payment_particular = get_cash_withdraw_particular($bank_id,$payment_date1);    
	    $ledger_particular = get_ledger_particular('By','Cash/Bank');
	    $gl_id = '20';
	    $payment_side = "Credit";
	    $clearance_status = "";
	    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK PAYMENT');

	    /////////Bank////////
	    $module_name = "Cash Withdraw";
	    $module_entry_id = $withdraw_id;
	    $transaction_id = "";
	    $payment_amount = $payment_old_amount;
	    $payment_date = $payment_date1;
	    $payment_particular = get_cash_withdraw_particular($bank_id,$payment_date1);
	    $ledger_particular = get_ledger_particular('By','Cash/Bank');
	    $gl_id = $pay_gl;
	    $payment_side = "Debit";
	    $clearance_status = "";
	    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,'BANK PAYMENT');
    }
}

public function cbank_cash_book_update($payment_id)
{
	global $bank_cash_book_master;

	$bank_id = $_POST['bank_id'];
	$payment_amount1 = $_POST['payment_amount'];
	$payment_date = $_POST['payment_date'];
	$branch_admin_id = isset($_POST['branch_admin_id']) ? $_POST['branch_admin_id'] : '';
	$payment_old_amount = $_POST['payment_old_amount'];

	$payment_date = date("Y-m-d", strtotime($payment_date));

    if($payment_amount1 == '0' && ($payment_old_amount != $payment_amount1))
    {
		$module_name = "Cash Withdraw";
		$module_entry_id = $payment_id;
		$payment_date = $payment_date;
		$payment_amount = $payment_amount1;
		$payment_mode = '';
		$bank_name = '';
		$transaction_id = '';
		$bank_id = $bank_id;
		$particular = get_cash_withdraw_particular($bank_id,$payment_date);
		$clearance_status = "";
		$payment_side = "Debit";
		$payment_type = "Cash";

		$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);

		$module_name = "Cash Withdraw";
		$module_entry_id = $payment_id;
		$payment_date = $payment_date;
		$payment_amount = $payment_amount1;
		$payment_mode = '';
		$bank_name = $bank_name;
		$transaction_id = $transaction_id;
		$bank_id = $bank_id;
		$particular = get_cash_withdraw_particular($bank_id,$payment_date);
		$clearance_status = "";
		$payment_side = "Credit";
		$payment_type = "Bank";

		$bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,$branch_admin_id);
	}
}

///////////////////////////// CASH WITHDRAW END/////////////////////////
}

?>