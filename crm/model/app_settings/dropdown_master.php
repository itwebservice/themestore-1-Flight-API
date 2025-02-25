<?php
function get_b2c_booking_dropdown(){
  $query = "select * from b2c_sale where 1";
  $sq_booking = mysqlQuery($query);
  while($row_booking = mysqli_fetch_assoc($sq_booking)){
    $date = $row_booking['created_at'];
    $yr = explode("-", $date);
    $year = $yr[0];
    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
    ?>
    <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_b2c_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['first_name'].' '.$sq_customer['last_name']; ?></option>
    <?php
  }
}

//**Group Booking Dropdown
function get_group_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id=''){
  $financial_year_id = $_SESSION['financial_year_id'];
  ?>
  <option value="" >Select Booking</option>
  <?php 
    $query = "select * from tourwise_traveler_details where financial_year_id='$financial_year_id' and delete_status='0' ";
    include "branchwise_filteration.php";
    $query .= " and tour_group_status != 'Cancel'";
    $query .= " order by id desc";
    $sq_booking = mysqlQuery($query);
    while($row_booking = mysqli_fetch_assoc($sq_booking)){

    $date = $row_booking['form_date'];
    $yr = explode("-", $date);
    $year =$yr[0];

    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
    if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
        ?>
        <option value="<?php echo $row_booking['id'] ?>"><?php echo get_group_booking_id($row_booking['id'],$year)."-"." ".$sq_customer['company_name']; ?></option>
        <?php }
        else{ ?> 

      <option value="<?= $row_booking['id'] ?>"><?= get_group_booking_id($row_booking['id'],$year) ?> : <?= $sq_customer['first_name'].' '.$sq_customer['last_name'] ?></option>
      <?php
    }
  }
}

//Package Booking Dropdown
function get_package_booking_dropdown($role='', $branch_admin_id='', $branch_status='',$emp_id='',$role_id='', $cancelled_show = true){
  ?>
  <option value="">*Select Booking</option>
  <?php
      $query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
      include "branchwise_filteration.php";
      $query .= " order by booking_id desc";
      $sq_booking = mysqlQuery($query);
      while($row_booking = mysqli_fetch_assoc($sq_booking)){
        if(!$cancelled_show){
          $pass_count= mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]'"));
          $cancle_count= mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]' and status='Cancel'"));
          if($pass_count==$cancle_count){
            continue;
          }
        }
        $date = $row_booking['booking_date'];
        $yr = explode("-", $date);
        $year =$yr[0];
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
          if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
           ?>
           <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['company_name']; ?></option>
           <?php }
           else{ ?> 
           <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name']; ?></option>
           <?php    
         }
      }
}

//user Reflect
function get_user_dropdown($role, $branch_admin_id, $branch_status,$emp_id){
  ?>

 <option value="">Booked By</option>

  <?php 
    $query = "select * from emp_master where 1";
      if($branch_status=='yes'){
    if($role=='Branch Admin'){
    $query .= " and branch_id = '$branch_admin_id'";
    } 
    elseif($role!='Admin' && $role!='Branch Admin'){
      $query .= " and emp_id='$emp_id'";
      }
    }
      $query .= " order by first_name";
      $sq_emp = mysqlQuery($query);
      while($row_emp = mysqli_fetch_assoc($sq_emp)){

        ?>
        <option value="<?= $row_emp['emp_id'] ?>"><?= $row_emp['first_name'].' '.$row_emp['last_name'] ?></option>
        <?php
      }
}

//Branch Reflect
function get_branch_dropdown($role, $branch_admin_id, $branch_status)

{

  ?>

 <option value="">Branch</option>

  <?php 
    $query = "select * from branches where 1";
      if($branch_status=='yes' && $role!='Admin'){
    $query .= " and branch_id = '$branch_admin_id'";
    } 
    $query .= " order by branch_name";
    $sq_branch = mysqlQuery($query);
    while($row_branch = mysqli_fetch_assoc($sq_branch)){

      ?>
      <option value="<?=  $row_branch['branch_id'] ?>"><?= $row_branch['branch_name'] ?></option>
      <?php
    } 
}
//Hotel Booking Dropdown

function get_hotel_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id='', $showCancelled = true)

{ ?>

    <option value="">Select Booking</option>
    <?php 
      $query = "select * from hotel_booking_master where 1 and delete_status='0' ";
      include "branchwise_filteration.php";
      $query .= " order by booking_id desc";
      $sq_booking = mysqlQuery($query);

      while($row_booking = mysqli_fetch_assoc($sq_booking))
      {
            $date = $row_booking['created_at'];
            $yr = explode("-", $date);
            $year =$yr[0];
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));

          if($sq_customer['type']=='Corporate'||$sq_customer['type']=='B2B'){ ?>
            <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_hotel_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['company_name']; ?></option>
          <?php } 
          else{ ?>
            <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_hotel_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name']; ?></option>

          <?php  }  

      }    

}
//Bus booking dropdown
function get_bus_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id='')

{ ?>

    <option value="">Booking ID</option>
    <?php 
      $query = "select * from bus_booking_master where 1 and delete_status='0' ";
      include "branchwise_filteration.php";
      $query .= " and financial_year_id = '".$_SESSION['financial_year_id']."' order by booking_id desc";
      $sq_booking = mysqlQuery($query);

      while($row_booking = mysqli_fetch_assoc($sq_booking))

      {
        $date = $row_booking['created_at'];
        $yr = explode("-", $date);
        $year =$yr[0];
        $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
        if($sq_customer['type']=='Corporate'||$sq_customer['type']=='B2B'){ ?>
          <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_bus_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['company_name']; ?></option>
        <?php } 
        else{ ?>
          <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_bus_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name']; ?></option>

          <?php
        }
      }    

}

//Hotel Booking Dropdown
function get_car_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id='')
{ ?>
    <option value="">*Booking ID</option>
    <?php
    $query = "select * from car_rental_booking where 1 and delete_status='0' ";
    // $query .= " and status!='Cancel'";
    include "branchwise_filteration.php";
    $query .= " and financial_year_id = '".$_SESSION['financial_year_id']."' order by booking_id desc";
    $sq_booking = mysqlQuery($query);

    while($row_booking = mysqli_fetch_assoc($sq_booking))
    {
      $date = $row_booking['created_at'];
      $yr = explode("-", $date);
      $year =$yr[0];
      $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));

        if($sq_customer['type']=='Corporate'||$sq_customer['type']=='B2B'){
              ?>
            <option value="<?= $row_booking['booking_id'] ?>"><?= get_car_rental_booking_id($row_booking['booking_id'],$year)." : ".$sq_customer['company_name'] ?></option>
        <?php }  else{ ?>
            <option value="<?= $row_booking['booking_id'] ?>"><?= get_car_rental_booking_id($row_booking['booking_id'],$year)." : ".$sq_customer['first_name'].' '.$sq_customer['last_name'] ?></option>
        <?php }
      }
}
//Passport Booking Dropdown

function get_passport_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id, $role_id='',$financial_year='')
{ ?>
      <option value="">Booking ID</option>
      <?php
      $query = "select * from passport_master where 1 ";
      include "branchwise_filteration.php";
      $query .= " and financial_year_id = '".$_SESSION['financial_year_id']."'  order by passport_id desc";
      $sq_booking = mysqlQuery($query);

      while($row_booking = mysqli_fetch_assoc($sq_booking)){
          $date = $row_booking['created_at'];
          $yr = explode("-", $date);
          $year =$yr[0];
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
          ?>
            <option value="<?php echo $row_booking['passport_id'] ?>"><?php echo get_passport_booking_id($row_booking['passport_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name']; ?></option>
          <?php
      }
}

//**Forex Booking Dropdown
function get_forex_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id='')
{
  ?>

  <option value="">Booking ID</option>

  <?php 
    $query = "select * from forex_booking_master where 1 ";
    include "branchwise_filteration.php";
    $query .= " and financial_year_id = '".$_SESSION['financial_year_id']."' and order by booking_id desc";
    $sq_booking = mysqlQuery($query);
  while($row_booking = mysqli_fetch_assoc($sq_booking)){

    $date = $row_booking['created_at'];
                      $yr = explode("-", $date);
                      $year =$yr[0];

    $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));

    ?>

    <option value="<?= $row_booking['booking_id'] ?>"><?= get_forex_booking_id($row_booking['booking_id'],$year) ?> : <?= $sq_customer['first_name'].' '.$sq_customer['last_name'] ?></option>

    <?php

  }
}
//Excursion Booking Dropdown
function get_excursion_booking_dropdown($role, $branch_admin_id, $branch_status,$emp_id,$role_id)

{ ?>

    <option value="">Booking ID</option>
    <?php 
      $query = "select * from excursion_master where 1 and delete_status='0'";
      include "branchwise_filteration.php";
      $query .= " and financial_year_id = '".$_SESSION['financial_year_id']."'  order by exc_id desc";
      $sq_booking = mysqlQuery($query);

      while($row_booking = mysqli_fetch_assoc($sq_booking))

      {

          $date = $row_booking['created_at'];
                      $yr = explode("-", $date);
                      $year =$yr[0];
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select first_name, middle_name, last_name from customer_master where customer_id='$row_booking[customer_id]'"));

           ?>

           <option value="<?php echo $row_booking['exc_id'] ?>"><?php echo get_exc_booking_id($row_booking['exc_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name']; ?></option>

           <?php

      }    

}

 
//Tour Cities dropdown

if(isset($_POST['city_master_dropdown'])){

  get_cities_dropdown(); 

}
function get_cities_dropdown($flag = '')
{ 
  if($flag==''){ ?>
  <option value="">City Name</option>
  <?php
  }
  $sq_city = mysqlQuery("select * from city_master where active_flag!='Inactive' order by REPLACE(city_name, ' ', '') asc");
  while($row_city = mysqli_fetch_assoc($sq_city)){
    ?>
    <option value="<?php echo $row_city['city_id'] ?>"><?php echo $row_city['city_name'] ?></option>
    <?php
  }
}

function get_locations_dropdown(){
          
    echo"<option value=''>Select Location</option>";
    $sq_location = mysqlQuery("select * from locations where active_flag='Active'");
    while($row_location = mysqli_fetch_assoc($sq_location))
    {
      echo "<option value=".$row_location['location_id'] .">". $row_location['location_name']."</option>";
    }  
}
//State name dropdown
function get_states_dropdown()

{

  ?>

  <option value="">*Select State/Country Name</option>

  <?php

  $sq_state = mysqlQuery("select * from state_master where active_flag='Active'");

  while($row_state = mysqli_fetch_assoc($sq_state))

  {

   ?>

   <option value="<?php echo $row_state['id'] ?>"><?php echo $row_state['state_name'] ?></option>

   <?php

  } 



}



//Bank name dropdown

function get_bank_dropdown($label='Creditor Bank')

{

  ?>
  <option value=""><?= $label ?></option>

  <?php

  $sq_bank = mysqlQuery("select * from bank_master where active_flag='Active' order by bank_name asc");

  while($row_bank = mysqli_fetch_assoc($sq_bank)){

    ?>

    <option value="<?= $row_bank['bank_id'] ?>"><?= $row_bank['bank_name'].' : '.$row_bank['branch_name'] ?></option>

    <?php

  }

}   

//new customer dropdown
function get_new_customer_dropdown($role,$branch_admin_id,$branch_status)
{
  ?>
    <option value="">*Select Customer</option>
    <option value="0">New Customer</option>
    <?php
    if($branch_status=='yes' && $role!='Admin'){
      $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query))
      { 
      	if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){ ?>
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
        <?php }
        else{ ?>        
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['first_name'].' '.$row_cust['last_name']; ?></option>      
          <?php 
        }
      }
  }
  else{ ?>
    <?php
    $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query))
      {
      	if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){ ?>
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
          <?php
        }
        else{ ?>
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['first_name'].' '.$row_cust['last_name']; ?></option>      
          <?php 
        }
      }
  }
}


//Customer dropdown
function get_customer_dropdown($role,$branch_admin_id,$branch_status)
{
  ?>
  <option value="">Select Customer</option>
  <?php 
  if($branch_status=='yes' && $role!='Admin'){
      $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query))
      { 
      	if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){ ?>
         <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
         <?php }
        else{ ?> 
         <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['first_name'].' '.$row_cust['last_name']; ?></option>      
        <?php 
      	}
      }
  }
  else{ ?>    
   <?php   $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query))
      {
      	if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){ ?>
         <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
         <?php }
        else{ ?>
         <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['first_name'].' '.$row_cust['last_name']; ?></option>      
      	<?php 
        }
      }
  }

}
function get_destinations(){
  
  $final_array = array();
  $sq_query = mysqlQuery("select dest_id,dest_name from destination_master where status='Active' order by dest_name desc");
  while($row_query = mysqli_fetch_assoc($sq_query))
  {
    $to_be_push = [
      "dest_id" => $row_query['dest_id'],
      "label" => $row_query['dest_name']
    ];
    array_push($final_array,$to_be_push);
  }
  echo json_encode($final_array);
}
function get_destinations_option($selected){
  
  $final_array = array();
  $sq_query = mysqlQuery("select dest_id,dest_name from destination_master where status='Active' order by dest_name desc");
  ?>
  <option value="">Select Destination</option>
  <?php 
    
  while($row_query = mysqli_fetch_assoc($sq_query))
  {
    ?>
    <option value="<?= $row_query['dest_id'] ?>" <?= $selected==$row_query['dest_id'] ? "selected" : ""  ?>><?= $row_query['dest_name'] ?></option>
  <?php
    
  }
  
}

function get_customer_hint($branch_status='no'){
  $final_array = array();
  global $encrypt_decrypt, $secret_key;
  $role = $_SESSION['role'];
  $branch_admin_id = $_SESSION['branch_admin_id'];

  if($branch_status=='yes' && $role!='Admin'){
    $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
    
    while($row_cust_new = mysqli_fetch_assoc($sq_query))
    { 
      $row_cust = $row_cust_new;
      $contact_no = $encrypt_decrypt->fnDecrypt($row_cust['contact_no'], $secret_key);
      $contact_no = str_replace($row_cust['country_code'],"",$contact_no);      
      $sq_code = mysqli_fetch_assoc(mysqlQuery("SELECT country_code FROM `country_list_master` where phone_code ='$row_cust[country_code]'"));

      $email_id = $encrypt_decrypt->fnDecrypt($row_cust['email_id'], $secret_key);
      if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){

        $sq_user = mysqlQuery("SELECT user_id,name FROM `customer_users` where customer_id ='$row_cust[customer_id]'");
        $user_arr = array();
        while($row_user = mysqli_fetch_assoc($sq_user))
        { 
          array_push($user_arr, ['user_id'=>$row_user['user_id'],'name'=>$row_user['name']]);
        }
        $to_be_push = [
          "customer_id"=>$row_cust['customer_id'],
          "value" => $row_cust['company_name'],
          "label" => $row_cust['company_name'],
          "contact_no" => $contact_no,
          "email_id" => $email_id,
          "country_code" => $sq_code['country_code'].' ('.$row_cust['country_code'].')',
          "country_id"=>$row_cust['country_code'],
          'type'=>$row_cust['type'],
          "user_arr"=>$user_arr
        ];
      }
      else{
        $to_be_push = [
          "customer_id"=>$row_cust['customer_id'],
          "value" =>$row_cust['first_name'].' '.$row_cust['last_name'],
          "label" =>$row_cust['first_name'].' '.$row_cust['last_name'],
          "contact_no" => $contact_no,
          "email_id" => $email_id,
          "country_code" => $sq_code['country_code'].' ('.$row_cust['country_code'].')',
          "country_id"=>$row_cust['country_code'],
          'type'=>$row_cust['type']
        ];
      }
      array_push($final_array, $to_be_push);
    }
}
else{
   $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
    while($row_cust = mysqli_fetch_assoc($sq_query))
    {
      $sq_user = mysqlQuery("SELECT user_id,name FROM `customer_users` where customer_id ='$row_cust[customer_id]'");
      $user_arr = array();
      while($row_user = mysqli_fetch_assoc($sq_user))
      { 
        array_push($user_arr, ['user_id'=>$row_user['user_id'],'name'=>$row_user['name']]);
      }
      $contact_no = $encrypt_decrypt->fnDecrypt($row_cust['contact_no'], $secret_key);
      $contact_no = str_replace($row_cust['country_code'],"",$contact_no);      
      $sq_code = mysqli_fetch_assoc(mysqlQuery("SELECT country_code FROM `country_list_master` where phone_code ='$row_cust[country_code]'"));
      $email_id = $encrypt_decrypt->fnDecrypt($row_cust['email_id'], $secret_key);
      $country_code = isset($sq_code['country_code']) ? $sq_code['country_code'].' ('.$row_cust['country_code'].')' : '';
      if($row_cust['type']=='Corporate'||$row_cust['type']=='B2B'){
        $to_be_push = array(
          "customer_id"=>$row_cust['customer_id'],
          "value" => $row_cust['company_name'],
          "label" => $row_cust['company_name'],
          "contact_no" => $contact_no,
          "email_id" => $email_id,
          "country_code" => $country_code,
          'type'=>$row_cust['type'],
          "country_id"=>$row_cust['country_code'],
          "user_arr"=>$user_arr
        );
      }
      else{
        $to_be_push = array(
          "customer_id"=>$row_cust['customer_id'],
          "value" =>$row_cust['first_name'].' '.$row_cust['last_name'],
          "label" =>$row_cust['first_name'].' '.$row_cust['last_name'],
          "contact_no" => $contact_no,
          "email_id" => $email_id,
          "country_code" => $country_code,
          "country_id"=>$row_cust['country_code'],
          'type'=>$row_cust['type']
        );
      }
      array_push($final_array, $to_be_push);
    }
}
echo json_encode($final_array);
}

//Get Corporate customer dropdown
function get_corpo_customer_dropdown($role,$branch_admin_id,$branch_status){
  ?>
  <option value="">Select Customer</option>
  <?php 

  if($branch_status=='yes' && $role!='Admin'){
      $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and type='Corporate' or type='B2B' and branch_admin_id='$branch_admin_id' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query))
      { ?>
          
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
      <?php 
      }
  }
  else{ ?>
   <?php   $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and type='Corporate' or type='B2B' order by customer_id desc");
      while($row_cust = mysqli_fetch_assoc($sq_query)){ ?>
          <option value="<?php  echo $row_cust['customer_id']; ?>"><?php  echo $row_cust['company_name']; ?></option>      
        <?php 
      }
  }
}

//Financial year dropdown
function get_financial_year_dropdown($all=true){

  if($all){
    ?>
    <option value="">All</option>
    <?php
  }
  $sq_finacial_year = mysqlQuery("select * from financial_year where active_flag!='Inactive' order by financial_year_id desc");

  while($row_financial_year = mysqli_fetch_assoc($sq_finacial_year)){
    $financial_year = get_date_user($row_financial_year['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($row_financial_year['to_date']);
    ?>
    <option value="<?= $row_financial_year['financial_year_id'] ?>"><?= $financial_year  ?></option>
    <?php
  }
}
//Financial year dropdown
function get_financial_year_dropdown_filter($financial_year_id){

  $sq_finacial_year = mysqlQuery("select * from financial_year where active_flag!='Inactive' and financial_year_id!='$financial_year_id' order by financial_year_id desc");
  while($row_financial_year = mysqli_fetch_assoc($sq_finacial_year)){
  $financial_year = get_date_user($row_financial_year['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($row_financial_year['to_date']);
  ?>
  <option value="<?= $row_financial_year['financial_year_id'] ?>"><?= $financial_year  ?></option>
  <?php
  }
}

//Airport Name dropdown
function get_airport_name_dropdown(){

  $sq_airport = mysqlQuery("select airport_name, airport_code from airport_master where flag!='Inactive' order by airport_name asc");
  while($row_airport = mysqli_fetch_assoc($sq_airport)){
      $row_airport_nam = clean($row_airport['airport_name']);
      $row_airport_code = clean($row_airport['airport_code']);

    ?>
    <option value="<?= $row_airport_nam ?>"><?php echo $row_airport_nam." (".$row_airport_code.")" ?></option>
    <?php
  }
}

//Airport Name/IDs dropdown
function get_airport_dropdown(){
  $sq_airport = mysqlQuery("select airport_name, airport_code, airport_id from airport_master where flag!='Inactive' order by airport_name");
  while($row_airport = mysqli_fetch_assoc($sq_airport)){
      $row_airport_nam = clean($row_airport['airport_name']);
      $row_airport_code = clean($row_airport['airport_code']);
  ?>
  <option value="<?= $row_airport['airport_id'] ?>"><?php echo $row_airport_nam." (".$row_airport_code.")" ?></option>
  <?php
  }
}
//Hotel dropdown
function get_hotel_dropdown(){
  $sq_hotel = mysqlQuery("select hotel_id,hotel_name,city_id from hotel_master where active_flag!='Inactive' order by hotel_name");
  while($row_hotel = mysqli_fetch_assoc($sq_hotel)){
      $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$row_hotel[city_id]'"));
      $hotel_name = clean($row_hotel['hotel_name']);
  ?>
  <option value="<?= $row_hotel['hotel_id'] ?>"><?php echo $hotel_name." (".$sq_city['city_name'].")" ?></option>
  <?php
  }
}

//Airline Name dropdown
function get_airline_name_dropdown(){

  $sq_airline = mysqlQuery("select * from airline_master where active_flag!='Inactive' order by airline_name asc");
  while($row_airline = mysqli_fetch_assoc($sq_airline)){
    ?>
    <option value="<?= $row_airline['airline_id'] ?>"><?= $row_airline['airline_name'].' ('.$row_airline['airline_code'].')' ?></option>
    <?php
  }

}
//Enquiry Airline Name dropdown
function get_enqairline_dropdown(){

  $sq_airline = mysqlQuery("select * from airline_master where active_flag!='Inactive' order by airline_name asc");
  while($row_airline = mysqli_fetch_assoc($sq_airline)){
    ?>
    <option value="<?= $row_airline['airline_name'].' ('.$row_airline['airline_code'].')' ?>"><?= $row_airline['airline_name'].' ('.$row_airline['airline_code'].')' ?></option>
    <?php
  }
}

//Taxation dropdown
function get_taxation_dropdown()
{
    ?>
    <option value="0">Tax(%)</option>
    <?php

    $sq_taxation = mysqlQuery("select * from taxation_master where active_flag='Active'");

    while($row_taxation = mysqli_fetch_assoc($sq_taxation)){



        $sq_tax_type = mysqli_fetch_assoc(mysqlQuery("select * from tax_type_master where tax_type_id='$row_taxation[tax_type_id]'"));

        ?>

        <option value="<?= $row_taxation['taxation_id'] ?>"><?= $sq_tax_type['tax_type'].'-'.$row_taxation['tax_in_percentage'] ?></option>

        <?php

    }

}



//Get Relation Dropdown

function get_relation_dropdown()
{

  ?>

  <option value="">Relation</option>

  <option value="Relative">Relative</option>

  <option value="Father">Father</option>

  <option value="Mother">Mother</option>

  <option value="Brother">Brother</option>

  <option value="Sister">Sister</option>

  <option value="Friend">Friend</option>

  <option value="Wife">Wife</option>
  <option value="Husband">Husband</option>

  <option value="Son">Son</option>

  <option value="Daughter">Daughter</option>

  <?php

}



//Get Payment Mode Dropdown
function get_payment_mode_dropdown(){
  ?>
  <option value="">*Mode</option>
  <option value="Cash"> Cash </option>
  <option value="Cheque"> Cheque </option>
  <option value="Credit Card">Credit Card</option>
  <option value="NEFT"> NEFT </option>
  <option value="RTGS"> RTGS </option>
  <option value="IMPS"> IMPS </option>
  <option value="DD"> DD </option>
  <option value="Online"> Online </option>
  <option value="Credit Note"> Credit Note </option>
  <option value="Advance"> Advance </option>
  <option value="Other"> Other </option>
  <?php }

//Get Honorifics
function get_hnorifi_dropdown()
{

  ?>

  <option value="Mr."> Mr. </option>
  <option value="Mrs"> Mrs </option>
  <option value="Miss"> Miss </option>
  <option value="Smt"> Smt </option>
  <option value="Infant"> Infant </option>

  <?php

}

//Get Train Class dropdown
function get_train_class_dropdown()
{?>

  <option value="1A">1A</option>

  <option value="2A">2A</option>

  <option value="3A">3A</option>

  <option value="FC">FC</option>

  <option value="CC">CC</option>

  <option value="SL">SL</option>

  <option value="2S">2S</option>

<?php

}
//Get Train Class dropdown
function get_flight_class_dropdown()
{ ?>

<option value="">*Class</option>
<option value="First Class">First Class</option>
<option value="Economy">Economy</option>
<option value="Premium Economy">Premium Economy</option>
<option value="Business">Business</option>
<option value="Other">Other</option>

<?php
} 

//Get app settings Tax Name

function get_tax_name()

{

  $sq_tax_name = mysqli_fetch_assoc(mysqlQuery("select tax_name from app_settings"));

  return $sq_tax_name['tax_name'];

}

function get_mealplan_dropdown(){
  ?>
  <option value="">Meal Plan</option>
  <?php
  $sq_meal = mysqlQuery("select * from meal_plan_master where 1");
  while($row_meal = mysqli_fetch_assoc($sq_meal)){
    ?>
    <option value="<?= $row_meal['meal_plan'] ?>"><?=  $row_meal['meal_plan'] ?></option>
    <?php
  }
}
function get_customer_type_dropdown(){
  ?>
  <option value="">*Customer Type</option>
  <option value="Regular">Regular</option>
  <option value="Walkin">Walkin</option>
  <option value="Corporate">Corporate</option>
  <option value="B2B">B2B</option>
<?php }

function get_customer_source_dropdown(){
  $sq_rc = mysqlQuery("select * from references_master where active_flag='Active'");
  ?>
  <option value="">Customer Source</option>
  <?php
  while($row_rc = mysqli_fetch_assoc($sq_rc)){
    ?>
    <option value="<?= $row_rc['reference_name'] ?>"><?=  $row_rc['reference_name'] ?></option>
    <?php } 
}

function get_room_category_dropdown(){
  $sq_rc = mysqlQuery("select * from room_category_master where active_status='Active'");
  ?><option value="">Room Category</option>
  <?php
  while($row_rc = mysqli_fetch_assoc($sq_rc)){
    ?>
    <option value="<?= $row_rc['room_category'] ?>"><?=  $row_rc['room_category'] ?></option>
<?php } }

function get_hotel_type_dropdown(){
?>
<option value="">Hotel Type</option>
  <?php
  $sq_rc = mysqlQuery("select * from hotel_type_master");
  while($row_rc = mysqli_fetch_assoc($sq_rc)){ ?>
    <option value="<?= $row_rc['type'] ?>"><?=  $row_rc['type'] ?></option>
<?php } }

function get_vehicle_types(){
?>
  <option value="">*Vehicle Type</option>
  <?php
  $sq_rc = mysqlQuery("select * from vehicle_type_master");
  while($row_rc = mysqli_fetch_assoc($sq_rc)){ ?>
    <option value="<?= $row_rc['type'] ?>"><?=  $row_rc['type'] ?></option>
<?php }
}
function get_all_suppliers(){
?>
  <option value="">Select Supplier</option>
  
  <optgroup value='hotel' label="Hotel">
  <?php //Hotel
  $sq_supp = mysqlQuery("select city_id,hotel_name from hotel_master");
  while($row_supp= mysqli_fetch_assoc($sq_supp)){
    $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));
    ?>
    <option><?=  $row_supp['hotel_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='transport' label="Transport">
  <?php  //Transport
    $sq_supp = mysqlQuery("select city_id,transport_agency_name from transport_agency_master");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));
      ?>
      <option><?=  $row_supp['transport_agency_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='dmc' label="DMC">
  <?php  //DMC
    $sq_supp = mysqlQuery("select city_id,company_name from dmc_master");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['company_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='car' label="Car Rental">
  <?php  //Car Rental
    $sq_supp = mysqlQuery("select city_id,vendor_name from car_rental_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['vendor_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='visa' label="Visa">
  <?php  //Visa
    $sq_supp = mysqlQuery("select city_id,vendor_name from visa_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['vendor_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='flight' label="Flight">
  <?php  //Visa
    $sq_supp = mysqlQuery("select vendor_id,vendor_name from ticket_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ ?>
      <option><?=  $row_supp['vendor_name'] ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='activities' label="Activities">
  <?php  //Acticity
    $sq_supp = mysqlQuery("select city_id,vendor_name from site_seeing_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['vendor_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='cruise' label="Cruise">
  <?php  //Cruise
    $sq_supp = mysqlQuery("select city_id,company_name from cruise_master");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['company_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='train' label="Train">
  <?php  //Train
    $sq_supp = mysqlQuery("select vendor_id,vendor_name from train_ticket_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ ?>
      <option><?=  $row_supp['vendor_name'] ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='passport' label="Passport">
  <?php  //Passport
    $sq_supp = mysqlQuery("select vendor_id,vendor_name from passport_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ ?>
      <option><?=  $row_supp['vendor_name'] ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='insurance' label="Insurance">
  <?php  //Insurance
    $sq_supp = mysqlQuery("select vendor_id,vendor_name from insuarance_vendor");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ ?>
      <option value="<?= $row_supp['vendor_name'] ?>"><?=  $row_supp['vendor_name'] ?></option>
  <?php } ?>
  </optgroup>
  <optgroup value='other' label="Other">
  <?php  //Other
    $sq_supp = mysqlQuery("select city_id,vendor_name from other_vendors");
    while($row_supp= mysqli_fetch_assoc($sq_supp)){ 
      $city_name = mysqli_fetch_assoc(mysqlQuery("SELECT city_name from city_master where city_id = ".$row_supp['city_id']));  
    ?>
      <option><?=  $row_supp['vendor_name'].' ('.$city_name['city_name'].')' ?></option>
  <?php } ?>
  </optgroup>

<?php }

function get_tax_conditions(){
  $sq_cond = mysqlQuery("select * from tax_conditions where id in('1','2','3','4','5','6','7','8','9')");?>
  
  <option value="">Condition</option>
  <?php while($row_cond= mysqli_fetch_assoc($sq_cond)){ ?>
    <option value="<?= $row_cond['id'] ?>"><?= $row_cond['name'] ?></option>
  <?php }
}

function get_other_charges(){
  $sq_cond = mysqlQuery("select * from other_charges_master where 1");?>
  
  <?php while($row_cond= mysqli_fetch_assoc($sq_cond)){ ?>
    <option value="<?= $row_cond['entry_id'] ?>"><?= $row_cond['name'] ?></option>
  <?php } ?>
  <option value="">*Rule For</option>
<?php }

function get_other_charges_conditions(){
  
  $sq_cond = mysqlQuery("select * from tax_conditions where id in('2','11','5','8','12','13','14','15','10','3','7','6','16')");?>
  
  <option value="">Condition</option>
  <?php while($row_cond= mysqli_fetch_assoc($sq_cond)){ ?>
    <option value="<?= $row_cond['id'] ?>"><?= $row_cond['name'] ?></option>
  <?php }
}

function get_country_code(){
  ?>
  <option value="">Country Code</option>
<?php
  $sq_code = mysqlQuery("SELECT * FROM `country_list_master`");
  while($row = mysqli_fetch_assoc($sq_code)){
?>
  <option value="<?= $row['phone_code'] ?>"><?= $row['country_code'].' ('.$row['phone_code'].')' ?></option>
<?php
  }
}
function get_package_type_dropdown(){
  ?>
  <!-- <option value="">*Select Package Type</option> -->
  <option value="ECONOMY">ECONOMY</option>
  <option value="LUXURY">LUXURY</option>
  <option value="PREMIUM">PREMIUM</option>
  <option value="ROYAL PACKAGE">ROYAL PACKAGE</option>
  <option value="STANDARD">STANDARD</option>
<?php
}
function get_hotel_category_dropdown(){
  echo '
  <option value="">*Select Hotel Type</option>
  <option value="Economy">Economy</option>
  <option value="Standard">Standard</option>
  <option value="Luxury">Luxury</option>';
}
function get_ferry_types(){
  echo '
  <option value="">*Select Ferry/Cruise Class</option>
  <option value="Business Class">Business Class</option>
  <option value="Luxury Class">Luxury Class</option>
  <option value="Royal Class">Royal Class</option>
  <option value="Economy Class">Economy Class</option>
  <option value="Premium Class">Premium Class</option>
  <option value="Premium Plus Class">Premium Plus Class</option>
  <option value="Deluxe Class">Deluxe Class</option>';
}
function get_bike_types(){
  ?>
  <option value="">*Select Bike Type</option>
<?php
  $sq_bike = mysqlQuery("SELECT * FROM `bike_type_master` where active_flag='Active'");
  while($row = mysqli_fetch_assoc($sq_bike)){
?>
  <option value="<?= $row['entry_id'] ?>"><?= $row['bike_type'] ?></option>
  <?php
  }
}
function get_tax_dropdown($reflection){
  ?>
<?php
  $sq_tax = mysqlQuery("SELECT * FROM `tax_master` where status='Active' and reflection='$reflection'");
  while($row_tax = mysqli_fetch_assoc($sq_tax)){

    $tax_string = $row_tax['name1'].':('.$row_tax['amount1'].'%):('.$row_tax['ledger1'].')';
    $tax_string .= ($row_tax['name2'] != '') ? '+'.$row_tax['name2'].':('.$row_tax['amount2'].'%):('.$row_tax['ledger2'].')' : '';
?>
  <option value="<?= $tax_string ?>"><?= $tax_string ?></option>
  <?php
  }
}
function get_service_duration_dropdown(){

  $sq_booking = mysqlQuery("select entry_id,duration from service_duration_master where status='Active'");
  while($row_booking = mysqli_fetch_assoc($sq_booking)){
    ?>
    <option value="<?php echo $row_booking['entry_id'] ?>"><?php echo $row_booking['duration']; ?></option>
    <?php
  }
}
?>
