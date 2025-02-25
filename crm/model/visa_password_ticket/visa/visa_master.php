<?php

$flag = true;

class visa_master
{
	public function visa_master_save()
	{
		$row_spec = 'sales';
		$customer_id = $_POST['customer_id'];
		$emp_id = $_POST['emp_id'];
		$visa_issue_amount = $_POST['visa_issue_amount'];
		$branch_admin_id = $_POST['branch_admin_id'];
		$financial_year_id = $_POST['financial_year_id'];
		$service_charge = $_POST['service_charge'];
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$visa_total_cost = $_POST['visa_total_cost'];
		$roundoff = $_POST['roundoff'];
		$due_date = $_POST['due_date'];
		$balance_date = $_POST['balance_date'];

		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$transaction_id = $_POST['transaction_id'];
		$bank_id = $_POST['bank_id'];
		$markup = $_POST['markup'];
		$service_tax_markup = $_POST['service_tax_markup'];
		$reflections = json_decode(json_encode($_POST['reflections']));

		$first_name_arr = isset($_POST['first_name_arr']) ? $_POST['first_name_arr'] : [];
		$middle_name_arr = isset($_POST['middle_name_arr']) ? $_POST['middle_name_arr'] : [];
		$last_name_arr = isset($_POST['last_name_arr']) ? $_POST['last_name_arr'] : [];
		$birth_date_arr = isset($_POST['birth_date_arr']) ? $_POST['birth_date_arr'] : [];
		$adolescence_arr = isset($_POST['adolescence_arr']) ? $_POST['adolescence_arr'] : [];
		$visa_country_name_arr = isset($_POST['visa_country_name_arr']) ? $_POST['visa_country_name_arr'] : [];
		$visa_type_arr = isset($_POST['visa_type_arr']) ? $_POST['visa_type_arr'] : [];
		$passport_id_arr = isset($_POST['passport_id_arr']) ? $_POST['passport_id_arr'] : [];
		$issue_date_arr = isset($_POST['issue_date_arr']) ? $_POST['issue_date_arr'] : [];
		$expiry_date_arr = isset($_POST['expiry_date_arr']) ? $_POST['expiry_date_arr'] : [];
		$nationality_arr = isset($_POST['nationality_arr']) ? $_POST['nationality_arr'] : [];
		$appointment_date_arr = isset($_POST['appointment_date_arr']) ? $_POST['appointment_date_arr'] : [];
		$credit_charges = isset($_POST['credit_charges']) ? $_POST['credit_charges'] : 0;
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';
		$currency_code = isset($_POST['currency_code']) ? $_POST['currency_code'] : '';

		$payment_date = date('Y-m-d', strtotime($payment_date));
		$balance_date = date("Y-m-d", strtotime($balance_date));
		$due_date = date("Y-m-d", strtotime($due_date));

		if ($payment_mode == 'Cheque' || $payment_mode == 'Credit Card') {
			$clearance_status = "Pending";
		} else {
			$clearance_status = "";
		}

		$financial_year_id = $_SESSION['financial_year_id'];
		begin_t();

		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}

		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$visa_issue_amount = ($value != "") ? $value : $visa_issue_amount;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
				case 'markup':
					$markup = ($value != "") ? $value : $markup;
					break;
			}
		}
		//Invoice number reset to one in new financial year
		$sq_count = mysqli_num_rows(mysqlQuery("select entry_id from invoice_no_reset_master where service_name='visa' and financial_year_id='$financial_year_id'"));
		if($sq_count > 0){ // Already having bookings for this financial year
		
			$sq_invoice = mysqli_fetch_assoc(mysqlQuery("select max_booking_id from invoice_no_reset_master where service_name='visa' and financial_year_id='$financial_year_id'"));
			$invoice_pr_id = $sq_invoice['max_booking_id'] + 1;
			$sq_invoice = mysqlQuery("update invoice_no_reset_master set max_booking_id = '$invoice_pr_id' where service_name='visa' and financial_year_id='$financial_year_id'");
		}
		else{ // This financial year's first booking
		
			// Get max entry_id of invoice_no_reset_master here
			$sq_entry_id = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as entry_id from invoice_no_reset_master"));
			$max_entry_id = $sq_entry_id['entry_id'] + 1;
			
			// Insert booking-id(1) for new financial_year only for first the time
			$sq_invoice = mysqlQuery("insert into invoice_no_reset_master(entry_id ,service_name, financial_year_id ,max_booking_id) values ('$max_entry_id','visa','$financial_year_id','1')");
			$invoice_pr_id = 1;
		}
		//visa save
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(visa_id) as max from visa_master"));
		$visa_id = $sq_max['max'] + 1;

		$reflections = json_encode($reflections);
		$bsmValues = json_encode($bsmValues);

		$sq_visa = mysqlQuery("insert into visa_master (visa_id, customer_id,branch_admin_id,financial_year_id, visa_issue_amount, service_charge, service_tax_subtotal, visa_total_cost, roundoff, markup, markup_tax, reflections,created_at, due_date,emp_id, bsm_values, currency_code,invoice_pr_id) values ('$visa_id', '$customer_id', '$branch_admin_id','$financial_year_id', '$visa_issue_amount', '$service_charge', '$service_tax_subtotal', '$visa_total_cost', '$roundoff','$markup','$service_tax_markup','$reflections','$balance_date', '$due_date', '$emp_id', '$bsmValues','$currency_code','$invoice_pr_id')");

		if (!$sq_visa) {
			rollback_t();
			echo "error--Sorry visa information not saved successfully!";
			exit;
		} else {
			for ($i = 0; $i < sizeof($first_name_arr); $i++) {

				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from visa_master_entries"));
				$entry_id = $sq_max['max'] + 1;

				$birth_date_arr[$i] = get_date_db($birth_date_arr[$i]);
				$issue_date_arr[$i] = get_date_db($issue_date_arr[$i]);
				$expiry_date_arr[$i] = get_date_db($expiry_date_arr[$i]);
				$appointment_date_arr[$i] = get_date_db($appointment_date_arr[$i]);

				$sq_entry = mysqlQuery("insert into visa_master_entries(entry_id, visa_id, first_name, middle_name, last_name, birth_date, adolescence, visa_country_name, visa_type, passport_id, issue_date, expiry_date,nationality,appointment_date) values('$entry_id', '$visa_id', '$first_name_arr[$i]', '$middle_name_arr[$i]', '$last_name_arr[$i]', '$birth_date_arr[$i]', '$adolescence_arr[$i]', '$visa_country_name_arr[$i]', '$visa_type_arr[$i]', '$passport_id_arr[$i]', '$issue_date_arr[$i]', '$expiry_date_arr[$i]', '$nationality_arr[$i]', '$appointment_date_arr[$i]')");

				if (!$sq_entry) {
					$GLOBALS['flag'] = false;
					echo "error--Some Visa entries are not saved!";
					//exit;
				}
			}

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from visa_payment_master"));
			$payment_id = $sq_max['max'] + 1;

			$sq_payment = mysqlQuery("insert into visa_payment_master (payment_id, visa_id, financial_year_id, branch_admin_id,  payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$visa_id', '$financial_year_id', '$branch_admin_id', '$payment_date', '$payment_amount', '$payment_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_charges','$credit_card_details') ");
			if (!$sq_payment) {
				$GLOBALS['flag'] = false;
				echo "error--Sorry, Payment not saved!";
			}

			//Update customer credit note balance
			$payment_amount1 = $payment_amount;
			if($payment_mode=='Credit Note'){
			$sq_credit_note = mysqlQuery("select * from credit_note_master where customer_id='$customer_id'");
			while ($row_credit = mysqli_fetch_assoc($sq_credit_note)) {
				if ($row_credit['payment_amount'] <= $payment_amount1 && $payment_amount1 != '0') {
					$payment_amount1 = $payment_amount1 - $row_credit['payment_amount'];
					$temp_amount = 0;
				} else {
					$temp_amount = $row_credit['payment_amount'] - $payment_amount1;
					$payment_amount1 = 0;
				}
				$sq_credit = mysqlQuery("update credit_note_master set payment_amount ='$temp_amount' where id='$row_credit[id]'");
			}
		}
		$row_visa_type = mysqli_fetch_assoc(mysqlQuery("select * from visa_master_entries where visa_id='$visa_id'"));
		$sq_pass_count = mysqli_num_rows(mysqlQuery("select * from  visa_master_entries where visa_id='$visa_id' and status!='Cancel'"));

		$pass_name = $row_visa_type['first_name'].' '.$row_visa_type['last_name'];
		$booking_date = $balance_date;
		$yr = explode("-", $booking_date);
		$year = $yr[0];

			//Get Particular
			$particular = $this->get_particular($customer_id, $visa_type_arr[0], get_visa_booking_id($visa_id,$year), $pass_name,$sq_pass_count);

				//Finance save
				$this->finance_save($visa_id, $payment_id, $row_spec, $branch_admin_id, $particular);
			if($payment_mode != 'Credit Note'){
				//Bank and Cash Book Save
				$this->bank_cash_book_save($visa_id, $payment_id, $branch_admin_id);
			}

			if ($GLOBALS['flag']) {

				commit_t();
				//Visa Booking email send
				$sq_cms_count = mysqli_num_rows(mysqlQuery("select * from cms_master_entries where id='11' and active_flag='Active'"));
				if ($sq_cms_count != '0') {
					$this->visa_booking_email_send($visa_id, $visa_country_name_arr, $visa_type_arr, $first_name_arr, $payment_amount);
				}
				$this->booking_sms($visa_id, $customer_id, $balance_date);

				//Visa payment email send
				$visa_payment_master  = new visa_payment_master;
				$visa_payment_master->payment_email_notification_send($visa_id, $payment_amount, $payment_mode, $payment_date);

				//Visa payment sms send
				if ($payment_amount != 0) {
					$visa_payment_master->payment_sms_notification_send($visa_id, $payment_amount, $payment_mode, $credit_charges);
				}

				echo "Visa Booking has been successfully saved-".$visa_id;
				exit;
			} else {

				rollback_t();

				exit;
			}
		}
	}

	public function visa_master_delete(){

		global $delete_master,$transaction_master;
		$visa_id = $_POST['booking_id'];

		$deleted_date = date('Y-m-d');
		$row_spec = "sales";
	
		$row_visa = mysqli_fetch_assoc(mysqlQuery("select * from visa_master where visa_id='$visa_id'"));
		$row_visa_type = mysqli_fetch_assoc(mysqlQuery("select * from visa_master_entries where visa_id='$visa_id'"));
		$sq_pass_count = mysqli_num_rows(mysqlQuery("select * from  visa_master_entries where visa_id='$row_visa[visa_id]' and status!='Cancel'"));

		$pass_name = $row_visa_type['first_name'].' '.$row_visa_type['last_name'];
		$reflections = json_decode($row_visa['reflections']);
		$service_tax_markup = $row_visa['markup_tax'];
		$service_tax_subtotal = $row_visa['service_tax_subtotal'];
		$customer_id = $row_visa['customer_id'];
		$booking_date = $row_visa['created_at'];
		$net_total = $row_visa['visa_total_cost'];
		$yr = explode("-", $booking_date);
		$year = $yr[0];
		
		$sq_ct = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		$cust_name = ($sq_ct['type']=='Corporate'||$sq_ct['type'] == 'B2B') ?$sq_ct['company_name'] : $sq_ct['first_name'].' '.$sq_ct['last_name'];

		$yr = explode("-", $booking_date);
		$year = $yr[0];

		$trans_id = get_visa_booking_id($visa_id,$year).' : '.$cust_name;
		$transaction_master->updated_entries('Visa Sale',$visa_id,$trans_id,$net_total,0);

		$particular = $this->get_particular($customer_id, $row_visa_type['visa_type'], get_visa_booking_id($visa_id,$year), $pass_name,$sq_pass_count);
		$delete_master->delete_master_entries('Invoice','Visa',$visa_id,get_visa_booking_id($visa_id,$year),$cust_name,$row_visa['visa_total_cost']);

		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
		$cust_gl = $sq_cust['ledger_id'];

		////////////Sales/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = 140;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		////////////service charge/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = ($reflections[0]->hotel_sc != '') ? $reflections[0]->hotel_sc : 188;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		/////////Service Charge Tax Amount////////
		$service_tax_subtotal = explode(',', $service_tax_subtotal);
		$tax_ledgers = explode(',', $reflections[0]->hotel_taxes);
		for ($i = 0; $i < sizeof($service_tax_subtotal); $i++) {

			$service_tax = explode(':', $service_tax_subtotal[$i]);
			$tax_amount = $service_tax[2];
			$ledger = $tax_ledgers[$i];

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = 0;
			$payment_date = $deleted_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$old_gl_id = $gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		}

		////////////markup/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = ($reflections[0]->hotel_markup != '') ? $reflections[0]->hotel_markup : 200;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		/////////Markup Tax Amount////////
		// Eg. CGST:(9%):24.77, SGST:(9%):24.77
		$service_tax_markup = explode(',', $service_tax_markup);
		$tax_ledgers = explode(',', $reflections[0]->hotel_markup_taxes);
		for ($i = 0; $i < sizeof($service_tax_markup); $i++) {

			$service_tax = explode(':', $service_tax_markup[$i]);
			$tax_amount = $service_tax[2];
			$ledger = $tax_ledgers[$i];

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = 0;
			$payment_date = $deleted_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$old_gl_id = $gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '1', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		}
		/////////roundoff/////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = 230;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		////////Customer Amount//////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = $cust_gl;
		$payment_side = "Debit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		
		$sq_delete = mysqlQuery("update visa_master set visa_issue_amount = '0',service_charge='0',markup='0',markup_tax='', service_tax_subtotal='', visa_total_cost='0', roundoff='0', delete_status='1' where visa_id='$visa_id'");
		if($sq_delete){
			echo 'Entry deleted successfully!';
			exit;
		}
	}
	public function booking_sms($booking_id, $customer_id, $created_at)
	{
		global $model, $encrypt_decrypt, $secret_key, $app_contact_no;
		$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		$mobile_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);

		$message = "Dear " . $sq_customer_info['first_name'] . " " . $sq_customer_info['last_name'] . ", your Visa Tour booking is confirmed. Please send your documents as earlier. Please contact for more details ." . $app_contact_no . "";
		$model->send_message($mobile_no, $message);
	}
	public function finance_save($visa_id, $payment_id, $row_spec, $branch_admin_id, $particular)
	{
		$customer_id = $_POST['customer_id'];
		$visa_issue_amount = $_POST['visa_issue_amount'];
		$service_charge = $_POST['service_charge'];
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$visa_total_cost = $_POST['visa_total_cost'];
		$roundoff = $_POST['roundoff'];
		$payment_date = $_POST['payment_date'];
		$payment_amount1 = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$transaction_id1 = $_POST['transaction_id'];
		$bank_id1 = $_POST['bank_id'];
		$booking_date = $_POST['balance_date'];
		$credit_charges = isset($_POST['credit_charges']) ? $_POST['credit_charges'] : 0;
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';
		$markup = $_POST['markup'];
		$service_tax_markup = $_POST['service_tax_markup'];

		$reflections = json_decode(json_encode($_POST['reflections']));

		$booking_date = date("Y-m-d", strtotime($booking_date));
		$payment_date1 = date('Y-m-d', strtotime($payment_date));
		$year1 = explode("-", $booking_date);
		$yr1 = $year1[0];

		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$visa_issue_amount = ($value != "") ? $value : $visa_issue_amount;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
				case 'markup':
					$markup = ($value != "") ? $value : $markup;
					break;
			}
		}

		$visa_sale_amount = $visa_issue_amount;
		$payment_amount1 = intval($payment_amount1) + intval($credit_charges);

		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}

		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
		$cust_gl = $sq_cust['ledger_id'];

		//Getting cash/Bank Ledger
		if ($payment_mode == 'Cash') {
			$pay_gl = 20;
			$type = 'CASH RECEIPT';
		} else {
			$sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id1' and user_type='bank'"));
			$pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';
			$type = 'BANK RECEIPT';
		}

		global $transaction_master;
		////////////Sales/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $visa_sale_amount;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$gl_id = 140;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		/////////Service Charge////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $service_charge;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$gl_id = ($reflections[0]->hotel_sc != '') ? $reflections[0]->hotel_sc : 188;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',  $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		/////////Service Charge Tax Amount////////
		$service_tax_subtotal = explode(',', $service_tax_subtotal);
		$tax_ledgers = explode(',', $reflections[0]->hotel_taxes);
		for ($i = 0; $i < sizeof($service_tax_subtotal); $i++) {

			$service_tax = explode(':', $service_tax_subtotal[$i]);
			$tax_amount = $service_tax[2];
			$ledger = $tax_ledgers[$i];

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = $tax_amount;
			$payment_date = $booking_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',  $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');
		}

		///////////Markup//////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $markup;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$gl_id = ($reflections[0]->hotel_markup != '') ? $reflections[0]->hotel_markup : 200;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',  $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		/////////Markup Tax Amount////////
		$service_tax_markup = explode(',', $service_tax_markup);
		$tax_ledgers = explode(',', $reflections[0]->hotel_markup_taxes);
		for ($i = 0; $i < sizeof($service_tax_markup); $i++) {

			$service_tax = explode(':', $service_tax_markup[$i]);
			$tax_amount = $service_tax[2];
			$ledger = $tax_ledgers[$i];

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = $tax_amount;
			$payment_date = $booking_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '1', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');
		}

		////////Customer Amount//////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $visa_total_cost;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$gl_id = $cust_gl;
		$payment_side = "Debit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		////Roundoff Value
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $roundoff;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$gl_id = 230;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		//////////Payment Amount///////////
		if ($payment_mode != 'Credit Note') {

			if ($payment_mode == 'Credit Card') {

				//////Customer Credit Charges///////
				$module_name = "Visa Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $credit_charges, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $cust_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				//////Credit Charges ledger///////
				$module_name = "Visa Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $credit_charges, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = 224;
				$payment_side = "Credit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				//////Get Credit card company Ledger///////
				$credit_card_details = explode('-', $credit_card_details);
				$entry_id = $credit_card_details[0];
				$sq_cust1 = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$entry_id' and user_type='credit company'"));
				$company_gl = $sq_cust1['ledger_id'];
				//////Get Credit card company Charges///////
				$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select * from credit_card_company where entry_id='$entry_id'"));
				//////company's credit card charges
				$company_card_charges = ($sq_credit_charges['charges_in'] == 'Flat') ? $sq_credit_charges['credit_card_charges'] : ($payment_amount1 * ($sq_credit_charges['credit_card_charges'] / 100));
				//////company's tax on credit card charges
				$tax_charges = ($sq_credit_charges['tax_charges_in'] == 'Flat') ? $sq_credit_charges['tax_on_credit_card_charges'] : ($company_card_charges * ($sq_credit_charges['tax_on_credit_card_charges'] / 100));
				$finance_charges = intval($company_card_charges) + intval($tax_charges);
				$finance_charges = number_format($finance_charges, 2);
				$credit_company_amount = intval($payment_amount1) - intval($finance_charges);

				//////Finance charges ledger///////
				$module_name = "Visa Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $finance_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $finance_charges, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = 231;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				
				//////Credit company amount///////
				$module_name = "Visa Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_company_amount;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $credit_company_amount, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $company_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
			}
			else{
				$module_name = "Visa Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $payment_amount1;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $payment_amount1, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $pay_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
			}

			//////Customer Payment Amount///////
			$module_name = "Visa Booking Payment";
			$module_entry_id = $payment_id;
			$transaction_id = $transaction_id1;
			$payment_amount = $payment_amount1;
			$payment_date = $payment_date1;
			$payment_particular = get_sales_paid_particular(get_visa_booking_id($visa_id, $yr1), $payment_date1, $payment_amount1, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr1), $bank_id1, $transaction_id1);
			$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
			$gl_id = $cust_gl;
			$payment_side = "Credit";
			$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
			$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
		}
	}
	public function bank_cash_book_save($visa_id, $payment_id, $branch_admin_id)
	{
		global $bank_cash_book_master;

		$customer_id = $_POST['customer_id'];
		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$transaction_id = $_POST['transaction_id'];
		$bank_id = $_POST['bank_id'];
		$booking_date = $_POST['balance_date'];
		$credit_charges = $_POST['credit_charges'];
		$credit_card_details = $_POST['credit_card_details'];

		$payment_date = date("Y-m-d", strtotime($payment_date));

		if ($payment_mode == 'Credit Card') {

			$payment_amount = intval($payment_amount) + intval($credit_charges);
			$credit_card_details = explode('-', $credit_card_details);
			$entry_id = $credit_card_details[0];
			$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select bank_id from credit_card_company where entry_id ='$entry_id'"));
			$bank_id = $sq_credit_charges['bank_id'];
		}

		$year1 = explode("-", $payment_date);
		$yr1 = $year1[0];
		$year2 = explode("-", $booking_date);
		$yr2 = $year1[0];
		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}
		$module_name = "Visa Booking Payment";
		$module_entry_id = $payment_id;
		$payment_date = $payment_date;
		$payment_amount = $payment_amount;
		$payment_mode = $payment_mode;
		$bank_name = $bank_name;
		$transaction_id = $transaction_id;
		$bank_id = $bank_id;
		$particular = get_sales_paid_particular(get_visa_booking_payment_id($payment_id, $yr1), $payment_date, $payment_amount, $customer_id, $payment_mode, get_visa_booking_id($visa_id, $yr2), $bank_id, $transaction_id);
		$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
		$payment_side = "Debit";
		$payment_type = ($payment_mode == "Cash") ? "Cash" : "Bank";

		$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type, $branch_admin_id);
	}
	public function visa_master_update()
	{
		$row_spec = "sales";
		$visa_id = $_POST['visa_id'];
		$customer_id = $_POST['customer_id'];
		$visa_issue_amount = $_POST['visa_issue_amount'];
		$service_charge = $_POST['service_charge'];
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$visa_total_cost = $_POST['visa_total_cost'];
		$roundoff = $_POST['roundoff'];
		$due_date1 = $_POST['due_date1'];
		$balance_date1 = $_POST['balance_date1'];
		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		
		$first_name_arr = isset($_POST['first_name_arr']) ? $_POST['first_name_arr'] : [];
		$middle_name_arr = isset($_POST['middle_name_arr']) ? $_POST['middle_name_arr'] : [];
		$last_name_arr = isset($_POST['last_name_arr']) ? $_POST['last_name_arr'] : [];
		$birth_date_arr = isset($_POST['birth_date_arr']) ? $_POST['birth_date_arr'] : [];
		$adolescence_arr = isset($_POST['adolescence_arr']) ? $_POST['adolescence_arr'] : [];
		$visa_country_name_arr = isset($_POST['visa_country_name_arr']) ? $_POST['visa_country_name_arr'] : [];
		$visa_type_arr = isset($_POST['visa_type_arr']) ? $_POST['visa_type_arr'] : [];
		$passport_id_arr = isset($_POST['passport_id_arr']) ? $_POST['passport_id_arr'] : [];
		$issue_date_arr = isset($_POST['issue_date_arr']) ? $_POST['issue_date_arr'] : [];
		$expiry_date_arr = isset($_POST['expiry_date_arr']) ? $_POST['expiry_date_arr'] : [];
		$nationality_arr = isset($_POST['nationality_arr']) ? $_POST['nationality_arr'] : [];
		$appointment_date_arr = isset($_POST['appointment_date_arr']) ? $_POST['appointment_date_arr'] : [];
		$entry_id_arr = isset($_POST['entry_id_arr']) ? $_POST['entry_id_arr'] : [];
		$e_checkbox_arr = isset($_POST['e_checkbox_arr']) ? $_POST['e_checkbox_arr'] : [];

		$sq_visa_info = mysqli_fetch_assoc(mysqlQuery("select * from visa_master where visa_id='$visa_id'"));
		$markup = $_POST['markup'];
		$service_tax_markup = $_POST['service_tax_markup'];
		$reflections = json_decode(json_encode($_POST['reflections']));
		$currency_code = $_POST['currency_code'];
		$old_total = $_POST['old_total'];

		$due_date1 = date('Y-m-d', strtotime($due_date1));
		$balance_date1 = date('Y-m-d', strtotime($balance_date1));

		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$visa_issue_amount = ($value != "") ? $value : $visa_issue_amount;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
				case 'markup':
					$markup = ($value != "") ? $value : $markup;
					break;
			}
		}
		begin_t();

		$bsmValues = json_encode($bsmValues);
		$reflections = json_encode($reflections);
		$sq_visa = mysqlQuery("update visa_master set customer_id='$customer_id', visa_issue_amount='$visa_issue_amount', service_charge='$service_charge' , service_tax_subtotal='$service_tax_subtotal', visa_total_cost='$visa_total_cost', due_date='$due_date1',created_at='$balance_date1',markup='$markup',markup_tax='$service_tax_markup',reflections='$reflections',bsm_values='$bsmValues' , roundoff='$roundoff',currency_code='$currency_code' where visa_id='$visa_id' ");

		if (!$sq_visa) {

			rollback_t();

			echo "error--Sorry, Visa information not updated successfully!";

			exit;
		} else {

			for ($i = 0; $i < sizeof($first_name_arr); $i++) {

				$birth_date_arr[$i] = get_date_db($birth_date_arr[$i]);
				$issue_date_arr[$i] = get_date_db($issue_date_arr[$i]);
				$expiry_date_arr[$i] = get_date_db($expiry_date_arr[$i]);
				$appointment_date_arr[$i] = get_date_db($appointment_date_arr[$i]);
				if($e_checkbox_arr[$i] == 'true'){
					if ($entry_id_arr[$i] == "") {

						$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from visa_master_entries"));
						$entry_id = $sq_max['max'] + 1;

						$sq_entry = mysqlQuery("insert into visa_master_entries(entry_id, visa_id, first_name, middle_name, last_name, birth_date, adolescence, visa_country_name, visa_type, passport_id, issue_date, expiry_date, nationality,appointment_date) values('$entry_id', '$visa_id', '$first_name_arr[$i]', '$middle_name_arr[$i]', '$last_name_arr[$i]', '$birth_date_arr[$i]', '$adolescence_arr[$i]', '$visa_country_name_arr[$i]', '$visa_type_arr[$i]', '$passport_id_arr[$i]', '$issue_date_arr[$i]', '$expiry_date_arr[$i]', '$nationality_arr[$i]','$appointment_date_arr[$i]')");

						if (!$sq_entry) {
							$GLOBALS['flag'] = false;
							echo "error--Some Visa entries are not saved!";
							//exit;
						}
					} else {
						$sq_entry = mysqlQuery("update visa_master_entries set first_name='$first_name_arr[$i]', middle_name='$middle_name_arr[$i]', last_name='$last_name_arr[$i]', birth_date='$birth_date_arr[$i]', adolescence='$adolescence_arr[$i]', visa_country_name='$visa_country_name_arr[$i]', visa_type='$visa_type_arr[$i]', passport_id='$passport_id_arr[$i]', issue_date='$issue_date_arr[$i]', expiry_date='$expiry_date_arr[$i]', nationality='$nationality_arr[$i]',appointment_date	='$appointment_date_arr[$i]' where entry_id='$entry_id_arr[$i]'");
						if (!$sq_entry) {
							$GLOBALS['flag'] = false;
							echo "error--Some Visa entries are not updated!";
							//exit;
						}
					}
				}else{
					$sq_entry = mysqlQuery("delete from visa_master_entries where entry_id='$entry_id_arr[$i]'");
					if (!$sq_entry) {
						$GLOBALS['flag'] = false;
						echo "error--Some Visa entries are not deleted!";
						//exit;
					}
				}
			}

			$row_visa_type = mysqli_fetch_assoc(mysqlQuery("select * from visa_master_entries where visa_id='$visa_id'"));
			$sq_pass_count = mysqli_num_rows(mysqlQuery("select * from  visa_master_entries where visa_id='$visa_id' and status!='Cancel'"));
	
			$pass_name = $row_visa_type['first_name'].' '.$row_visa_type['last_name'];
			$visa_type = $row_visa_type['visa_type'];
			$booking_date = $balance_date1;
			$yr = explode("-", $booking_date);
			$year = $yr[0];

			//Get Particular
			$particular = $this->get_particular($customer_id, $visa_type, get_visa_booking_id($visa_id,$year), $pass_name,$sq_pass_count);
			//Finance update
			$this->finance_update($sq_visa_info, $row_spec, $particular);

			global $transaction_master;
			if(floatval($old_total) != floatval($visa_total_cost)){

				$sq_ct = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
				if($sq_ct['type']=='Corporate'||$sq_ct['type'] == 'B2B'){
					$cust_name = $sq_ct['company_name'];
				}else{
					$cust_name = $sq_ct['first_name'].' '.$sq_ct['last_name'];
				}
		
				$trans_id = get_visa_booking_id($visa_id,$year).' : '.$cust_name;
				$transaction_master->updated_entries('Visa Sale',$visa_id,$trans_id,$old_total,$visa_total_cost);
			}
			if ($GLOBALS['flag']) {
				commit_t();
				echo "Visa Booking has been successfully updated.";
				exit;
			} else {
				rollback_t();
				exit;
			}
		}
	}

	function get_particular($customer_id, $services,$booking_id,$pass_name,$pass_count)
	{
		return $booking_id.' and '.$services . ' for ' . $pass_name.' * '.$pass_count;
	}

	public function finance_update($sq_visa_info, $row_spec, $particular)
	{
		$row_spec = 'sales';
		$visa_id = $_POST['visa_id'];
		$customer_id = $_POST['customer_id'];
		$visa_issue_amount = $_POST['visa_issue_amount'];
		$service_charge = $_POST['service_charge'];
		// $service_tax = $_POST['service_tax'];
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$visa_total_cost = $_POST['visa_total_cost'];
		$roundoff = $_POST['roundoff'];
		$balance_date1 = $_POST['balance_date1'];
		$markup = $_POST['markup'];
		$service_tax_markup = $_POST['service_tax_markup'];
		$created_at = date('Y-m-d', strtotime($balance_date1));
		$reflections = json_decode(json_encode($_POST['reflections']));
		global $transaction_master;

		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$visa_issue_amount = ($value != "") ? $value : $visa_issue_amount;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
				case 'markup':
					$markup = ($value != "") ? $value : $markup;
					break;
			}
		}
		$visa_sale_amount = $visa_issue_amount;

		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
		$cust_gl = $sq_cust['ledger_id'];

		////////////Sales/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $visa_sale_amount;
		$payment_date = $created_at;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = 140;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		////////////service charge/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $service_charge;
		$payment_date = $created_at;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = ($reflections[0]->hotel_sc != '') ? $reflections[0]->hotel_sc : 188;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		/////////Service Charge Tax Amount////////
		$service_tax_subtotal = explode(',', $service_tax_subtotal);
		$tax_ledgers = explode(',', $reflections[0]->hotel_taxes);
		for ($i = 0; $i < sizeof($service_tax_subtotal); $i++) {

			$service_tax = explode(':', $service_tax_subtotal[$i]);
			$tax_amount = $service_tax[2];
			$ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = $tax_amount;
			$payment_date = $created_at;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$old_gl_id = $gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		}

		////////////Markup/////////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $markup;
		$payment_date = $created_at;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = ($reflections[0]->hotel_markup != '') ? $reflections[0]->hotel_markup : 200;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		/////////Markup Tax Amount////////
		// Eg. CGST:(9%):24.77, SGST:(9%):24.77
		$service_tax_markup = explode(',', $service_tax_markup);
		$tax_ledgers = explode(',', $reflections[0]->hotel_markup_taxes);
		for ($i = 0; $i < sizeof($service_tax_markup); $i++) {

			$service_tax = explode(':', $service_tax_markup[$i]);
			$tax_amount = $service_tax[2];
			$ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

			$module_name = "Visa Booking";
			$module_entry_id = $visa_id;
			$transaction_id = "";
			$payment_amount = $tax_amount;
			$payment_date = $created_at;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Visa Sales');
			$old_gl_id = $gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '1', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		}
		/////////roundoff/////////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $roundoff;
		$payment_date = $created_at;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = 230;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		////////Customer Amount//////
		$module_name = "Visa Booking";
		$module_entry_id = $visa_id;
		$transaction_id = "";
		$payment_amount = $visa_total_cost;
		$payment_date = $created_at;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Visa Sales');
		$old_gl_id = $gl_id = $cust_gl;
		$payment_side = "Debit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
	}

	public function visa_booking_email_send($visa_id, $visa_country_name_arr, $visa_type_arr, $first_name_arr, $payment_amount = 0)
	{
		global $encrypt_decrypt, $secret_key, $currency;

		$link = BASE_URL . 'view/customer';
		$sq_visa = mysqli_fetch_assoc(mysqlQuery("select * from visa_master where visa_id='$visa_id'"));
		$date = $sq_visa['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_pay = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum,sum(`credit_charges`) as sumc from visa_payment_master where clearance_status!='Cancelled' and visa_id='$visa_id'"));
		$credit_card_amount = $sq_pay['sumc'];
		$total_amount = floatval($sq_visa['visa_total_cost']) + floatval($credit_card_amount);
		$total_pay_amt = floatval($sq_pay['sum']) + floatval($credit_card_amount);
		$outstanding =  floatval($total_amount) - floatval($total_pay_amt);
		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_visa[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'].' '.$sq_customer['last_name'];

		$username = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
		$password = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key);

		$doc_link_content = '';

		for ($i = 0; $i < sizeof($visa_country_name_arr); $i++) {

			$visa_docs_link = '../../../images/Visa-Documents/' . strtoupper($visa_country_name_arr[$i]) . '/' . $visa_type_arr[$i] . '.txt';

			if (is_file($visa_docs_link)) {
			} else {
				$visa_docs_link = "";
			}
			if ($visa_docs_link != "") {

				$visa_docs_link = BASE_URL . 'images/Visa-Documents/' . strtoupper($visa_country_name_arr[$i]) . '/' . $visa_type_arr[$i] . '.txt';

				$doc_link_content .= '

				<tr>
					<td>
						<span style="display: inline-block; padding: 14px 0 6px 0; border-bottom: 1px dotted #a0a0a0;">
							<a href="' . $visa_docs_link . '">Required Documents Link for</a> : <strong>' . $visa_country_name_arr[$i] . '</strong>
						</span>
					</td>
				</tr>';
			}
		}

		$subject = 'Booking confirmation acknowledgement! ( ' . get_visa_booking_id($visa_id, $year) . ' )';
		$VisaDetails = mysqlQuery('SELECT * FROM `visa_master_entries` WHERE visa_id = ' . $visa_id);

		$total_amount1 = currency_conversion($currency,$sq_visa['currency_code'],$total_amount);
		$total_pay_amt1 = currency_conversion($currency,$sq_visa['currency_code'],$total_pay_amt);
		$outstanding1 = currency_conversion($currency,$sq_visa['currency_code'],$outstanding);
		$content = '<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
		<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Total Amount</td>   <td style="text-align:left;border: 1px solid #888888;" >' . $total_amount1 . '</td></tr>
		<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Paid Amount</td>   <td style="text-align:left;border: 1px solid #888888;">' . $total_pay_amt1 . '</td></tr> 
		<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Balance Amount</td>   <td style="text-align:left;border: 1px solid #888888;">' . $outstanding1 . '</td></tr>
		</table>
		</tr>';

		while ($rows = mysqli_fetch_assoc($VisaDetails)) {
			$content .= '<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
			<tr><th colspan=2>Visa Details</th></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Customer Name</td>   <td style="text-align:left;border: 1px solid #888888;" >' . $rows['first_name'] . '</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Country Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $rows['visa_country_name'] . '</td></tr> 
			<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Visa Type</td>   <td style="text-align:left;border: 1px solid #888888;">' . $rows['visa_type'] . '</td></tr>
		</table>
		</tr>';
		}

		$content .= mail_login_box($username, $password, $link);
		global $model, $backoffice_email_id;
		$model->app_email_send('15', $customer_name, $password, $content, $subject);
		if ($backoffice_email_id != "")
			$model->app_email_send('15', "Admin", $backoffice_email_id, $content, $subject);
	}

	public function employee_sign_up_mail($first_name, $last_name, $username, $password, $email_id)
	{
		global $app_email_id, $app_name, $app_contact_no, $admin_logo_url, $app_website;
		global $mail_em_style, $mail_em_style1, $mail_font_family, $mail_strong_style, $mail_color;
		$link = BASE_URL . 'view/customer';
		$content = mail_login_box($username, $password, $link);
		$subject = 'Welcome aboard!';
		global $model;
		$model->app_email_send('2', $first_name, $email_id, $content, $subject, '1');
	}

	public function whatsapp_send()
	{
		global $app_contact_no, $encrypt_decrypt, $secret_key,$app_name,$session_emp_id;

		$booking_date = $_POST['booking_date'];
		$customer_id = $_POST['customer_id'];

		if ($customer_id == '0') {
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master ORDER BY customer_id DESC LIMIT 1"));
		} else {
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		}

		$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);

		$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id= '$session_emp_id'"));
		if ($session_emp_id == 0) {
			$contact = $app_contact_no;
		} else {
			$contact = $sq_emp_info['mobile_no'];
		}
		if($sq_customer['type']=='Corporate'||$sq_customer['type'] == 'B2B'){
			$customer_name = $sq_customer['company_name'];
		}else{
			$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
		}

		$whatsapp_msg = rawurlencode('Dear ' . $customer_name . ',
Hope you are doing great. This is to inform you that your booking is confirmed with us. We look forward to provide you a great experience.
*Booking Date* : ' . get_date_user($booking_date) . '

Please contact for more details : '.$app_name.' '.$contact);
	if ($customer_id == '0') {

		//Customer Whatsapp message
		$username = $_POST['contact_no'];
		$password = $_POST['email_id'];
		$whatsapp_msg .= whatsapp_login_box($username,$password);
	}
	$whatsapp_msg .= '%0aThank%20you.%0a';
	$link = 'https://web.whatsapp.com/send?phone=' . $contact_no . '&text=' . $whatsapp_msg;
	echo $link;
	}
}
