<?php
//Generic Files
include "../../../../model.php"; 
include "printFunction.php";

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/group_tour/index.php'"));
$branch_status = $sq['branch_status'];
if ($branch_admin_id != 0) {
  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
} else {
  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='1'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
}

$quotation_id = $_GET['quotation_id'];
global $app_quot_img,$currency,$app_quot_format;

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Group Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_quotation_master where quotation_id='$quotation_id'"));
$sq_package_program = mysqlQuery("select * from group_tour_program where tour_id ='$sq_quotation[tour_group_id]'");
$sq_tour = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$sq_quotation[tour_group_id]'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select link from video_itinerary_master where dest_id = '$sq_tour[dest_id]'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year =$yr[0];

if($sq_emp_info['first_name']==''){
  $emp_name = 'Admin';
}
else{
  $emp_name = $sq_emp_info['first_name'].' '.$sq_emp_info['last_name'];
}
$tour_cost = $sq_quotation['tour_cost'];
////////////////Currency conversion ////////////
$currency_amount1 = currency_conversion($currency,$sq_quotation['currency_code'],$sq_quotation['quotation_cost']);
?>

<!-- landingPage -->
<section class="landingSec main_block">
  <div class="landingPageTop main_block">
    <img src="<?= getFormatImg($app_quot_format, $sq_tour['dest_id'])?>" class="img-responsive">
    <h1 class="landingpageTitle"><?= $sq_quotation['tour_name'] ?></h1>
    <span class="landingPageId"><?= get_quotation_id($quotation_id,$year) ?><br><?= get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']) ?></span>
  </div>

  <div class="ladingPageBottom main_block side_pad">

    <div class="row">
      <div class="col-md-4">
        <div class="landigPageCustomer">
          <h3 class="customerFrom">PREPARED FOR</h3>
          <span class="customerName"><i class="fa fa-user"></i> : <?= $sq_quotation['customer_name'] ?></span><br>
          <span class="customerMail"><i class="fa fa-envelope"></i> : <?= $sq_quotation['email_id'] ?></span><br>
          <?php if($sq_quotation['mobile_number'] != ''){ ?>
            <span class="customerMobile"><i class="fa fa-phone"></i> : <?= $sq_quotation['mobile_number'] ?></span> <?php } ?>
          <span class="generatorName">PREPARED BY <?= $emp_name?></span><br>
        </div>
      </div>
      <div class="col-md-8 text-right">
      
      <div class="detailBlock text-center">
        <div class="detailBlockIcon detailBlockBlue">
          <i class="fa fa-calendar"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= get_date_user($sq_quotation['quotation_date']) ?></h3>
          <span class="contentLabel">QUOTATION DATE</span>
        </div>
      </div>

      <div class="detailBlock text-center">
        <div class="detailBlockIcon detailBlockGreen">
          <i class="fa fa-hourglass-half"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?php echo ($sq_quotation['total_days']) . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' ?></h3>
          <span class="contentLabel">DURATION</span>
        </div>
      </div>

      <div class="detailBlock text-center">
        <div class="detailBlockIcon detailBlockYellow">
          <i class="fa fa-users"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= $sq_quotation['total_passangers'] ?></h3>
          <span class="contentLabel">TOTAL GUEST(s)</span>
        </div>
      </div>

      </div>
    </div>

  </div>
</section>

<!-- Itinerary -->
<section class="itinerarySec main_block side_pad mg_tp_30">
  <div class="vitinerary_div">
    <h6>Destination Guide Video</h6>
    <img src="<?php echo BASE_URL.'images/quotation/youtube-icon.png'; ?>" class="itinerary-img img-responsive"><br/>
    <a href="<?=$sq_dest['link']?>" class="no-marg" target="_blank"></a>
  </div>
  <ul class="print_itinenary main_block no-pad no-marg">
    <?php 
    $count = 1;
    $i = 0;
    $dates = (array) get_dates_for_tour_itineary($quotation_id); 
    while($row_itinarary = mysqli_fetch_assoc($sq_package_program)){
      
      $date_format = isset($dates[$i]) ? $dates[$i] : 'NA';
      if($row_itinarary['daywise_images'] != ""){
        $img = $row_itinarary['daywise_images'];
        $pos = strstr($img,'uploads');
        if ($pos != false){
            $newUrl1 = preg_replace('/(\/+)/','/',$img); 
            $img = BASE_URL.str_replace('../', '', $newUrl1);
        }    
      } 
      else 
        $img = "http://itourscloud.com/destination_gallery/asia/singapore/Asia_Singapore_Four.jpg";
    ?>
    <li class="singleItinenrary leftItinerary col-md-12 no-pad">
      <div class="itneraryContent col-md-12 no-pad text-right mg_tp_20 mg_bt_20">
        <div class="itneraryImg col-md-4 no-pad">
          <img src="<?= $img ?>" class="img-responsive" style="display: block;">
          <div style="display: flex; margin-top: 20px; align-items: center;">
            <div class="dayCount" style="position: static; ">
              <span>Day-<?= $count ?> <b>(<?php echo $date_format ?>) </b></span>
            </div>
            <div class="itineraryDetail" style="position: static; margin-right:auto;">
              <ul class="no-marg no-pad" style="margin-top: 15px; display: flex;">
                <li><span><i class="fa fa-bed"></i> : </span><?=  $row_itinarary['stay'] ?></li>
                <li><span><i class="fa fa-cutlery"></i> : </span><?= $row_itinarary['meal_plan'] ?></li>
              </ul>
            </div>
            
          </div>
        </div>
        <div class="itneraryText col-md-8 no-pad" style="padding-left: 20px;">
          <h5 style="padding-left: 50px;" class="specialAttraction no-marg text-left"><?= $row_itinarary['attraction'] ?></h5>
          <p style="margin-left: 50px;" class="text-left"><?= $row_itinarary['day_wise_program'] ?></p>
        </div>
      </div>
      
    </li>

    <?php
  $count++; $i++;} ?>
  </ul>

</section>

<!-- traveling Information -->
<section class="travelingDetails main_block mg_tp_30">
      
      <?php
      $sq_train_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_train_entries where quotation_id='$quotation_id'"));
      if($sq_train_count>0){ ?>
      <!-- train -->
      <section class="transportDetails main_block side_pad mg_tp_30">
        <div class="row">
          <div class="col-md-4">
            <div class="transportImg">
              <img src="<?= BASE_URL ?>images/quotation/train.png" class="img-responsive">
            </div>
          </div>
          <div class="col-md-8">
            <div class="table-responsive mg_tp_30">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>From_LOCATION</th>
                    <th>To_LOCATION</th>
                    <th>Class</th>
                    <th>Departure_D/T</th>
                    <th>Arrival_D/T</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  $sq_train = mysqlQuery("select * from group_tour_quotation_train_entries where quotation_id='$quotation_id'");
                  while($row_train = mysqli_fetch_assoc($sq_train)){  
                    ?>
                    <tr>
                      <td><?= $row_train['from_location'] ?></td>
                      <td><?= $row_train['to_location'] ?></td>
                      <td><?= $row_train['class'] ?></td>
                      <td><?= get_datetime_user($row_train['departure_date']) ?></td>
                      <td><?= get_datetime_user($row_train['arrival_date']) ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>  
        </div>
      </section>
      <?php } ?>
      <!-- flight -->
      <?php 
      $sq_plane_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'"));
      if($sq_plane_count>0){ 
      ?>
      <section class="transportDetails main_block side_pad mg_tp_30">
        <div class="row">
          <div class="col-md-4">
            <div class="transportImg">
              <img src="<?= BASE_URL ?>images/quotation/flight.png" class="img-responsive">
            </div>
          </div>
          <div class="col-md-8">
            <div class="table-responsive mg_tp_30">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>From_SECTOR</th>
                    <th>To_SECTOR</th>
                    <th>Airline</th>
                    <th>Departure_D/T</th>
                    <th>Arrival_D/T</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  $sq_plane = mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'");
                    while($row_plane = mysqli_fetch_assoc($sq_plane)){
                    $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
                  ?>   
                  <tr>
                    <td><?= $row_plane['from_location'] ?></td>
                    <td><?= $row_plane['to_location'] ?></td>
                    <td><?= $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')' ?></td>
                    <td><?= get_datetime_user($row_plane['dapart_time']) ?></td>
                    <td><?= get_datetime_user($row_plane['arraval_time']) ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>  
        </div>
      </section>
      <?php } ?>
      <!-- hotel -->
      <?php 
      $sq_h_count = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'"));
      if($sq_h_count != '0'){
      ?>
      <section class="transportDetails main_block side_pad mg_tp_30">
        <div class="row">
          <div class="col-md-8">
            <div class="table-responsive mg_tp_30">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>City Name</th>
                    <th>Hotel Name</th>
                    <th>Hotel Category</th>
                    <th>Total Nights</th>
                  </tr>
                </thead>
                  <?php
                  $count = 0;
                  $sq_hotel = mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'");
                  while($row_hotel = mysqli_fetch_assoc($sq_hotel))
                  {
                    ?>
                    <tr>
                      <td><?php
                      $city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = ".$row_hotel['city_id']));
                      echo $city['city_name'] ?></td>
                      <td><?php
                      $hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id = ".$row_hotel['hotel_id']));
                      echo $hotel['hotel_name'] ?></td>
                      <td><?= $row_hotel['hotel_type'] ?></td>
                      <td><?= $row_hotel['total_nights'] ?></td>
                    </tr>
                    <?php
                  }
                  ?>
              </table>
            </div>
          </div>  
          <div class="col-md-4">
            <div class="transportImg">
              <img src="<?= BASE_URL ?>images/quotation/hotel.png" class="img-responsive">
            </div>
          </div>
        </div>
      </section>
      <?php } ?>
      <?php
      $sq_cr_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_cruise_entries where quotation_id='$quotation_id'"));
      if($sq_cr_count>0){ ?>
      <!-- cruise -->
      <section class="transportDetails main_block side_pad mg_tp_30">
        <div class="row">
          <div class="col-md-8">
            <div class="table-responsive mg_tp_30">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>Departure_D/T</th>
                    <th>Arrival_D/T</th>
                    <th>Route</th>
                    <th>Cabin</th>
                    <th>Sharing</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  $sq_cruise = mysqlQuery("select * from group_tour_quotation_cruise_entries where quotation_id='$quotation_id'");
                  while($row_cruise = mysqli_fetch_assoc($sq_cruise)){  
                    ?>
                    <tr>
                      <td><?= get_datetime_user($row_cruise['dept_datetime']) ?></td>
                      <td><?= get_datetime_user($row_cruise['arrival_datetime']) ?></td>
                      <td><?= $row_cruise['route'] ?></td>
                      <td><?= $row_cruise['cabin'] ?></td>
                      <td><?= $row_cruise['sharing'] ?></td>
                    </tr>
                  <?php } ?>  
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-4">
            <div class="transportImg">
              <img src="<?= BASE_URL ?>images/quotation/cruise.png" class="img-responsive">
            </div>
          </div>  
        </div>
      </section>
      <?php } ?>
</section>

<?php
if($sq_quotation['incl'] != ''){ ?>

  <section class="incluExcluTerms main_block">
    <!-- Inclusion Exclusion -->
    <div class="incluExclu main_block">
        <div class="contenPanel main_block side_pad mg_tp_30">
            <?php if($sq_quotation['incl'] != ''){?>
            <div class="row">
              <div class="col-md-12">
                <h3 class="costBankTitle">INCLUSIONS</h3>
                <pre class="real_text"><?= $sq_quotation['incl'] ?></pre>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
    </div>

  </section>
<?php } ?>
<?php
if($sq_quotation['excl'] != ''){ ?>

  <section class="incluExcluTerms main_block">
    <!-- Inclusion Exclusion -->
    <div class="incluExclu main_block">
        <div class="contenPanel main_block side_pad mg_tp_30">

            <?php if($sq_quotation['excl'] != ''){?>
            <div class="row">
              <div class="col-md-12">
                <h3 class="costBankTitle">EXCLUSIONS</h3>
                <pre class="real_text"><?= $sq_quotation['excl'] ?></pre>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
    </div>

  </section>
<?php } ?>

<?php
if(isset($sq_terms_cond['terms_and_conditions'])){ ?>

  <section class="incluExcluTerms main_block">
    <!-- Inclusion Exclusion -->
      <div class="incluExclu main_block">
        <div class=" main_block side_pad mg_tp_30">
          <div class="row">
              <div class="col-md-12">
                <h3 class="costBankTitle">TERMS AND CONDITIONS</h3>
                <div class="tncContent">
                  <pre class="real_text"><?= $sq_terms_cond['terms_and_conditions'] ?></pre>
                </div>
              </div>
          </div>
        </div>
      </div>
  </section>
<?php } ?>

<!-- Ending Page -->
<section class="incluExcluTerms main_block mg_tp_20">
  
  <!-- Guest Detail -->
  <div class="guestDetail main_block text-center">
    <h3 class="costBankTitle">TOTAL GUEST</h3>
    <img src="<?= BASE_URL ?>images/quotation/guestCount.png" class="img-responsive">
    <span class="guestCount adultCount">Adult : <?= $sq_quotation['total_adult']+$sq_quotation['single_person'] ?></span>
    <span class="guestCount childCount">Child : <?= $sq_quotation['total_children'] ?></span>
    <span class="guestCount infantCount">Infant : <?= $sq_quotation['total_infant'] ?></span>
  </div>
  <?php
  $tour_cost1 = $sq_quotation['tour_cost'];
  $service_charge = $sq_quotation['service_charge'];
  $tour_cost= $tour_cost1 +$service_charge;
  $service_tax_amount = 0;
  $tax_show = '';
  $bsmValues = json_decode($sq_quotation['bsm_values']);
  $name = '';
  if($sq_quotation['service_tax_subtotal'] !== 0.00 && ($sq_quotation['service_tax_subtotal']) !== ''){
    $service_tax_subtotal1 = explode(',',$sq_quotation['service_tax_subtotal']);
    for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
      $service_tax = explode(':',$service_tax_subtotal1[$i]);
      $service_tax_amount +=  $service_tax[2];
      $name .= $service_tax[0]  . $service_tax[1] .', ';
    }
  }
  $service_tax_amount_show = currency_conversion($currency,$sq_quotation['currency_code'],$service_tax_amount);

  if($bsmValues[0]->service != ''){   //inclusive service charge
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  }
  else{
    // $tax_show = $service_tax_amount;
    $tax_show =  rtrim($name, ', ').' : ' . ($service_tax_amount);
    $newBasic = $tour_cost;
  }

  ////////////Basic Amount Rules
  if($bsmValues[0]->basic != ''){ //inclusive markup
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  }
  $newBasic1 = currency_conversion($currency,$sq_quotation['currency_code'],$newBasic);
  ?>
  <!-- Costing & Bank Detail -->
  <div class="costBankSec main_block mg_tp_20 costing_bank_details_bk">
    <div class="costBankInner main_block side_pad mg_tp_20 mg_bt_20">
      <div class="row">
        <!-- Costing -->
        <div class="col-md-6">
          <h3 class="costBankTitle text-center">COSTING DETAILS</h3>
          <div class="row mg_bt_20">
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/tourCost.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $newBasic1 ?></h4>
              <p>TOUR COST</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/tax.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= str_replace(',','',$name).'<br/>'.$service_tax_amount_show ?></h4>
              <p>TAX</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/quotationCost.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $currency_amount1 ?></h4>
              <p>QUOTATION COST</p>
            </div>
          </div>
        </div>
        <!-- Bank Detail -->
            <div class="col-md-6" style="border-left:1px solid #dddddd;">
              <h3 class="costBankTitle text-center">BANK DETAILS</h3>
              <div class="row mg_bt_20">
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/bankName.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></h4>
                    <p>BANK NAME</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?> </h4>
                    <p>BRANCH</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accName.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></h4>
                    <p>A/C TYPE</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
                    <p>A/C NO</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?=($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
                    <p>BANK ACCOUNT NAME</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                    <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
                    <p>SWIFT CODE</p>
                </div>
                <?php 
              if(check_qr()) { ?>
                <div class="col-md-12 text-center" style="margin-top:20px; margin-bottom:20px;">
                        <?= get_qr('Landscape Standard') ?>
                        <br>
                        <h4 class="no-marg">Scan & Pay </h4>
          </div> 
          <?php } ?>
              </div>
        </div>
      </div>
    </div>
  </div>

  <!-- contact-detail -->
  <section class="contactsec main_block">
    <div class="row">
      <div class="col-md-7">
        <div class="contactTitlePanel text-center">
          <!-- <h3>Contact Us</h3> -->
          <img src="<?= BASE_URL ?>images/quotation/contactImg.jpg" class="img-responsive">
          <?php if($app_website != ''){?><p class="no-marg"><?php echo $app_website; ?></p><?php } ?>
        </div>
      </div>
      <div class="col-md-5">  
        <?php //if($app_address != ''){?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-map-marker"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Corporate Office</h5>
            <p class="cBlockData"><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['address1'].','.$branch_details['address2'].','.$branch_details['city'] : $app_address; ?></p>
          </div>
        </div>      
        <?php //} ?>
        <?php //if($app_contact_no != ''){?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-phone"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Contact</h5>
            <p class="cBlockData"><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['contact_no']  : $app_contact_no; ?></p>
          </div>
        </div>
        <?php //} ?>
        <?php //if($app_email_id != ''){?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-envelope"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Email Id</h5>
            <p class="cBlockData"><?php echo ($branch_status=='yes' && $role!='Admin' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></p>
          </div>
        </div>
        <?php //} ?>

      </div>
    </div>
  </section>

</section>

</body>
</html>