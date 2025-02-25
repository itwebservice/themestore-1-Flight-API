<?php
global $currency;
include "../../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
        'rgb' => $color
        )
    ));
}

//This array sets the font atrributes
$header_style_Array = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    ));

//This is border array
$borderArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
      );

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("Office 2007 XLSX Test Document")
                             ->setSubject("Office 2007 XLSX Test Document")
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Test result file");


//////////////////////////****************Content start**************////////////////////////////////
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status'];

$customer_id = $_GET['customer_id'];
$booking_id = $_GET['booking_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$cust_type = $_GET['cust_type'];
$company_name = (isset($_GET['company_name'])) ? $_GET['company_name'] : '';
$booker_id = $_GET['booker_id'];
$branch_id = $_GET['branch_id'];

$sql_booking_date = mysqli_fetch_assoc(mysqlQuery("select * from hotel_booking_master where booking_id = '$booking_id' and delete_status='0'")) ;
$booking_date = $sql_booking_date['created_at'];
$yr = explode("-", $booking_date);
$year =$yr[0];

if($customer_id!=""){
    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
		$cust_name = $sq_customer['company_name'];
	}else{
		$cust_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
	}
}
else{
    $cust_name = "";
}
if($from_date!="" && $to_date!=""){
    $date_str = $from_date.' to '.$to_date;
}
else{
    $date_str = "";
}
$invoice_id = ($booking_id!="") ? get_hotel_booking_id($booking_id,$year): "";
if($company_name == 'undefined') { $company_name = ''; }

if($booker_id != '')
{
    $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$booker_id'"));
    if($sq_emp['first_name'] == '') { $emp_name='Admin';}
    else{ $emp_name = $sq_emp['first_name'].' '.$sq_emp['last_name']; }
}

if($branch_id != '') { 
    $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_id'"));
    $branch_name = $sq_branch['branch_name']==''?'NA':$sq_branch['branch_name'];
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'Hotel Summary')
            ->setCellValue('B3', 'Booking ID')
            ->setCellValue('C3', $invoice_id)
            ->setCellValue('B4', 'Customer')
            ->setCellValue('C4', $cust_name)
            ->setCellValue('B5', 'From-To Date')
            ->setCellValue('C5',  $date_str)
            ->setCellValue('B6', 'Customer Type')
            ->setCellValue('C6', $cust_type)
            ->setCellValue('B7', 'Company Name')
            ->setCellValue('C7', $company_name)
            ->setCellValue('B8', 'Booked By')
            ->setCellValue('C8', $emp_name)
            ->setCellValue('B9', 'Branch')
            ->setCellValue('C9', $branch_name);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);   

$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);  

$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($borderArray); 

$query = "select * from hotel_booking_master where 1 and delete_status='0' ";
if($customer_id!=""){
    $query .=" and customer_id='$customer_id'";
}
if($booking_id!=""){
    $query .=" and booking_id='$booking_id'";
}
if($from_date!="" && $to_date!=""){
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    $query .= " and created_at between '$from_date' and '$to_date'";
}
if($cust_type != ""){
    $query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
if($company_name != ""){
    $query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
if($booker_id!=""){
    $query .= " and emp_id='$booker_id'";
}
if($branch_id!=""){
    $query .= " and emp_id in(select emp_id from emp_master where branch_id = '$branch_id')";
}
if($branch_status=='yes' && $role!='Admin'){
    $query .= " and branch_admin_id = '$branch_admin_id'";
}
elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
$query .= " and emp_id='$emp_id'";
}
$query .= " order by booking_id desc";

 $row_count = 11;
 
 $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$row_count, "Sr. No")
                ->setCellValue('C'.$row_count, "Booking ID")
                ->setCellValue('D'.$row_count, "Customer_Name")
                ->setCellValue('E'.$row_count, "Mobile")
                ->setCellValue('F'.$row_count, "EMAIL_ID")
                ->setCellValue('G'.$row_count, "Total_Hotel")
                ->setCellValue('H'.$row_count, "Booking_Date")
                ->setCellValue('I'.$row_count, "Basic_Amount")
                ->setCellValue('J'.$row_count, "Service_Charge")
                ->setCellValue('K'.$row_count, "Tax")
                ->setCellValue('L'.$row_count, "TCS")
                ->setCellValue('M'.$row_count, "Credit card charges")
                ->setCellValue('N'.$row_count, "Discount")
                ->setCellValue('O'.$row_count, "TDS")
                ->setCellValue('P'.$row_count, "Sale")
                ->setCellValue('Q'.$row_count, "Cancel")
                ->setCellValue('R'.$row_count, "Total")
                ->setCellValue('S'.$row_count, "Paid")
                ->setCellValue('T'.$row_count, "Outstanding Balance")
                ->setCellValue('U'.$row_count, "Due_Date")
                ->setCellValue('V'.$row_count, "Purchase")
                ->setCellValue('W'.$row_count, "Purchased_From")
                ->setCellValue('X'.$row_count, "Branch")
                ->setCellValue('Y'.$row_count, "Booked_By")
                ->setCellValue('Z'.$row_count, "Incentive");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($borderArray);    

$row_count++;

        $count = 0;
        $total_balance=0;
        $total_refund=0;        
        $cancel_total =0;
        $sale_total = 0;
        $paid_total = 0;
        $balance_total = 0;
        $vendor_name1 = '';

        $sq_booking = mysqlQuery($query);
        while($row_booking = mysqli_fetch_assoc($sq_booking)){

            $pass_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));
            $cancel_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]' and status='Cancel'"));
            if($pass_count==$cancel_count){
                $bg="danger";
            }
            else {
                $bg="#fff";
            }

            $date = $row_booking['created_at'];
            $yr = explode("-", $date);
            $year =$yr[0];
            $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
            $contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
            $email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key);
            if($sq_customer['type']=='Corporate'||$sq_customer['type'] == 'B2B'){
                $customer_name = $sq_customer['company_name'];
            }else{
                $customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
            }

            $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_booking[emp_id]'"));
            if($sq_emp['first_name'] == '') { $emp_name='Admin';}
            else{ $emp_name = $sq_emp['first_name'].' '.$sq_emp['last_name']; }

            $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$sq_emp[branch_id]'"));
            $branch_name = $sq_branch['branch_name']==''?'NA':$sq_branch['branch_name'];
            $sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from hotel_booking_entries where booking_id = '$row_booking[booking_id]'"));

            $due_date = ($row_booking['due_date'] == '1970-01-01') ? 'NA' : get_date_user($row_booking['due_date']);
            $sq_payment_total = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum ,sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_booking[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

            $sq_hotel_info = mysqli_fetch_assoc(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_booking[booking_id]'"));

            $total_paid = 0;
            $total_paid =  $sq_payment_total['sum'];  
            $total_paid = ($total_paid == '') ? '0' : $total_paid;

            $sale_bal = $row_booking['total_fee'];
            $paid_amount = $sq_payment_total['sum'];
            $total_bal = $sale_bal - $paid_amount;
            if($total_bal>=0)
            {
                $available_bal = $available_bal + $total_bal;
            }else
            {
                $pending_bal = $pending_bal + ($total_bal);
            }
            if($paid_amount==""){ $paid_amount = 0; }

            $sale_amount=$row_booking['total_fee']-$row_booking['cancel_amount'];
            $canc_amount=$row_booking['cancel_amount'];

            $total_fee = $row_booking['total_fee'];


            if($canc_amount=="") { $canc_amount = 0; }

            $total_amount1 = $row_booking['total_fee'] - $canc_amount;
            $total_bal = $sale_bal - $canc_amount;  

            if($pass_count == $cancel_count){
                if($paid_amount > 0){
                    if($canc_amount >0){
                        if($paid_amount > $canc_amount){
                            $bal = 0;
                        }else{
                            $bal = $canc_amount - $paid_amount;
                        }
                    }else{
                        $bal = 0;
                    }
                }
                else{
                    $bal = $canc_amount;
                }
            }
            else{
                $bal = $total_fee - $paid_amount;
            }

            //Footer
            $cancel_total = $cancel_total + $canc_amount;
            $sale_total = $sale_total + $total_bal;
            $paid_total = $paid_total + $paid_amount;
            $balance_total = $balance_total + $bal;

            /////// Purchase ////////
            $total_purchase = 0;
            $purchase_amt = 0;
            $i=0;
            $p_due_date = '';
            $sq_purchase_count = mysqli_num_rows(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_booking[booking_id]' and delete_status='0'"));
            if($sq_purchase_count == 0){  $p_due_date = 'NA'; }
            $sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_booking[booking_id]' and delete_status='0'");
            while($row_purchase = mysqli_fetch_assoc($sq_purchase)){	
                if($row_purchase['purchase_return'] == 0){
                    $total_purchase += $row_purchase['net_total'];
                }
                else if($row_purchase['purchase_return'] == 2){
                    $cancel_estimate = json_decode($row_purchase['cancel_estimate']);
                    $p_purchase = ($row_purchase['net_total'] - floatval($cancel_estimate[0]->net_total));
                    $total_purchase += $p_purchase;
                }
                $vendor_name = get_vendor_name_report($row_purchase['vendor_type'], $row_purchase['vendor_type_id']);
                if($vendor_name != ''){ $vendor_name1 .= $vendor_name.','; }
            }
            $vendor_name1 = substr($vendor_name1, 0, -1);

            $invoice_no = get_hotel_booking_id($row_booking['booking_id'],$year);
            $booking_id = $row_booking['booking_id'];
            $invoice_date = date('d-m-Y',strtotime($row_booking['created_at']));
            $customer_id = $row_booking['customer_id'];
            $service_name = "Hotel Invoice";
            //**Service Tax
            $taxation_type = $row_booking['taxation_type'];
            $service_tax_per = $row_booking['service_tax'];			
            $service_tax = $row_booking['service_tax_subtotal'];
            //**Basic Cost
            $basic_cost = $row_booking['sub_total'] + $row_booking['cancel_amount'];
            $service_charge = $row_booking['service_charge'];
            $credit_card_charges = $sq_payment_total['sumc'];

            //**Net Amount
            $net_amount = $row_booking['total_fee'] - $row_booking['cancel_amount'];;
            $sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='Hotel / Accommodation'"));   
            $sac_code = $sq_sac['hsn_sac_code'];
            //// Calculate Service Tax//////
            $service_tax_amount = 0;
            if($row_booking['service_tax_subtotal'] !== 0.00 && ($row_booking['service_tax_subtotal']) !== ''){
            $service_tax_subtotal1 = explode(',',$row_booking['service_tax_subtotal']);
            for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
                $service_tax = explode(':',$service_tax_subtotal1[$i]);
                $service_tax_amount +=  $service_tax[2];
                }
            }

            //// Calculate Markup Tax//////
            $markupservice_tax_amount = 0;
            if($row_booking['markup_tax'] !== 0.00 && $row_booking['markup_tax'] !== ""){
            $service_tax_markup1 = explode(',',$row_booking['markup_tax']);
            for($i=0;$i<sizeof($service_tax_markup1);$i++){
                $service_tax = explode(':',$service_tax_markup1[$i]);
                $markupservice_tax_amount += $service_tax[2];

                }
            }
            $sq_incentive = mysqli_fetch_assoc(mysqlQuery("select * from booker_sales_incentive where booking_id='$row_booking[booking_id]' and service_type='Hotel Booking'"));
            // Currency conversion
            $currency_amount1 = currency_conversion($currency,$row_booking['currency_code'],$net_amount);
            if($row_booking['currency_code'] !='0' && $currency != $row_booking['currency_code']){
                $currency_amount = ' ('.$currency_amount1.')';
            }else{
                $currency_amount = '';
            }

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$row_count, ++$count)
            ->setCellValue('C'.$row_count, get_hotel_booking_id($row_booking['booking_id'],$year))
            ->setCellValue('D'.$row_count, $customer_name)
            ->setCellValue('E'.$row_count, $contact_no)
            ->setCellValue('F'.$row_count, $email_id)
            ->setCellValue('G'.$row_count, $sq_total_member)
            ->setCellValue('H'.$row_count, get_date_user($row_booking['created_at']))
            ->setCellValue('I'.$row_count, number_format($row_booking['sub_total'],2))
            ->setCellValue('J'.$row_count, number_format($row_booking['service_charge']+$row_booking['markup'],2))
            ->setCellValue('K'.$row_count, number_format($service_tax_amount + $markupservice_tax_amount,2))
            ->setCellValue('L'.$row_count, number_format($row_booking['tcs_tax'],2))
            ->setCellValue('M'.$row_count, number_format($sq_payment_total['sumc'],2))
            ->setCellValue('N'.$row_count, number_format($row_booking['discount'],2))
            ->setCellValue('O'.$row_count, number_format($row_booking['tds'],2))
            ->setCellValue('P'.$row_count, number_format($row_booking['total_fee'],2))
            ->setCellValue('Q'.$row_count, number_format($canc_amount,2))
            ->setCellValue('R'.$row_count, number_format($net_amount,2).$currency_amount)
            ->setCellValue('S'.$row_count, number_format($paid_amount,2))
            ->setCellValue('T'.$row_count, number_format($bal,2))
            ->setCellValue('U'.$row_count, $due_date)
            ->setCellValue('V'.$row_count, number_format($total_purchase,2))
            ->setCellValue('W'.$row_count, $vendor_name1)
            ->setCellValue('X'.$row_count, $branch_name)
            ->setCellValue('Y'.$row_count, $emp_name)
            ->setCellValue('Z'.$row_count, number_format($sq_incentive['incentive_amount'],2));

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($borderArray);    

		$row_count++;

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, "")
        ->setCellValue('H'.$row_count, "")
        ->setCellValue('J'.$row_count, "")
        ->setCellValue('K'.$row_count, "")
        ->setCellValue('L'.$row_count, "")
        ->setCellValue('M'.$row_count, "")
        ->setCellValue('N'.$row_count, "")
        ->setCellValue('O'.$row_count, "")
        ->setCellValue('P'.$row_count, 'TOTAL CANCEL : '.number_format($cancel_total,2))
        ->setCellValue('Q'.$row_count, 'TOTAL SALE :'.number_format($sale_total,2))
        ->setCellValue('R'.$row_count, 'TOTAL PAID : '.number_format($paid_total,2))
        ->setCellValue('S'.$row_count, 'TOTAL BALANCE :'.number_format($balance_total,2));

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':S'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':S'.$row_count)->applyFromArray($borderArray);

}
	

//////////////////////////****************Content End**************////////////////////////////////
	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


for($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="HotelSummary('.date('d-m-Y H:i').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
