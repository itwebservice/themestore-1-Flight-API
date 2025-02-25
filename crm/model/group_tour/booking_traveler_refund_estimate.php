<?php

$flag = true;

class booking_traveler_refund_estimate{



public function refund_estimate_update()

{
    $row_spec ='sales';
    $tourwise_id = $_POST['tourwise_id'];
    $cancel_amount = $_POST['cancel_amount'];
    $total_refund_amount = $_POST['total_refund_amount'];
    $tax_value = $_POST['tax_value'];
    $tour_service_tax_subtotal = $_POST['tour_service_tax_subtotal'];
    $cancel_amount_exc = $_POST['cancel_amount_exc'];

    begin_t();

    $created_at = date('Y-m-d H:i');

    $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(estimate_id) as max from refund_traveler_estimate"));
    $estimate_id = $sq_max['max'] + 1;

    $sq_est = mysqlQuery("insert into refund_traveler_estimate(estimate_id, tourwise_traveler_id, cancel_amount, total_refund_amount, created_at,`tax_value`,`tax_amount`,`cancel_amount_exc`) values ('$estimate_id', '$tourwise_id','$cancel_amount', '$total_refund_amount', '$created_at','$tax_value','$tour_service_tax_subtotal','$cancel_amount_exc')");       

    if($sq_est){

        if($GLOBALS['flag']){

            commit_t();
            $this->finance_save($row_spec);
            echo "Refund estimate has been successfully saved!";
            exit;
        }
        else{
            rollback_t();
            exit;   
        }
    }
    else{
        rollback_t();
        echo "error--Sorry, Estimate not saved!";
        exit;
    }

}


public function finance_save($row_spec)
{
    $tourwise_id = $_POST['tourwise_id'];
    $cancel_amount = $_POST['cancel_amount'];
    $ledger_posting = $_POST['ledger_posting'];
    $cancel_amount_exc = $_POST['cancel_amount_exc'];
    $tour_service_tax_subtotal_cancel = $_POST['tour_service_tax_subtotal'];

    $created_at = date("Y-m-d");
    $year = date('Y');
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$tourwise_id'"));
    $customer_id = $sq_booking['customer_id'];
    $total_discount = $sq_booking['repeater_discount'] + $sq_booking['adjustment_discount'];
    $total_sale_amount = $sq_booking['basic_amount']+$total_discount;
    $service_tax = $sq_booking['service_tax'];
    $reflections = json_decode($sq_booking['reflections']);

    //Getting customer Ledger
    $sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
    $cust_gl = $sq_cust['ledger_id'];
    $sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
    $cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
    $sq_tour = mysqli_fetch_assoc(mysqlQuery("select tour_name from tour_master where tour_id='$sq_booking[tour_id]'"));
    $tour_name = $sq_tour['tour_name'];
    $sq_tourgroup = mysqli_fetch_assoc(mysqlQuery("select from_date,to_date from tour_groups where group_id='$sq_booking[tour_group_id]'"));
    $from_date = new DateTime($sq_tourgroup['from_date']);
    $to_date = new DateTime($sq_tourgroup['to_date']);
    $numberOfNights= $from_date->diff($to_date)->format("%a");
    
    $particular = 'Against Invoice no '.get_group_booking_id($tourwise_id,$year).' for '.$tour_name.' for '.$cust_name.' for '.$numberOfNights.' Nights starting from '.get_date_user($sq_tourgroup['from_date']);

    global $transaction_master;

    /////////Service Charge Tax Amount////////
    // Eg. CGST:(9%):24.77, SGST:(9%):24.77
    $service_tax_subtotal = explode(',',$service_tax);
    $tax_ledgers = explode(',',$reflections[0]->hotel_taxes);
    $total_tax = 0;
    for($i=0;$i<sizeof($service_tax_subtotal);$i++){

        $service_tax = explode(':',$service_tax_subtotal[$i]);
        $tax_amount = $service_tax[2];
        $total_tax += $tax_amount;
        $ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

        $module_name = "Group Booking";
        $module_entry_id = $tourwise_id;
        $transaction_id = "";
        $payment_amount = $tax_amount;
        $payment_date = $created_at;
        $payment_particular = $particular;
        $ledger_particular = '';
        $gl_id = $ledger;
        $payment_side = "Debit";
        $clearance_status = "";
        $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    }
    //////////Sales/////////////
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $total_sale_amount;
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = 60;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');

    ////Roundoff Value
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $sq_booking['roundoff'];
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = 230;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    
    // TCS charge 
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $sq_booking['tcs_tax'];
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = 232;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');

    // Discount 
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $total_discount;
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = 36;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');

    ////////Customer Sale Amount//////
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $sq_booking['net_total'];
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');    
    
    $service_tax_subtotal = explode(',',$tour_service_tax_subtotal_cancel);
    $tax_ledgers = explode(',',$ledger_posting);
    $total_tax = 0;
    for($i=0;$i<sizeof($service_tax_subtotal);$i++){

        $service_tax = explode(':',$service_tax_subtotal[$i]);
        $tax_amount = $service_tax[2];
        $total_tax += $tax_amount;
        $ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

        $module_name = "Group Booking";
        $module_entry_id = $tourwise_id;
        $transaction_id = "";
        $payment_amount = $tax_amount;
        $payment_date = $created_at;
        $payment_particular = $particular;
        $ledger_particular = '';
        $gl_id = $ledger;
        $payment_side = "Credit";
        $clearance_status = "";
        $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    }

    ////////Cancel Amount//////
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $cancel_amount_exc;
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = 161;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');    

    ////////Customer Cancel Amount//////
    $module_name = "Group Booking";
    $module_entry_id = $tourwise_id;
    $transaction_id = "";
    $payment_amount = $cancel_amount;
    $payment_date = $created_at;
    $payment_particular = $particular;
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
}

}

?>