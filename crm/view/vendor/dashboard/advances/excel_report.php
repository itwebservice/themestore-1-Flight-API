<?php
include "../../../../model/model.php";
include_once('../../inc/vendor_generic_functions.php');
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
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status']; 
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$financial_year_id = $_GET['financial_year_id'];
$vendor_type = $_GET['vendor_type'];
$vendor_type_id = $_GET['vendor_type_id'];

if($vendor_type!=""){
     $vendor_str=$vendor_type;
}
else{
    $vendor_str = "";
}
if($vendor_type_id!=""){
     $vendor_str1 =  get_vendor_name($vendor_type, $vendor_type_id);
}
else{
    $vendor_str1 = "";
}
if($financial_year_id != ""){ 
    $query_year = mysqli_fetch_assoc(mysqlQuery("Select * from financial_year where financial_year_id='$financial_year_id'")); 
    $fin_year = get_date_user($query_year['from_date']).' To '.get_date_user($query_year['to_date']);  
}
else{
    $fin_year = "";
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'PrePurchase Advances')
            ->setCellValue('B3', 'Supplier Type')
            ->setCellValue('C3', $vendor_str)
            ->setCellValue('B4', 'Supplier Name')
            ->setCellValue('C4', $vendor_str1)
            ->setCellValue('B5', 'Financial Year')
            ->setCellValue('C5', $fin_year);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);  

$query = "select * from vendor_advance_master where 1 and delete_status='0'";
if($financial_year_id!=""){
    $query .= " and financial_year_id='$financial_year_id'";
}
if($vendor_type!=""){
    $query .= " and vendor_type='$vendor_type'";
}
if($vendor_type_id!=""){
    $query .= " and vendor_type_id='$vendor_type_id'";
}

if($branch_status=='yes' && $role!='Admin'){
    $query .= " and branch_admin_id = '$branch_admin_id'";
}
elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
$query .= " and emp_id='$emp_id'";
}
$query .= " order by payment_id desc ";
$total_paid_amt = 0;

$row_count = 8;

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Sr. No")
        ->setCellValue('C'.$row_count, "Transaction ID")
        ->setCellValue('D'.$row_count, "Supplier Type")
        ->setCellValue('E'.$row_count, "Supplier Name")
        ->setCellValue('F'.$row_count, "Payment Date")
        ->setCellValue('G'.$row_count, "Amount")
        ->setCellValue('H'.$row_count, "Mode")
        ->setCellValue('I'.$row_count, "Bank Name")
        ->setCellValue('J'.$row_count, "Cheque No/ID");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);    

$row_count++;
$count = 0;

    $sq_payment = mysqlQuery($query);
    $sq_pending_amount=0;
    $sq_cancel_amount=0;
    $sq_paid_amount=0;
    $total_payment=0;
    while($row_payment = mysqli_fetch_assoc($sq_payment)){
        $vendor_type_val = get_vendor_name($row_payment['vendor_type'], $row_payment['vendor_type_id']);
        $estimate_type_val = get_estimate_type_name($row_payment['estimate_type'], $row_payment['estimate_type_id']);
        $total_payment = $total_payment + $row_payment['payment_amount'];

        if($row_payment['clearance_status']=="Pending"){ 
            $bg='warning';
            $sq_pending_amount = $sq_pending_amount + $row_payment['payment_amount'];
        }
        else if($row_payment['clearance_status']=="Cancelled"){ 
            $bg='danger';
            $sq_cancel_amount = $sq_cancel_amount + $row_payment['payment_amount'];
        }

        $year1 = explode("-", $row_payment['payment_date']);
        $yr1 = $year1[0];
        $trans_id = get_puradv_payment_id($row_payment['payment_id'],$yr1);
        
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, ++$count)
        ->setCellValue('C'.$row_count,  $trans_id)
        ->setCellValue('D'.$row_count,  $row_payment['vendor_type'])
        ->setCellValue('E'.$row_count, $vendor_type_val)
        ->setCellValue('F'.$row_count, date('d-m-Y', strtotime($row_payment['payment_date'])))
        ->setCellValue('G'.$row_count, $row_payment['payment_amount'])
        ->setCellValue('H'.$row_count, $row_payment['payment_mode'])
        ->setCellValue('I'.$row_count, $row_payment['bank_name'])
        ->setCellValue('J'.$row_count, $row_payment['transaction_id']);

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);    

		$row_count++;

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, "Total Amount : ".number_format($total_payment, 2))
        ->setCellValue('H'.$row_count, "Total Pending : ".number_format($sq_pending_amount, 2))
        ->setCellValue('I'.$row_count, "Total Cancel : ".number_format($sq_cancel_amount, 2))
        ->setCellValue('J'.$row_count, "Total Paid : ".number_format(($total_payment - $sq_pending_amount - $sq_cancel_amount), 2));

        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($header_style_Array);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);

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
header('Content-Disposition: attachment;filename="PrePurchase Advances('.date('d-m-Y H:i').').xls"');
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
