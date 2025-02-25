<?php
//Generic Files
include "../../../../model.php"; 
include "../../print_functions.php";
require("../../../../../classes/convert_amount_to_word.php"); 

//Parameters
$invoice_no = $_GET['invoice_no'];
$train_ticket_id = $_GET['train_ticket_id'];
$invoice_date = $_GET['invoice_date'];
$customer_id = $_GET['customer_id'];
$service_name = $_GET['service_name'];
$basic_cost1 = $_GET['basic_cost'];
$taxation_type = $_GET['taxation_type'];
$service_tax_per = $_GET['service_tax_per'];

$net_amount = $_GET['net_amount'];
$bank_name = isset($_GET['bank_name']) ? $_GET['bank_name'] : '';
$total_paid = $_GET['total_paid'];
$balance_amount = $_GET['balance_amount'];
$sac_code = $_GET['sac_code'];
$service_charge = $_GET['service_charge'];
$credit_card_charges = $_GET['credit_card_charges'];
$bg = $_GET['bg'];
$canc_amount = $_GET['canc_amount'];

$charge = ($credit_card_charges!='')?$credit_card_charges:0 ;
$balance_amount = ($balance_amount < 0) ? 0 : $balance_amount;
$total_paid += $charge;
$sq_passenger = mysqlQuery("select * from  train_ticket_master_entries where train_ticket_id = '$train_ticket_id'");
$sq_passenger_count = mysqli_fetch_assoc(mysqlQuery("select count(*) as cnt from  train_ticket_master_entries where train_ticket_id = '$train_ticket_id'"));
$sq_fields = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id = '$train_ticket_id' and delete_status='0'"));
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : $sq_fields['branch_admin_id'];

$basic_cost = number_format($basic_cost1,2);
$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id='$train_ticket_id' and delete_status='0'"));
$roundoff = $sq_hotel['roundoff'];
$bsmValues = json_decode($sq_hotel['bsm_values']);
$tax_show = '';
$newBasic = $basic_cost1;

//Header
if($app_invoice_format == "Standard"){include "../headers/standard_header_html.php"; }
if($app_invoice_format == "Regular"){include "../headers/regular_header_html.php"; }
if($app_invoice_format == "Advance"){include "../headers/advance_header_html.php"; }

///////Service Charge Rules
$service_tax_amount = 0;
$name = '';
if($sq_hotel['service_tax_subtotal'] !== 0.00 && ($sq_hotel['service_tax_subtotal']) !== ''){
  $service_tax_subtotal1 = explode(',',$sq_hotel['service_tax_subtotal']);
  for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
    $service_tax = explode(':',$service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $name .= $service_tax[0]  . $service_tax[1] .', ';
  }
}
if($bsmValues[0]->service != ''){   //inclusive service charge
  $newBasic = $basic_cost1;
  $newSC = $service_tax_amount + $service_charge;
}
else{
  $tax_show =  rtrim($name, ', ').' : ' . $currency_code." ".($service_tax_amount);
  $newSC = $service_charge;
}
// ////////////////////Markup Rules
// $markupservice_tax_amount = 0;
// if($sq_hotel['markup_tax'] !== 0.00 && $sq_hotel['markup_tax'] !== ""){
//   $service_tax_markup1 = explode(',',$sq_hotel['markup_tax']);
//   for($i=0;$i<sizeof($service_tax_markup1);$i++){
//     $service_tax = explode(':',$service_tax_markup1[$i]);
//     $markupservice_tax_amount += $service_tax[2];
//   }
// }
// if($bsmValues[0]->markup != ''){ //inclusive markup
//   $newBasic = $basic_cost1 + $sq_hotel['markup'] + $markupservice_tax_amount;
// }
// else{
//   $newBasic = $basic_cost1;
//   $newSC = $service_charge + $sq_hotel['markup'];
//   $tax_show = rtrim($name, ', ') .' : ' . $currency_code." ".($markupservice_tax_amount + $service_tax_amount);
// }
////////////Basic Amount Rules
if($bsmValues[0]->basic != ''){ //inclusive markup
  
  $newBasic = $basic_cost1 + $service_tax_amount;
  $tax_show = '';
}
if($service_charge_switch == 'No'){
  $basic_service_amt = floatval($newBasic) + floatval($newSC);
}
$net_amount1 = 0;
$net_amount1 =  $basic_cost1 + $service_charge +$roundoff +  $sq_fields['delivery_charges'] +$service_tax_amount;
$amount_in_word = $amount_to_word->convert_number_to_words($net_amount1);
?>

<hr class="no-marg">
<div class="row">
<div class="col-md-12 mg_tp_20"><p class="border_lt"><span class="font_5">PASSENGER (s):  <?= $sq_passenger_count['cnt'] ?></span></p></div></div>
<!-- invoice_receipt_body_table-->
  <div class="main_block inv_rece_table">
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
        <table class="table table-bordered no-marg" id="tbl_emp_list" style="padding: 0 !important;">
          <thead>
            <tr class="table-heading-row">
              <th>SR.NO</th>
              <th>Name</th>
              <th>Travel_From</th>
              <th>Travel_To</th>
              <th>Departure Date/Time</th>
              <th>Train_Name</th>
              <th>Train_No</th>
            </tr>
          </thead>
          <tbody>   
          <?php
          $count = 1;
          while($row_passenger = mysqli_fetch_assoc($sq_passenger)){
            $sq_dest1 = mysqlQuery("select * from train_ticket_master_trip_entries where train_ticket_id = '$row_passenger[train_ticket_id]'");
            while($sq_dest = mysqli_fetch_assoc($sq_dest1)){
            ?>
            <tr class="odd">
              <td><?php echo $count; ?></td>
              <td><?php echo $row_passenger['first_name'].' '.$row_passenger['last_name']; ?></td>
              <td><?php echo $sq_dest['travel_from']; ?></td>
              <td><?php echo $sq_dest['travel_to']; ?></td>
              <td><?php echo get_datetime_user($sq_dest['travel_datetime']); ?></td>
              <td><?php echo $sq_dest['train_name']; ?></td>
              <td><?php echo $sq_dest['train_no']; ?></td>
            </tr>
            <?php $count++;
              }
            } ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
  <!-- invoice_receipt_body_calculation -->

<section class="print_sec main_block">
  <div class="row">
    <div class="col-md-12">
      <div class="main_block inv_rece_calculation border_block">
        <?php if($service_charge_switch == 'No'){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?php echo $currency_code." ".number_format($basic_service_amt,2); ?></span></p></div>
        <?php }else{ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?php echo $currency_code." ".number_format($newBasic,2); ?></span></p></div>
        <?php } ?>
        
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TOTAL </span><span class="font_5 float_r"><?= $currency_code." ".number_format($net_amount1,2) ?></span></p></div>
        <?php if($service_charge_switch == 'Yes'){ ?>
          <div class="col-md-6">
              <p class="border_lt"><span class="font_5">SERVICE CHARGE </span><span class="float_r"><?php echo $currency_code." ".number_format($newSC,2); ?></span></p>
          </div>
        <?php }else{ ?>
          <div class="col-md-6">
              <p class="border_lt"><span class="font_5"> </span><span class="float_r"></span></p>
          </div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">CREDIT CARD CHARGES </span><span class="float_r"><?= $currency_code." ".number_format($charge,2)?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">DELIVERY CHARGE </span><span class="float_r"><?php echo $currency_code." ".number_format( $sq_fields['delivery_charges'],2) ; ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ADVANCE PAID </span><span class="font_5 float_r"><?= $currency_code." ".number_format($total_paid,2) ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TAX</span><span class="float_r"><?= $tax_show ?></span></p></div>
        <?php
        if($bg != ''){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">CANCELLATION CHARGES</span><span class="float_r"><?= $currency_code.' '.$canc_amount ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ROUNDOFF </span><span class="font_5 float_r"><?= $currency_code." ".$roundoff ?></span></p></div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">CURRENT DUE </span><span class="font_5 float_r"><?= $currency_code." ".number_format($balance_amount,2) ?></span></p></div>
        <?php
        if($bg == ''){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">ROUNDOFF </span><span class="font_5 float_r"><?= $currency_code." ".$roundoff ?></span></p></div>
        <?php } ?>
        
      </div>
    </div>
  </div>
</section>

<?php 
//Footer
include "../generic_footer_html.php"; ?>