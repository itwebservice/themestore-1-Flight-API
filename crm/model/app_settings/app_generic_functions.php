<?php
if (!function_exists('begin_panel')) {
  function begin_panel($title, $entry_id = '')
  {
    return '<div class="app_panel"> <div class="app_panel_head"><h2 class="pull-left">' . $title . '</h2></div> <div class="app_panel_content">';
  }
}
if (!function_exists('end_panel')) {
  function end_panel()
  {
    return  '</div></div>';
  }
} ?>
<?php
//App Widget Fucntion start
function begin_widget()
{ ?> <div class="widget_parent"> <?php }
function end_widget()
{ ?> </div> <?php }
function widget_head($head_title)
{ ?> <div class="widget_head"><?= $head_title ?></div> <?php }
function widget_element($title_arr, $content_arr, $percent, $label)
{
if (sizeof($title_arr) == 1) {
  $col_cl = "12";
}
if (sizeof($title_arr) == 2) {
  $col_cl = "6";
}
if (sizeof($title_arr) == 3) {
  $col_cl = "4";
}
if (sizeof($title_arr) == 4) {
  $col_cl = "3";
}
if (sizeof($title_arr) == 5) {
  $col_cl = "2";
}
if (sizeof($title_arr) == 6) {
  $col_cl = "2";
}
if (sizeof($title_arr) >= 7) {
  $col_cl = "2";
}
?>
  <div class="stat_content">
    <div class="row">
      <?php
      for ($i = 0; $i < sizeof($title_arr); $i++) {

        if ($i == 0) {
          $type = "success";
        }
        if ($i == 1) {
          $type = "info";
        }
        if ($i == 2) {
          $type = "danger";
        }
      ?>
        <div class="content_span col-sm-<?= $col_cl ?>">
          <div class="stat_content-tilte <?= $type ?>-col"><?= $title_arr[$i] ?></div>
          <div class="stat_content-amount"><?= $content_arr[$i] ?></div>
        </div>
      <?php } ?>
    </div>
  </div>
  <!-- <div class="row"><div class="col-md-12">
       <div class="widget-badge mg_tp_10">
            <div class="label label-warning">+ <?= $percent ?> %</div>&nbsp;&nbsp;
            <label><?= $label ?></label>
        </div> 
    </div></div> -->
  <!-- <div class="row"> <div class="col-md-12">
        <div class="progress mg_bt_0">
          <div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percent ?>%"></div>
        </div>
    </div> </div> -->
<?php }

// Userdefined function for php-8 mysqli-query
function mysqlQuery($query)
{
  global $conn;
  return mysqli_query($conn, $query);
}
// Userdefined function for php-8 mysqli_real_escape_string
function mysqlREString($string)
{
  global $conn;
  if(isset($string)){
    return mysqli_real_escape_string($conn, $string);
  }else{
    return false;
  }
}

function currency_conversion($from_currency, $to_currency, $quotation_cost)
{
  $from_currency_logo = mysqli_fetch_assoc(mysqlQuery("SELECT `default_currency`,`currency_code` FROM `currency_name_master` WHERE id=" . $from_currency));
  $from_currency_logo = ($from_currency_logo['currency_code']);
  if (isset($to_currency) && $to_currency != '0' && $from_currency != $to_currency) {

    $currency_logo_d = mysqli_fetch_assoc(mysqlQuery("SELECT `default_currency`,`currency_code` FROM `currency_name_master` WHERE id=" . $to_currency));
    $currency_logo = ($currency_logo_d['currency_code']);
    
    $sq_from = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$from_currency'"));
    $from_currency_rate = $sq_from['currency_rate'];
    $sq_to = mysqli_fetch_assoc(mysqlQuery("select * from roe_master where currency_id='$to_currency'"));
    $to_currency_rate = $sq_to['currency_rate'];
    $c_amount = ($quotation_cost != 0) ? ($from_currency_rate / $to_currency_rate) * $quotation_cost : 0;
    $currency_amount = $currency_logo . ' ' . number_format($c_amount, 2);
  } else {
    $currency_amount = $from_currency_logo . ' ' . number_format(floatval($quotation_cost), 2);
  }
  return $currency_amount;
}
function get_date_user($date)
{
  if ($date != "0000-00-00" && $date != "1970-01-01" && $date != "") {
    $date = date('d-m-Y', strtotime($date));
  } else {
    $date = "";
  }
  return $date;
}

function get_date_db($date)
{
  $date = date('Y-m-d', strtotime($date));
  return $date;
}

function get_datetime_user($date)
{
  if ($date != "0000-00-00 00:00:00" && $date != "1970-01-01 00:00:00" && $date != "") {
    $date = date('d-m-Y H:i', strtotime($date));
  } else {
    $date = "";
  }
  return $date;
}
function get_datetime_db($date)
{
  $date = date('Y-m-d H:i', strtotime($date));
  return $date;
}

function begin_t()
{
mysqlQuery("START TRANSACTION");
}
function commit_t()
{
mysqlQuery("COMMIT");
}
function rollback_t()
{
mysqlQuery("ROLLBACK");
}

function get_vendor_requst_vendor_name($vendor_type, $vendor_type_id)
{

if ($vendor_type == "Hotel") {
  $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$vendor_type_id'"));
  $client_name = $sq_hotel['hotel_name'];
}
if ($vendor_type == "Transport") {
  $sq_tr = mysqli_fetch_assoc(mysqlQuery("select * from transport_agency_master where transport_agency_id='$vendor_type_id'"));
  $client_name = $sq_tr['transport_agency_name'];
}
if ($vendor_type == "DMC") {
  $sq_dmc = mysqli_fetch_assoc(mysqlQuery("select * from dmc_master where dmc_id='$vendor_type_id'"));
  $client_name = $sq_dmc['company_name'];
}
return $client_name;
}

function get_vendor_requst_vendor_email($vendor_type, $vendor_type_id)
{

if ($vendor_type == "Hotel") {
  $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$vendor_type_id'"));
  $vendor_email_id = $sq_hotel['email_id'];
}
if ($vendor_type == "Transport") {
  $sq_tr = mysqli_fetch_assoc(mysqlQuery("select * from transport_agency_master where transport_agency_id='$vendor_type_id'"));
  $vendor_email_id = $sq_tr['email_id'];
}
if ($vendor_type == "DMC") {
  $sq_dmc = mysqli_fetch_assoc(mysqlQuery("select * from dmc_master where dmc_id='$vendor_type_id'"));
  $vendor_email_id = $sq_dmc['email_id'];
}
return $vendor_email_id;
}

function get_vendor_purchase_gl_id($vendor_type, $vendor_type_id)
{

if ($vendor_type == "Hotel Vendor") {
  $gl_id = 62;
}
if ($vendor_type == "Car Rental Vendor") {
  $gl_id = 16;
}
if ($vendor_type == "Visa Vendor") {
  $gl_id = 144;
}
if ($vendor_type == "Passport Vendor") {
  $gl_id = 143;
}
if ($vendor_type == "Ticket Vendor") {
  $gl_id = 48;
}
if ($vendor_type == "Excursion Vendor") {
  $gl_id = 46;
}
if ($vendor_type == "Insurance Vendor") {
  $gl_id = 142;
}
if ($vendor_type == "Train Ticket Vendor") {
  $gl_id = 132;
}
if ($vendor_type == "Transport Vendor") {
  $gl_id = 16;
}
if ($vendor_type == "DMC Vendor") {
  $gl_id = 39;
}
if ($vendor_type == "Cruise Vendor") {
  $gl_id = 29;
}
if ($vendor_type == "Other Vendor") {
  $gl_id = '167';
}
return $gl_id;
}

function get_vendor_cancelation_gl_id($vendor_type, $vendor_type_id)
{
if ($vendor_type == "Hotel Vendor") {
  $gl_id = 65;
}
if ($vendor_type == "Car Rental Vendor") {
  $gl_id = 17;
}
if ($vendor_type == "Visa Vendor") {
  $gl_id = 162;
}
if ($vendor_type == "Passport Vendor") {
  $gl_id = 163;
}
if ($vendor_type == "Ticket Vendor") {
  $gl_id = 49;
}
if ($vendor_type == "Excursion Vendor") {
  $gl_id = 43;
}
if ($vendor_type == "Insurance Vendor") {
  $gl_id = 142;
}
if ($vendor_type == "Train Ticket Vendor") {
  $gl_id = 131;
}
if ($vendor_type == "Transport Vendor") {
  $gl_id = 17;
}
if ($vendor_type == "DMC Vendor") {
  $gl_id = 40;
}
if ($vendor_type == "Cruise Vendor") {
  $gl_id = 30;
}
if ($vendor_type == "Other Vendor") {
  $gl_id = '168';
}

return $gl_id;
}
function get_bank_book_opening_balance($bank_id = '')
{
$query = "select sum(op_balance) as sum from bank_master where 1 ";
if ($bank_id != '') {
  $query .= " and bank_id='$bank_id'";
}
$query .= " and active_flag='Active'";
$sq_bal = mysqli_fetch_assoc(mysqlQuery($query));
$opening_bal = $sq_bal['sum'];
return $opening_bal;
}
function sundry_creditor_balance_update()
{
}

function get_bank_balance_update()
{
//sum of opening balalnce of each bank
$sq_bank_balance = mysqli_fetch_assoc(mysqlQuery("select sum(opening_balance) as opening_balance from bank_master"));
//update sundry creditor opening balance
$sq_bank = mysqlQuery("update gl_master set  gl_balance='$sq_bank_balance[opening_balance]' where gl_id='15'");
}

// function bank_cash_balance_check($refund_mode, $bank_id, $refund_amount, $old_amount='')
// {
//     if($refund_mode=="Cash"){

//         $sq_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from bank_cash_book_master where payment_type='Cash' and payment_side='Credit' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//         $sq_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from bank_cash_book_master where payment_type='Cash' and payment_side='Debit' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//         $transaction_bal = $sq_credit['sum'] - $sq_debit['sum'];

//         $opening_bal = $transaction_bal;
//     }
//     else{

//         $opening_bal = get_bank_book_opening_balance($bank_id);

//         $sq_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from bank_cash_book_master where payment_type='Bank' and payment_side='Credit' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//         $sq_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from bank_cash_book_master where payment_type='Bank' and payment_side='Debit' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//         $transaction_bal = $sq_credit['sum'] - $sq_debit['sum'];

//         $opening_bal = $opening_bal+$transaction_bal;
//         //echo $opening_bal; exit;
//     }
//     if($old_amount!=""){
//         $opening_bal = $opening_bal+$old_amount;
//     }

//     //This is temporary comment for balance chack validation
//     /*if($refund_amount>$opening_bal){
//       return false;
//     }
//     else{
//       return true;
//     }*/

//     return true;

// }

// function bank_cash_balance_error_msg($refund_mode, $bank_id)
// {
//     if($refund_mode=="Cash"){
//       return "error--Not enough cash available!";
//     }
//     else{
//       return "error--Not enough cash available in selected bank!";
//     }
// }


function mail_login_box($username, $password, $link)
{
global $mail_em_style, $mail_font_family, $mail_strong_style, $mail_color, $theme_color;
global $theme_color;
$content = '

  <tr>
    <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
      <tr>
          <td colspan=2><h3>Your Login Details!</h3></td>
      </tr>
      <tr><td style="text-align:left;border: 1px solid #888888;">Username</td>   <td style="text-align:left;border: 1px solid #888888;text-decoration:none !important;color:#888888 !important;">' . $username . '</td></tr>
      <tr><td style="text-align:left;border: 1px solid #888888;">Password</td>   <td style="text-decoration:none !important;text-align:left;border: 1px solid #888888;color:#888888 !important;">' . $password . '</td></tr>
      <tr><td style="text-align:left;border: 1px solid #888888;">Login</td>   <td style="text-align:left;border: 1px solid #888888;"><a href="' . $link . '" style="text-decoration: none !important;color: ' . $theme_color . '!important;">Click For Login!</a></td></tr>
    </table>
  </tr>
  ';

  return $content;
}

function whatsapp_login_box($username, $password)
{
  $login_link = BASE_URL . 'view/customer';
  $message =
    '%0aYour%20Login%20Details%20!.%0a*Username*%20:%20' . rawurlencode($username) . '%0a*Password*%20:%20' . ($password) . '%0a*Link*%20:%20' . $login_link;;
  return $message;
}



function clean($string)
{

  return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}

function get_customer_name($booking_type,$booking_id)
{
  $customer_id = ''; 

  if($booking_type == "Visa Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select visa_id,customer_id,created_at from visa_master where visa_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_visa_booking_id($sq_booking['visa_id'],$year[0]);
  }
  else if($booking_type == "Air Ticket Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select ticket_id,customer_id,created_at from ticket_master where ticket_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_ticket_booking_id($sq_booking['ticket_id'],$year[0]);
  }
  else if($booking_type == "Train Ticket Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select train_ticket_id,customer_id,created_at from train_ticket_master where train_ticket_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_train_ticket_booking_id($sq_booking['train_ticket_id'],$year[0]);
  }
  else if($booking_type == "Group Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select id,customer_id,form_date from tourwise_traveler_details where id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['form_date']);
    $booking_id = get_group_booking_id($sq_booking['id'],$year[0]);
  }
  else if($booking_type == "Package Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,booking_date from package_tour_booking_master where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['booking_date']);
    $booking_id = get_package_booking_id($sq_booking['booking_id'],$year[0]);
  }
  else if($booking_type == "Hotel Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,created_at from hotel_booking_master where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_hotel_booking_id($sq_booking['booking_id'],$year[0]);
  }
  else if($booking_type == "Bus Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,created_at from bus_booking_master where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_bus_booking_id($sq_booking['booking_id'],$year[0]);
  }
  else if($booking_type == "Car Rental Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,created_at from car_rental_booking where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_car_rental_booking_id($sq_booking['booking_id'],$year[0]);
  }
  else if($booking_type == "Excursion Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select exc_id,customer_id,created_at from excursion_master where exc_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_exc_booking_id($sq_booking['exc_id'],$year[0]);
  }
  else if($booking_type == "Miscellaneous Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select misc_id,customer_id,created_at from miscellaneous_master where misc_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_misc_booking_id($sq_booking['misc_id'],$year[0]);
  }
  else if($booking_type == "B2C Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,created_at from b2c_sale where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_b2c_booking_id($sq_booking['booking_id'],$year[0]);
  }
  else if($booking_type == "B2B Booking"){
    $sq_booking = mysqli_fetch_assoc(mysqlQuery("select booking_id,customer_id,created_at from b2b_booking_master where booking_id='$booking_id'"));
    $customer_id = $sq_booking['customer_id'];
    $year = explode("-", $sq_booking['created_at']);
    $booking_id = get_b2b_booking_id($sq_booking['booking_id'],$year[0]);
  }

  $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
  if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){ $customer_name = $sq_customer['company_name']; }
  else{
    $customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name']; }
  return $customer_name.'='.$booking_id;
}

function get_vendor_name_report($vendor_type, $vendor_type_id)
{
  $vendor_type_val = '';
  if ($vendor_type == "Hotel Vendor") {
    $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$vendor_type_id'"));
    $vendor_type_val = $sq_hotel['hotel_name'];
  }
  if ($vendor_type == "Transport Vendor") {
    $sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from transport_agency_master where transport_agency_id='$vendor_type_id'"));
    $vendor_type_val = $sq_transport['transport_agency_name'];
  }
  if ($vendor_type == "Car Rental Vendor") {
    $sq_cra_rental_vendor = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_cra_rental_vendor['vendor_name'];
  }
  if ($vendor_type == "DMC Vendor") {
    $sq_dmc_vendor = mysqli_fetch_assoc(mysqlQuery("select * from dmc_master where dmc_id='$vendor_type_id'"));
    $vendor_type_val = $sq_dmc_vendor['company_name'];
  }
  if ($vendor_type == "Visa Vendor") {
    $sq_visa_vendor = mysqli_fetch_assoc(mysqlQuery("select * from visa_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_visa_vendor['vendor_name'];
  }
  if ($vendor_type == "Passport Vendor") {
    $sq_passport_vendor = mysqli_fetch_assoc(mysqlQuery("select * from passport_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_passport_vendor['vendor_name'];
  }
  if ($vendor_type == "Ticket Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from ticket_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['vendor_name'];
  }
  if ($vendor_type == "Train Ticket Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['vendor_name'];
  }
  if ($vendor_type == "Excursion Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from site_seeing_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['vendor_name'];
  }
  if ($vendor_type == "Insurance Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from insuarance_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['vendor_name'];
  }
  if ($vendor_type == "Other Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from other_vendors where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['vendor_name'];
  }
  if ($vendor_type == "Cruise Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from cruise_master where cruise_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['company_name'];
  }

  return $vendor_type_val;
}

function get_vendor_pan_report($vendor_type, $vendor_type_id)
{
  if ($vendor_type == "Hotel Vendor") {
    $sq_hotel = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$vendor_type_id'"));
    $vendor_type_val = $sq_hotel['pan_no'];
  }
  if ($vendor_type == "Transport Vendor") {
    $sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from transport_agency_master where transport_agency_id='$vendor_type_id'"));
    $vendor_type_val = $sq_transport['pan_no'];
  }
  if ($vendor_type == "Car Rental Vendor") {
    $sq_cra_rental_vendor = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_cra_rental_vendor['pan_no'];
  }
  if ($vendor_type == "DMC Vendor") {
    $sq_dmc_vendor = mysqli_fetch_assoc(mysqlQuery("select * from dmc_master where dmc_id='$vendor_type_id'"));
    $vendor_type_val = $sq_dmc_vendor['pan_no'];
  }
  if ($vendor_type == "Visa Vendor") {
    $sq_visa_vendor = mysqli_fetch_assoc(mysqlQuery("select * from visa_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_visa_vendor['pan_no'];
  }
  if ($vendor_type == "Ticket Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from ticket_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }
  if ($vendor_type == "Train Ticket Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }
  if ($vendor_type == "Excursion Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from site_seeing_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }
  if ($vendor_type == "Insurance Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from insuarance_vendor where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }
  if ($vendor_type == "Other Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from other_vendors where vendor_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }
  if ($vendor_type == "Cruise Vendor") {
    $sq_vendor = mysqli_fetch_assoc(mysqlQuery("select * from cruise_master where cruise_id='$vendor_type_id'"));
    $vendor_type_val = $sq_vendor['pan_no'];
  }

  return $vendor_type_val;
}
function get_supplier_info($vendor_type, $estimate_id)
{

  $sq_supplier = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where vendor_type='$vendor_type' and estimate_id='$estimate_id'"));
  $arr = array(
    'vendor_type_id' => $sq_supplier['vendor_type_id'],
    'estimate_type' => $sq_supplier['estimate_type']
  );
  return $arr;
}

function getFormatImg($format, $dest_id = 0)
{
  $imgUrl = "";
  $formatMain = "Portrait-Creative";
  switch ($format) {
    case 1:
      $formatMain = "Portrait-Standard";
      break;
    case 2:
      $formatMain = "Landscape-Standard";
      break;
    case 3:
      $formatMain = "Landscape-Creative";
      break;
    case 4:
      $formatMain = "Portrait-Creative";
      break;
    case 5:
      $formatMain = "Portrait-Advanced";
      break;
    case 6:
      $formatMain = "Landscape-Advanced";
      break;


    default:
      $formatMain = "Portrait-Standard";
      break;
  }
  if (!empty($dest_id)) {
    $format_qry = mysqlQuery("SELECT img_url FROM `format_image_master` where dest_id='$dest_id' and is_selected='1' and type='$formatMain' LIMIT 1");
    if (mysqli_num_rows($format_qry) > 0) {
      while ($db = mysqli_fetch_array($format_qry)) {
        $imgUrl = $db['img_url'];
      }
    } else {
      $wDestQry = mysqlQuery("SELECT img_url FROM `format_image_master` where dest_id='0' and is_selected='1' and type='$formatMain' LIMIT 1");
      if (mysqli_num_rows($wDestQry) > 0) {
        while ($db2 = mysqli_fetch_array($wDestQry)) {
          $imgUrl = $db2['img_url'];
        }
      }
    }
  } else {
    $wDestQry = mysqlQuery("SELECT img_url FROM `format_image_master` where dest_id='0' and is_selected='1' and type='$formatMain' LIMIT 1");
    if (mysqli_num_rows($wDestQry) > 0) {
      while ($db2 = mysqli_fetch_array($wDestQry)) {
        $imgUrl = $db2['img_url'];
      }
    }
  }
  return $imgUrl;
}
?>