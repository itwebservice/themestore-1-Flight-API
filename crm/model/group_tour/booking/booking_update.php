<?php

$flag = true;

class booking_update
{
public function complete_booking_information_update()
{

  $row_spec = 'sales';

  //** Getting tour information

  $tour_id = $_POST['tour_id']; 

  $tour_group_id = $_POST['tour_group_id'];

  $traveler_group_id = $_POST['traveler_group_id'];

  $taxation_type = $_POST['taxation_type'];

  $customer_id = $_POST['customer_id'];

  $tourwise_id = $_POST['tourwise_id'];

  //** Getting member information    

  $m_honorific = $_POST['m_honorific'];

  $m_first_name = $_POST['m_first_name'] ?: [];

  $m_middle_name = $_POST['m_middle_name'];

  $m_last_name = $_POST['m_last_name'];

  $m_gender = $_POST['m_gender'];

  $m_birth_date = $_POST['m_birth_date'];

  $m_age = $_POST['m_age'];

  $m_adolescence = $_POST['m_adolescence'];

  $m_passport_no = $_POST['m_passport_no'];

  $m_passport_issue_date = $_POST['m_passport_issue_date'];

  $m_passport_expiry_date = $_POST['m_passport_expiry_date'];

  $m_traveler_id = $_POST['m_traveler_id'];

  $m_email_id = $_POST['m_email_id'];

  $m_mobile_no = $_POST['m_mobile_no'];

  $m_address = $_POST['m_address'];

  $m_handover_adnary = $_POST['m_handover_adnary'];

  $m_handover_gift = $_POST['m_handover_gift'];
  $e_checkbox_arr = $_POST['e_checkbox_arr'];


  //**Getting relatives details

  $relative_honorofic = $_POST['relative_honorofic'];

  $relative_name = $_POST['relative_name'];

  $relative_relation = $_POST['relative_relation'];

  $relative_mobile_no = $_POST['relative_mobile_no'];



  //**Hoteling facility
  $single_bed_room = $_POST['single_bed_room'];
  $double_bed_room = $_POST['double_bed_room'];
  $extra_bed = $_POST['extra_bed'];
  $on_floor = $_POST['on_floor'];
  $single_person = $_POST['single_person'];
  $single_person_cost = $_POST['single_person_cost'];



  //** Traveling information overall

  $train_expense = $_POST['train_expense'];

  $train_service_charge = $_POST['train_service_charge'];

  $train_taxation_id = $_POST['train_taxation_id'];

  $train_service_tax = $_POST['train_service_tax'];

  $train_service_tax_subtotal = $_POST['train_service_tax_subtotal'];

  $total_train_expense = $_POST['total_train_expense'];



  $plane_expense = $_POST['plane_expense'];

  $plane_service_charge = $_POST['plane_service_charge'];

  $plane_taxation_id = $_POST['plane_taxation_id'];

  $plane_service_tax = $_POST['plane_service_tax'];

  $plane_service_tax_subtotal = $_POST['plane_service_tax_subtotal'];

  $total_plane_expense = $_POST['total_plane_expense'];

  $cruise_expense = $_POST['cruise_expense'];
  $cruise_service_charge = $_POST['cruise_service_charge'];
  $cruise_taxation_id = $_POST['cruise_taxation_id'];
  $cruise_service_tax = $_POST['cruise_service_tax'];
  $cruise_service_tax_subtotal = $_POST['cruise_service_tax_subtotal'];
  $total_cruise_expense = $_POST['total_cruise_expense'];

  
  $total_travel_expense = $_POST['total_travel_expense'];



  $train_ticket_path = $_POST['train_ticket_path'];

  $plane_ticket_path = $_POST['plane_ticket_path'];
  $cruise_ticket_path = $_POST['cruise_ticket_path'];



  //**Traveling information for train

  $train_travel_date = isset($_POST['train_travel_date']) ?$_POST['train_travel_date'] : [];
  $train_from_location = isset($_POST['train_from_location']) ? $_POST['train_from_location'] : [];
  $train_to_location = isset($_POST['train_to_location']) ? $_POST['train_to_location'] : [];
  $train_train_no = isset($_POST['train_train_no']) ? $_POST['train_train_no'] : [];
  $train_travel_class = isset($_POST['train_travel_class']) ? $_POST['train_travel_class'] : [];
  $train_travel_priority = isset($_POST['train_travel_priority']) ? $_POST['train_travel_priority'] : [];
  $train_amount = isset($_POST['train_amount']) ? $_POST['train_amount'] : [];
  $train_seats = isset($_POST['train_seats']) ? $_POST['train_seats'] : [];
  $train_id = isset($_POST['train_id']) ? $_POST['train_id'] : [];
  $train_checkbox_arr = isset($_POST['train_checkbox_arr']) ? $_POST['train_checkbox_arr']: [];



  //**Traveling information for plane
  
  $from_city_id_arr = isset($_POST['from_city_id_arr']) ? $_POST['from_city_id_arr'] : [];
  $to_city_id_arr = isset($_POST['to_city_id_arr']) ? $_POST['to_city_id_arr'] : [];
  $plane_travel_date = isset($_POST['plane_travel_date']) ?$_POST['plane_travel_date']: [];
  $plane_from_location = isset($_POST['plane_from_location']) ? $_POST['plane_from_location'] : [];
  $plane_to_location = isset($_POST['plane_to_location']) ? $_POST['plane_to_location'] : [];
  $plane_amount = isset($_POST['plane_amount']) ? $_POST['plane_amount'] : [];
  $plane_seats = isset($_POST['plane_seats']) ? $_POST['plane_seats'] : [];
  $plane_company = isset($_POST['plane_company']) ? $_POST['plane_company'] : [];
  $flight_class_arr = isset($_POST['flight_class_arr']) ? $_POST['flight_class_arr'] : [];
  $arravl_arr = isset($_POST['arravl_arr']) ? $_POST['arravl_arr'] : [];
  $plane_id = isset($_POST['plane_id']) ? $_POST['plane_id'] : [];
  $plane_checkbox_arr = isset($_POST['plane_checkbox_arr']) ? $_POST['plane_checkbox_arr'] : [];

  // Cruise
  $cruise_dept_date_arr = isset($_POST['cruise_dept_date_arr']) ? $_POST['cruise_dept_date_arr']: [];
  $cruise_arrival_date_arr = isset($_POST['cruise_arrival_date_arr']) ? $_POST['cruise_arrival_date_arr'] : [];
  $route_arr = isset($_POST['route_arr']) ? $_POST['route_arr'] : [];
  $cabin_arr = isset($_POST['cabin_arr']) ? $_POST['cabin_arr'] : [];
  $sharing_arr = isset($_POST['sharing_arr']) ? $_POST['sharing_arr'] : [];
  $cruise_seats_arr = isset($_POST['cruise_seats_arr']) ? $_POST['cruise_seats_arr']: [];
  $cruise_amount_arr = isset($_POST['cruise_amount_arr']) ? $_POST['cruise_amount_arr']: [];
  $c_entry_id_arr = isset($_POST['c_entry_id_arr']) ? $_POST['c_entry_id_arr'] : [];
  $cruise_checkbox_arr = isset($_POST['cruise_checkbox_arr']) ? $_POST['cruise_checkbox_arr'] : [];

  //**Visa Details

  $visa_country_name = $_POST['visa_country_name'];

  $visa_amount = $_POST['visa_amount'];

  $visa_service_charge = $_POST['visa_service_charge'];

  $visa_taxation_id = $_POST['visa_taxation_id'];

  $visa_service_tax = $_POST['visa_service_tax'];

  $visa_service_tax_subtotal = $_POST['visa_service_tax_subtotal'];

  $visa_total_amount = $_POST['visa_total_amount'];



  $insuarance_company_name = $_POST['insuarance_company_name'];

  $insuarance_amount = $_POST['insuarance_amount'];

  $insuarance_service_charge = $_POST['insuarance_service_charge'];

  $insuarance_taxation_id = $_POST['insuarance_taxation_id'];

  $insuarance_service_tax = $_POST['insuarance_service_tax'];

  $insuarance_service_tax_subtotal = $_POST['insuarance_service_tax_subtotal'];

  $insuarance_total_amount = $_POST['insuarance_total_amount'];



  //**Discount details

  $adult_expense = $_POST['adult_expense'];              

  $child_b_expense = $_POST['child_b_expense'];
  $child_wb_expense = $_POST['child_wb_expense'];

  $infant_expense = $_POST['infant_expense'];

  $tour_fee = $_POST['tour_fee'];
  $repeater_discount = $_POST['repeater_discount'];
  $adjustment_discount = $_POST['adjustment_discount'];
  $tour_fee_subtotal_1 = $_POST['tour_fee_subtotal_1'];
  $tour_taxation_id = $_POST['tour_taxation_id'];
  $service_tax_per = $_POST['service_tax_per'];
  $service_tax = $_POST['service_tax'];
  $tour_fee_subtotal_2 = $_POST['tour_fee_subtotal_2'];
  $net_total = $_POST['net_total'];
  $old_total = $_POST['old_total'];
  $roundoff = $_POST['roundoff'];
  $total_discount = $_POST['total_discount'];
  $tcs_tax = $_POST['tcs_tax'];
  $tcs = $_POST['tcs'];

  $reflections = json_decode(json_encode($_POST['reflections']));
  $currency_code = $_POST['currency_code'];
  
  $basic_amount = $_POST['basic_amount'];
  $special_request = $_POST['special_request'];   
  $special_request = addslashes($special_request);

  $bsmValues = json_decode(json_encode($_POST['bsmValues']));
  foreach($bsmValues[0] as $key => $value){
    switch($key){
    case 'basic' : $basic_amount = ($value != "") ? $value : $basic_amount;break;
    case 'discount' : $total_discount = ($value != "") ? $value : $total_discount;break;
    }
  }
  $reflections = json_encode($reflections);
  $bsmValues = json_encode($bsmValues);
  begin_t();  





  //** This function saves travelers details and returns traveleres group id.

   $this->traveler_member_details_update($traveler_group_id, $m_honorific, $m_first_name, $m_middle_name, $m_last_name, $m_gender, $m_birth_date, $m_age, $m_adolescence, $m_passport_no, $m_passport_issue_date, $m_passport_expiry_date, $m_handover_adnary, $m_handover_gift, $m_traveler_id,$e_checkbox_arr);

  



  //** This function saves tourwise traveler details and returns tourwise traveler id

   $this->tourwise_traveler_detail_update($traveler_group_id, $tour_id, $tour_group_id, $taxation_type, $customer_id, $tourwise_id, $relative_honorofic, $relative_name, $relative_relation, $relative_mobile_no, $single_bed_room, $double_bed_room, $extra_bed, $on_floor, $train_expense, $train_service_charge, $train_taxation_id, $train_service_tax, $train_service_tax_subtotal, $total_train_expense, $plane_expense, $plane_service_charge, $plane_taxation_id, $plane_service_tax, $plane_service_tax_subtotal, $total_plane_expense, $cruise_expense, $cruise_service_charge, $cruise_taxation_id, $cruise_service_tax, $cruise_service_tax_subtotal, $total_cruise_expense, $total_travel_expense, $train_ticket_path, $plane_ticket_path, $cruise_ticket_path, $visa_country_name, $visa_amount, $visa_service_charge, $visa_taxation_id, $visa_service_tax, $visa_service_tax_subtotal, $visa_total_amount, $insuarance_company_name, $insuarance_amount, $insuarance_service_charge, $insuarance_taxation_id, $insuarance_service_tax, $insuarance_service_tax_subtotal, $insuarance_total_amount, $adult_expense, $child_b_expense, $child_wb_expense, $infant_expense, $tour_fee, $repeater_discount, $adjustment_discount, $tour_fee_subtotal_1, $tour_taxation_id, $service_tax_per, $service_tax, $tour_fee_subtotal_2, $net_total, 0, $special_request,$reflections,$basic_amount,$roundoff,$bsmValues,$total_discount,$currency_code,$tcs_tax,$tcs,$single_person,$single_person_cost);



  //**Traveler ersonal information save
  $this->traveler_personal_information_update($tourwise_id, $m_email_id, $m_mobile_no, $m_address);

  //** This function stores the traveling information
  $this->update_traveling_information( $tourwise_id, $train_travel_date, $train_from_location, $train_to_location, $train_train_no, $train_travel_class, $train_travel_priority, $train_amount, $train_seats, $train_id, $plane_travel_date, $from_city_id_arr, $plane_from_location, $to_city_id_arr, $plane_to_location, $plane_amount, $plane_seats, $plane_company, $plane_id, $arravl_arr,$cruise_dept_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr, $cruise_seats_arr, $cruise_amount_arr,$c_entry_id_arr,$train_checkbox_arr,$plane_checkbox_arr,$cruise_checkbox_arr,$flight_class_arr);

  //**=============**Finance Entries update start**============**///Get Particular
  $particular = $this->get_particular($customer_id,$tour_id,$tour_group_id,$tourwise_id);
  $booking_save_transaction = new booking_update_transaction;
  $sq_date = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$tourwise_id' and delete_status='0'"));
  $booking_save_transaction->finance_update($tourwise_id, $row_spec, $sq_date['form_date'],$particular);
  //**=============**Finance Entries update end**============**//

  /////////////////// // Update entries log//////////////////////
  global $transaction_master;
  if(floatval($old_total) != floatval($net_total)){

    $date = $sq_date['form_date'];
    $yr = explode("-", $date);
    $year = $yr[0];
    $sq_ct = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
    if($sq_ct['type']=='Corporate'||$sq_ct['type'] == 'B2B'){
      $cust_name = $sq_ct['company_name'];
    }else{
      $cust_name = $sq_ct['first_name'].' '.$sq_ct['last_name'];
    }

    $trans_id = get_group_booking_id($tourwise_id,$year).' : '.$cust_name;
    $transaction_master->updated_entries('Group Tour Sale',$tourwise_id,$trans_id,$old_total,$net_total);
  }
  /////////////////// // Update entries log end//////////////////

  if($GLOBALS['flag']){
    commit_t();
    echo "Group Tour has been successfully updated.";
    exit;
  }

  else{

    rollback_t();

    exit;

  }
}
function get_particular($customer_id,$tour_id,$tour_group_id,$id){

  $sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$id'"));
  $date = $sq_booking['form_date'];
  $yr = explode("-", $date);
  $year = $yr[0];

	$pass_count= mysqli_num_rows(mysqlQuery("select * from travelers_details where traveler_group_id='$sq_booking[traveler_group_id]' and status!='Cancel'"));
	$sq_pass= mysqli_fetch_assoc(mysqlQuery("select * from travelers_details where traveler_group_id='$sq_booking[traveler_group_id]' and status!='Cancel'"));

  $sq_ct = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from customer_master where customer_id='$customer_id'"));
  $cust_name = $sq_ct['first_name'].' '.$sq_ct['last_name'];
  $sq_tour = mysqli_fetch_assoc(mysqlQuery("select tour_name from tour_master where tour_id='$tour_id'"));
  $tour_name = $sq_tour['tour_name'];
  $sq_tourgroup = mysqli_fetch_assoc(mysqlQuery("select from_date,to_date from tour_groups where group_id='$tour_group_id'"));
  $from_date = new DateTime($sq_tourgroup['from_date']);
  $to_date = new DateTime($sq_tourgroup['to_date']);
  $numberOfNights= $from_date->diff($to_date)->format("%a");

  return get_group_booking_id($id,$year).' and '.$tour_name.' for '.$cust_name. '('.$sq_pass['first_name'].' '.$sq_pass['last_name'].') *'.$pass_count.' for '.$numberOfNights.' Nights starting from '.get_date_user($sq_tourgroup['from_date']);
}

///////////////////////////////////***** Updating Traveleres Members Information *********/////////////////////////////////////////////////////////////

public function traveler_member_details_update($traveler_group_id, $m_honorific, $m_first_name, $m_middle_name, $m_last_name, $m_gender, $m_birth_date, $m_age, $m_adolescence, $m_passport_no, $m_passport_issue_date, $m_passport_expiry_date, $m_handover_adnary, $m_handover_gift, $m_traveler_id,$e_checkbox_arr)
{  

    $m_handover_adnary = mysqlREString($m_handover_adnary);    
    $m_handover_gift = mysqlREString($m_handover_gift);

  for($i=0; $i<sizeof($m_first_name); $i++)
  {    

    $m_honorific[$i] = mysqlREString($m_honorific[$i]);

    $m_first_name[$i] = mysqlREString($m_first_name[$i]);

    $m_middle_name[$i] = mysqlREString($m_middle_name[$i]);

    $m_last_name[$i] = mysqlREString($m_last_name[$i]);

    $m_gender[$i] = mysqlREString($m_gender[$i]);

    $m_birth_date[$i] = mysqlREString($m_birth_date[$i]);

    $m_age[$i] = mysqlREString($m_age[$i]);

    $m_adolescence[$i] = mysqlREString($m_adolescence[$i]);  

    $m_passport_no[$i] = mysqlREString($m_passport_no[$i]);

    $m_passport_issue_date[$i] = mysqlREString($m_passport_issue_date[$i]);

    $m_passport_expiry_date[$i] = mysqlREString($m_passport_expiry_date[$i]);  

    $m_traveler_id[$i] = mysqlREString($m_traveler_id[$i]);

    $count_travelers = mysqli_num_rows(mysqlQuery("select * from travelers_details where traveler_id='$m_traveler_id[$i]'"));

    if($m_birth_date[$i]!=""){  $m_birth_date[$i] = date("Y-m-d", strtotime($m_birth_date[$i])); }

    if($m_passport_issue_date[$i]!=""){  $m_passport_issue_date[$i] = date("Y-m-d", strtotime($m_passport_issue_date[$i])); }

    if($m_passport_expiry_date[$i]!=""){  $m_passport_expiry_date[$i] = date("Y-m-d", strtotime($m_passport_expiry_date[$i])); }
    
		if($e_checkbox_arr[$i] == 'true'){
      if($count_travelers == '1')
      {    
          $sq = mysqlQuery("update travelers_details set  m_honorific = '$m_honorific[$i]', first_name = '$m_first_name[$i]', middle_name = '$m_middle_name[$i]', last_name = '$m_last_name[$i]', gender = '$m_gender[$i]', birth_date = '$m_birth_date[$i]', age = '$m_age[$i]', adolescence = '$m_adolescence[$i]', passport_no='$m_passport_no[$i]', passport_issue_date='$m_passport_issue_date[$i]', passport_expiry_date='$m_passport_expiry_date[$i]', handover_adnary = '$m_handover_adnary', handover_gift = '$m_handover_gift', status = 'Active' where traveler_id='$m_traveler_id[$i]'");
          if(!$sq)
          {
            $GLOBALS['flag'] = false;
            echo "Error at row".($i+1)." for traveler members.";
            //exit;
          }
      }   
      else
      {  
          $sq = mysqlQuery("select max(traveler_id) as max from travelers_details");
          $value = mysqli_fetch_assoc($sq);
          $max_traveler_id = $value['max'] + 1;       

          $query = "insert into travelers_details (traveler_id, traveler_group_id, m_honorific, first_name, middle_name, last_name, gender, birth_date, age, adolescence, passport_no, passport_issue_date, passport_expiry_date, handover_adnary, handover_gift, status) values ('$max_traveler_id','$traveler_group_id', '$m_honorific[$i]', '$m_first_name[$i]', '$m_middle_name[$i]', '$m_last_name[$i]', '$m_gender[$i]', '$m_birth_date[$i]', '$m_age[$i]', '$m_adolescence[$i]', '$m_passport_no[$i]', '$m_passport_issue_date[$i]', '$m_passport_expiry_date[$i]', '$m_handover_adnary', '$m_handover_gift', 'Active')";        
          $sq = mysqlQuery($query); 

          if(!$sq)
          {
            $GLOBALS['flag'] = false;
            echo "Error at row".($i+1)." for traveler members.";
            //exit;
          }
      }  
    }else{
      
			$sq = mysqlQuery("delete from travelers_details where traveler_id='$m_traveler_id[$i]'");
      if(!$sq)
      {
        $GLOBALS['flag'] = false;
        echo "Error at row".($i+1)." for traveler members.";
        //exit;
      }
    }
}  



}







///////////////////////////////////***** Updating Tourwise Traveler Information *********/////////////////////////////////////////////////////////////

public function tourwise_traveler_detail_update($traveler_group_id, $tour_id, $tour_group_id, $taxation_type, $customer_id, $tourwise_id, $relative_honorofic, $relative_name, $relative_relation, $relative_mobile_no, $single_bed_room, $double_bed_room, $extra_bed, $on_floor, $train_expense, $train_service_charge, $train_taxation_id, $train_service_tax, $train_service_tax_subtotal, $total_train_expense, $plane_expense, $plane_service_charge, $plane_taxation_id, $plane_service_tax, $plane_service_tax_subtotal, $total_plane_expense, $cruise_expense, $cruise_service_charge, $cruise_taxation_id, $cruise_service_tax, $cruise_service_tax_subtotal, $total_cruise_expense, $total_travel_expense, $train_ticket_path, $plane_ticket_path, $cruise_ticket_path, $visa_country_name, $visa_amount, $visa_service_charge, $visa_taxation_id, $visa_service_tax, $visa_service_tax_subtotal, $visa_total_amount, $insuarance_company_name, $insuarance_amount, $insuarance_service_charge, $insuarance_taxation_id, $insuarance_service_tax, $insuarance_service_tax_subtotal, $insuarance_total_amount, $adult_expense, $child_b_expense, $child_wb_expense, $infant_expense, $tour_fee, $repeater_discount, $adjustment_discount, $tour_fee_subtotal_1, $tour_taxation_id, $service_tax_per, $service_tax, $tour_fee_subtotal_2, $net_total, $current_booked_seats, $special_request,$reflections,$basic_amount,$roundoff,$bsmValues,$total_discount,$currency_code,$tcs_tax,$tcs,$single_person,$single_person_cost)

{  

  $sq = mysqlQuery("update tourwise_traveler_details set tour_id='$tour_id', tour_group_id='$tour_group_id', taxation_type='$taxation_type', customer_id='$customer_id', relative_honorofic = '$relative_honorofic', relative_name = '$relative_name', relative_relation = '$relative_relation', relative_mobile_no = '$relative_mobile_no', s_double_bed_room = '$double_bed_room', s_single_bed_room = '$single_bed_room', s_extra_bed = '$extra_bed', s_on_floor = '$on_floor', train_expense='$train_expense', train_service_charge='$train_service_charge', train_taxation_id='$train_taxation_id', train_service_tax='$train_service_tax', train_service_tax_subtotal='$train_service_tax_subtotal', total_train_expense = '$total_train_expense', plane_expense='$plane_expense', plane_service_charge='$plane_service_charge', plane_taxation_id='$plane_taxation_id', plane_service_tax='$plane_service_tax', plane_service_tax_subtotal='$plane_service_tax_subtotal', total_plane_expense = '$total_plane_expense',cruise_expense='$cruise_expense', cruise_service_charge='$cruise_service_charge', cruise_taxation_id='$cruise_taxation_id', cruise_service_tax='$cruise_service_tax', cruise_service_tax_subtotal='$cruise_service_tax_subtotal', total_cruise_expense='$total_cruise_expense', total_travel_expense = '$total_travel_expense', train_upload_ticket = '$train_ticket_path', plane_upload_ticket = '$plane_ticket_path', cruise_upload_ticket = '$cruise_ticket_path', visa_country_name='$visa_country_name', visa_amount='$visa_amount', visa_service_charge='$visa_service_charge', visa_taxation_id='$visa_taxation_id', visa_service_tax='$visa_service_tax', visa_service_tax_subtotal='$visa_service_tax_subtotal', visa_total_amount='$visa_total_amount', insuarance_company_name='$insuarance_company_name', insuarance_amount='$insuarance_amount', insuarance_service_charge='$insuarance_service_charge', insuarance_taxation_id='$insuarance_taxation_id', insuarance_service_tax='$insuarance_service_tax', insuarance_service_tax_subtotal='$insuarance_service_tax_subtotal', insuarance_total_amount='$insuarance_total_amount', adult_expense = '$adult_expense', child_with_bed = '$child_b_expense', child_without_bed = '$child_wb_expense',infant_expense = '$infant_expense', tour_fee = '$tour_fee', repeater_discount = '$repeater_discount', adjustment_discount = '$adjustment_discount', tour_fee_subtotal_1='$tour_fee_subtotal_1', tour_taxation_id='$tour_taxation_id', service_tax_per = '$service_tax_per', service_tax = '$service_tax', tour_fee_subtotal_2='$tour_fee_subtotal_2', net_total = '$net_total', current_booked_seats = '$current_booked_seats', special_request = '$special_request',reflections='$reflections',basic_amount = '$basic_amount',roundoff='$roundoff',bsm_values='$bsmValues' ,total_discount='$total_discount',currency_code='$currency_code',tcs_tax='$tcs_tax',tcs_per='$tcs',single_person='$single_person',single_person_cost='$single_person_cost' where id = '$tourwise_id'");

    if(!$sq)

    {

      $GLOBALS['flag'] = false;

      echo "Booking details not updated.";

      //exit;

    }  



    if(!$sq)

    {

      $GLOBALS['flag'] = false;

      echo "<br><b>Travelere adjust with other not Saved!</b><br>";

      //exit;

    }      

}



///////////////////////////////////***** Traveler personal information save*********/////////////////////////////////////////////////////////////

public function traveler_personal_information_update($tourwise_traveler_id, $m_email_id, $m_mobile_no, $m_address)

{

	  $m_email_id = mysqlREString($m_email_id);

    $m_mobile_no = mysqlREString($m_mobile_no);

    $m_address = mysqlREString($m_address);





    $sq = mysqlQuery("update traveler_personal_info set  email_id='$m_email_id', mobile_no='$m_mobile_no', address='$m_address' where tourwise_traveler_id='$tourwise_traveler_id'");



    if(!$sq){

      $GLOBALS['flag'] = false;

    	echo "Traveler personal information not updated.";

    	//exit;

    }

}







///////////////////////////////////***** Updating Traveling Information *********/////////////////////////////////////////////////////////////

 public function update_traveling_information( $tourwise_id, $train_travel_date, $train_from_location, $train_to_location, $train_train_no, $train_travel_class, $train_travel_priority, $train_amount, $train_seats, $train_id, $plane_travel_date, $from_city_id_arr, $plane_from_location, $to_city_id_arr,$plane_to_location, $plane_amount, $plane_seats, $plane_company, $plane_id, $arravl_arr,$cruise_dept_date_arr, $cruise_arrival_date_arr, $route_arr, $cabin_arr, $sharing_arr, $cruise_seats_arr, $cruise_amount_arr,$c_entry_id_arr,$train_checkbox_arr,$plane_checkbox_arr,$cruise_checkbox_arr,$flight_class_arr )

 {



      //**Saves Train Information    

      

      for($i=0; $i<sizeof($train_travel_date); $i++)

      {        



        $train_travel_date[$i] = mysqlREString($train_travel_date[$i]);

        $train_from_location[$i] = mysqlREString($train_from_location[$i]);

        $train_to_location[$i] = mysqlREString($train_to_location[$i]);

        $train_train_no[$i] = mysqlREString($train_train_no[$i]);

        $train_travel_class[$i] = mysqlREString($train_travel_class[$i]);

        $train_travel_priority[$i] = mysqlREString($train_travel_priority[$i]);

        $train_amount[$i] = mysqlREString($train_amount[$i]);

        $train_seats[$i] = mysqlREString($train_seats[$i]);



        $train_travel_date[$i] = date("Y-m-d H:i", strtotime($train_travel_date[$i]));



        $count_train_details = mysqli_num_rows(mysqlQuery("select * from train_master where train_id='$train_id[$i]'"));


				if($train_checkbox_arr[$i] == 'true'){

          if($count_train_details == 1)

          {

              $sq = mysqlQuery("update train_master set date = '$train_travel_date[$i]', from_location = '$train_from_location[$i]', to_location = '$train_to_location[$i]', train_no = '$train_train_no[$i]', train_priority = '$train_travel_priority[$i]', train_class = '$train_travel_class[$i]', amount = '$train_amount[$i]', seats = '$train_seats[$i]'  where train_id = '$train_id[$i]' and tourwise_traveler_id = '$tourwise_id'");

              if(!$sq)

              {

                  $GLOBALS['flag'] = false;

                  echo "Train Details Not Saved";

                  //exit;

              } 



          }  

          else

          {  

            $sq = mysqlQuery("select max(train_id) as max from train_master");

            $value = mysqli_fetch_assoc($sq);

            $max_train_id = $value['max'] + 1;;



            $sq = mysqlQuery(" insert into train_master (train_id, tourwise_traveler_id, date, from_location, to_location, train_no, amount, seats, train_priority, train_class ) values ('$max_train_id', '$tourwise_id', '$train_travel_date[$i]', '$train_from_location[$i]', '$train_to_location[$i]', '$train_train_no[$i]', '$train_amount[$i]', '$train_seats[$i]', '$train_travel_priority[$i]', '$train_travel_class[$i]') ");

              if(!$sq)

              {

                $GLOBALS['flag'] = false;

                echo "Error at row".($i+1)." for train information.";

                //exit;

              }   



          }
        }
        else{
					$sq_entry = mysqlQuery("delete from train_master where train_id = '$train_id[$i]' and tourwise_traveler_id = '$tourwise_id'");
					if(!$sq_entry){
						$GLOBALS['flag'] = false;
						echo "error--Some entries not deleted!";
					}
				}





      }  

     



      //**Saves Plane Information    

      

      for($i=0; $i<sizeof($plane_travel_date); $i++)

      {

        
        $plane_travel_date[$i] = mysqlREString($plane_travel_date[$i]);
         $from_city_id_arr[$i] = mysqlREString($from_city_id_arr[$i]);

        $plane_from_location[$i] = mysqlREString($plane_from_location[$i]);

        $to_city_id_arr[$i] = mysqlREString($to_city_id_arr[$i]);

        $plane_to_location[$i] = mysqlREString($plane_to_location[$i]);

        $plane_amount[$i] = mysqlREString($plane_amount[$i]);

        $plane_seats[$i] = mysqlREString($plane_seats[$i]);

        $plane_company[$i] = mysqlREString($plane_company[$i]);

        $arravl_arr[$i] = mysqlREString($arravl_arr[$i]);
        $flight_class_arr[$i] = mysqlREString($flight_class_arr[$i]);

      
        $plane_travel_date[$i] = date("Y-m-d H:i", strtotime($plane_travel_date[$i]));
        $from_location = array_slice(explode(' - ', $plane_from_location[$i]), 1);
        $from_location = implode(' - ',$from_location);
        $to_location = array_slice(explode(' - ', $plane_to_location[$i]), 1);
        $to_location = implode(' - ',$to_location);


        $plane_travel_date[$i] = date("Y-m-d H:i", strtotime($plane_travel_date[$i]));

        $arravl_arr[$i] = date('Y-m-d H:i', strtotime($arravl_arr[$i]));





        $count_plane_details = mysqli_num_rows(mysqlQuery("select * from plane_master where plane_id='$plane_id[$i]'"));

				if($plane_checkbox_arr[$i] == 'true'){

          if($count_plane_details == 1)

          {

            $sq = mysqlQuery("update plane_master set date = '$plane_travel_date[$i]', from_location = '$from_location', to_location = '$to_location', company = '$plane_company[$i]', amount = '$plane_amount[$i]', seats = '$plane_seats[$i]', arraval_time = '$arravl_arr[$i]', from_city='$from_city_id_arr[$i]', to_city='$to_city_id_arr[$i]',class='$flight_class_arr[$i]'  where plane_id = '$plane_id[$i]' and tourwise_traveler_id = '$tourwise_id'");

            if(!$sq)

            {

                $GLOBALS['flag'] = false;

                echo "Plane Details Not Saved";

                //exit;

            } 

          }  

          else

          {  

            $sq = mysqlQuery("select max(plane_id) as max from plane_master");

            $value = mysqli_fetch_assoc($sq);

            $max_plane_id = $value['max'] + 1;

            $sq = mysqlQuery(" insert into plane_master (plane_id, tourwise_traveler_id, date, from_city, to_city, from_location, to_location, company, amount, seats, arraval_time,class ) values ('$max_plane_id', '$tourwise_id', '$plane_travel_date[$i]', '$from_city_id_arr[$i]', '$to_city_id_arr[$i]', '$from_location', '$to_location', '$plane_company[$i]', '$plane_amount[$i]', '$plane_seats[$i]', '$arravl_arr[$i]','$flight_class_arr[$i]') ");



            if(!$sq)

            {

              $GLOBALS['flag'] = false;

              echo "Error at row".($i+1)." for Flight information.";

              //exit;

            } 

          } 
        }
        else{
					$sq_entry = mysqlQuery("delete from plane_master where plane_id = '$plane_id[$i]' and tourwise_traveler_id = '$tourwise_id'");
					if(!$sq_entry){
						$GLOBALS['flag'] = false;
						echo "error--Some entries not deleted!";
					}
				}

      }
      //**Saves Cruise Information    

      

      for($i=0; $i<sizeof($cruise_dept_date_arr); $i++)
      {

        $cruise_dept_date_arr[$i] = mysqlREString($cruise_dept_date_arr[$i]);
        $cruise_arrival_date_arr[$i] = mysqlREString($cruise_arrival_date_arr[$i]);
        $route_arr[$i] = mysqlREString($route_arr[$i]);
        $cabin_arr[$i] = mysqlREString($cabin_arr[$i]);
        $sharing_arr[$i] = mysqlREString($sharing_arr[$i]);
        $cruise_seats_arr[$i] = mysqlREString($cruise_seats_arr[$i]);
        $cruise_amount_arr[$i] = mysqlREString($cruise_amount_arr[$i]);
        $c_entry_id_arr[$i] = mysqlREString($c_entry_id_arr[$i]);

        $cruise_dept_date_arr[$i] = date("Y-m-d, H:i", strtotime($cruise_dept_date_arr[$i]));        
        $cruise_arrival_date_arr[$i] = date("Y-m-d, H:i", strtotime($cruise_arrival_date_arr[$i])); 

				if($cruise_checkbox_arr[$i] == 'true'){
          if($c_entry_id_arr[$i] == ''){
              $sq = mysqlQuery("select max(cruise_id) as max from group_cruise_master");
              $value = mysqli_fetch_assoc($sq);
              $max_cruise_id = $value['max'] + 1;
              $sq = mysqlQuery("insert into group_cruise_master (cruise_id, booking_id, dept_datetime, arrival_datetime, route, cabin,sharing, seats, amount ) values ('$max_cruise_id', '$tourwise_id', '$cruise_dept_date_arr[$i]', '$cruise_arrival_date_arr[$i]', '$route_arr[$i]', '$cabin_arr[$i]', '$sharing_arr[$i]', '$cruise_seats_arr[$i]', '$cruise_amount_arr[$i]') ");
              if(!$sq)
              {
                $GLOBALS['flag'] = false;
                echo "Error at row".($i+1)." for cruise information.";
              }
          }
          else{
              $sq = mysqlQuery("update group_cruise_master set dept_datetime = '$cruise_dept_date_arr[$i]', arrival_datetime = '$cruise_arrival_date_arr[$i]', route = '$route_arr[$i]', cabin = '$cabin_arr[$i]', sharing = '$sharing_arr[$i]', seats = '$cruise_seats_arr[$i]', amount = '$cruise_amount_arr[$i]'  where cruise_id = '$c_entry_id_arr[$i]' and booking_id = '$tourwise_id'");
          }   
        }else{
					$sq_entry = mysqlQuery("delete from group_cruise_master where cruise_id = '$c_entry_id_arr[$i]' and booking_id = '$tourwise_id'");
					if(!$sq_entry){
						$GLOBALS['flag'] = false;
						echo "error--Some entries not deleted!";
					}

        }





      }  
    



 }





}

?>