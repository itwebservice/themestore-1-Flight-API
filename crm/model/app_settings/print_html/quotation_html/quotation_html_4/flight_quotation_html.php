<?php 
//Generic Files
include "../../../../model.php"; 
include "printFunction.php";
global $app_quot_img,$currency,$quot_note;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/home/index.php'"));
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

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Flight Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_master where quotation_id='$quotation_id'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));
$sq_plane = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'"));
$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year =$yr[0];
if($sq_emp_info['first_name']==''){
  $emp_name = 'Admin';
}
else{
  $emp_name = $sq_emp_info['first_name'].' '.$sq_emp_info['last_name'];
}


$tax_show = '';
$newBasic = $basic_cost1 = $sq_quotation['subtotal'] ;
$service_charge = $sq_quotation['service_charge'];
$bsmValues = json_decode($sq_quotation['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$percent = '';
if($sq_quotation['service_tax'] !== 0.00 && ($sq_quotation['service_tax']) !== ''){
  $service_tax_subtotal1 = explode(',',$sq_quotation['service_tax']);
  for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
    $service_tax = explode(':',$service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $percent .= $service_tax[0]  . $service_tax[1] .', ';
  }
}
////////////////////Markup Rules
$markupservice_tax_amount = 0;
if($sq_quotation['markup_cost_subtotal'] !== 0.00 && $sq_quotation['markup_cost_subtotal'] !== ""){
  $service_tax_markup1 = explode(',',$sq_quotation['markup_cost_subtotal']);
  for($i=0;$i<sizeof($service_tax_markup1);$i++){
    $service_tax = explode(':',$service_tax_markup1[$i]);
    $markupservice_tax_amount += $service_tax[2];
  }
}
$total_tax_amount_show = currency_conversion($currency,$currency,floatval($service_tax_amount) + floatval($markupservice_tax_amount) + $sq_quotation['roundoff']);

if(($bsmValues[0]->service != '' || $bsmValues[0]->basic != '')  && $bsmValues[0]->markup != ''){
  $tax_show = '';
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $markupservice_tax_amount + $service_charge + $service_tax_amount;
}
elseif(($bsmValues[0]->service == '' || $bsmValues[0]->basic == '')  && $bsmValues[0]->markup == ''){
  $tax_show = $percent.' '. ($total_tax_amount_show);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge;
}
elseif(($bsmValues[0]->service != '' || $bsmValues[0]->basic != '') && $bsmValues[0]->markup == ''){
  $tax_show = $percent.' '. ($markupservice_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $service_tax_amount;
}
else{
  $tax_show = $percent.' '. ($service_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $markupservice_tax_amount;
}
$quotation_cost = currency_conversion($currency,$currency,$sq_quotation['quotation_cost']);
?>

  <!-- landingPage -->
  <section class="landingSec main_block">

    <div class="landingPageTop main_block">
      <img src="<?= $app_quot_img ?>" class="img-responsive">
      <span class="landingPageId"><?= get_quotation_id($quotation_id,$year) ?> </span>
    </div>

    <div class="ladingPageBottom main_block side_pad">
      <div class="row">
        <div class="col-md-12 mg_tp_30">
            <h3 class="customerFrom">PREPARED FOR</h3>
        </div>
        <div class="col-md-4">
          <div class="landigPageCustomer">
            <span class="customerName mg_tp_10"><i class="fa fa-user"></i> : <?= $sq_quotation['customer_name'] ?></span><br>
            <span class="customerMail mg_tp_10"><i class="fa fa-envelope"></i> : <?= $sq_quotation['email_id'] ?></span><br>
            <span class="customerMobile mg_tp_10"><i class="fa fa-phone"></i> : <?= $sq_quotation['mobile_no'] ?></span><br>
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
            <div class="detailBlockIcon detailBlockYellow">
              <i class="fa fa-users"></i>
            </div>
            <div class="detailBlockContent">
              <h3 class="contentValue"><?= $sq_plane['total_adult'] + $sq_plane['total_child'] + $sq_plane['total_infant'] ?></h3>
              <span class="contentLabel">TOTAL SEATS</span>
            </div>
          </div>

          <!-- <div class="detailBlock text-center">
            <div class="detailBlockIcon detailBlockRed">
              <i class="fa fa-tag"></i>
            </div>
            <div class="detailBlockContent">
              <h3 class="contentValue"><?= $quotation_cost ?></h3>
              <span class="contentLabel">PRICE</span>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </section>

    <!-- traveling Information -->
  <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p4/pageBGF.jpg" class="img-responsive pageBGImg">
      <section class="travelingDetails main_block mg_tp_30 pageSectionInner">

      <?php 
        $count = 1;
        $sq_plane = mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'");
        while($row_plane = mysqli_fetch_assoc($sq_plane)){
        $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'")); 
        $itinerarySide= ($count%2!=0)?"transportDetailsleft":"transportDetailsright";
        ?> 

        <!-- Flight -->
        <section class="transportDetailsPanel main_block side_pad mg_tp_30 mg_bt_30">
          <div class="travsportInfoBlock">
            <div class="transportIcon">
              <img src="<?= BASE_URL ?>images/quotation/p4/TI_flight.png" class="img-responsive">
            </div>
            <div class="transportDetails">
              <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
                <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                    <tr class="table-heading-row">
                      <th>From_sector</th>
                      <th>To_sector</th>
                      <th>Airline</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?= $row_plane['from_location'] ?></td>
                      <td><?= $row_plane['to_location'] ?></td>
                      <td><?= ($sq_airline['airline_name'] != '') ? $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')' : 'NA' ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <div class="table-responsive" style="margin-top:30px;margin-right: 1px;">
                <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                    <tr class="table-heading-row">
                      <th>Class</th>
                      <th>Departure_D/T</th>
                      <th>Arrival_D/T</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?= ($row_plane['class'] != '') ? $row_plane['class'] : 'NA' ?></td>
                      <td><?= get_datetime_user($row_plane['dapart_time']) ?></td>
                      <td><?= get_datetime_user($row_plane['arraval_time']) ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </section>
        <?php $count++; } ?>

      </section>
    </section>

    <!-- traveling Information -->
    <?php if(isset($sq_terms_cond['terms_and_conditions']) || isset($quot_note)){?>
  <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p4/pageBG.jpg" class="img-responsive pageBGImg">

        <!-- Terms and Conditions -->
      <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">
        <?php if(isset($sq_terms_cond['terms_and_conditions'])){ ?>
          <div class="row">
            <div class="col-md-12 mg_tp_30">
              <div class="incluExcluTermsTabPanel main_block">
                  <h3 class="incexTitle">TERMS AND CONDITIONS</h3>
                  <div class="tncContent">
                      <pre class="real_text"><?= $sq_terms_cond['terms_and_conditions'] ?></pre>      
                  </div>
              </div>
            </div>

          </div>
          <?php
        }
        if(isset($quot_note)){?>
          <div class="row mg_tp_10">
            <div class="col-md-12">
              <?php echo $quot_note; ?>
            </div>
          </div>
        <?php } ?>
      </section>
  </section>
    <?php } ?>

      <!-- Costing & Banking Page -->
      <section class="pageSection main_block">
          <!-- background Image -->
          <img src="<?= BASE_URL ?>images/quotation/p4/pageBGF.jpg" class="img-responsive pageBGImg">
          <section class="endPageSection main_block mg_tp_30 pageSectionInner">
            <div class="row">
            </div>              
            <div class="row constingBankingPanelRow">
              <!-- Costing -->
              <div class="col-md-12 constingBankingPanel constingPanel">
                    <h3 class="costBankTitle text-center">COSTING DETAILS</h3>
                    <?php
                    $fare_cost = currency_conversion($currency,$currency,(floatval($newBasic)));
                    ?>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/subtotal.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= $fare_cost ?></h4>
                      <p>TOTAL FARE</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/tax.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= $tax_show ?></h4>
                      <p>TAX</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/quotationCost.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= $quotation_cost ?></h4>
                      <p>QUOTATION COST</p>
                    </div>
              </div>
              
            

              <!-- Bank Detail -->
              <div class="col-md-12 constingBankingPanel BankingPanel">
                    <h3 class="costBankTitle text-center">Bank Details</h3>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/bankName.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting  ?></h4>
                      <p>BANK NAME</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"> </div>
                      <h4 class="no-marg"><?=  ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></h4>
                      <p>BRANCH</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accName.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name  ?></h4>
                      <p>A/C TYPE</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
                      <p>A/C NO</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
                      <p>BANK ACCOUNT NAME</p>
                    </div>
                    <div class="col-md-4 text-center mg_bt_30">
                      <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                      <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
                      <p>SWIFT CODE</p>
                    </div>
                    <?php 
              if(check_qr()) { ?>
                    <div class="col-md-12 text-center" style="margin-top:20px; margin-bottom:20px;">
                        <?= get_qr('Protrait Creative') ?>
                        <br>
                        <h4 class="no-marg">Scan & Pay </h4>
          </div>
          <?php } ?>
              </div>
              
            
            </div>

          </section>
        </section>

      <!-- Costing & Banking Page -->
      <section class="pageSection main_block">
        <!-- background Image -->
        <img src="<?= BASE_URL ?>images/quotation/p4/pageBG.jpg" class="img-responsive pageBGImg">
        <section class="contactSection main_block mg_tp_30 text-center pageSectionInner">
            <div class="companyLogo">
              <img src="<?= $admin_logo_url ?>">
            </div>
        <div class="companyContactDetail">
            <h3><?= $app_name ?></h3>
            <?php //if($app_address != ''){ ?>
            <div class="contactBlock">
              <i class="fa fa-map-marker"></i>
              <p><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['address1'].','.$branch_details['address2'].','.$branch_details['city'] : $app_address; ?></p>
            </div>
            <?php //} ?>
            <?php //if($app_contact_no != ''){?>
            <div class="contactBlock">
              <i class="fa fa-phone"></i>
              <p><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['contact_no']  : $app_contact_no; ?></p>
            </div>
            <?php //} ?>
            <?php //if($app_email_id != ''){?>
            <div class="contactBlock">
              <i class="fa fa-envelope"></i>
              <p><?php echo ($branch_status=='yes' && $role!='Admin' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></p>
            </div>
            <?php //} ?>
            <?php if($app_website != ''){?>
            <div class="contactBlock">
              <i class="fa fa-globe"></i>
              <p><?php echo $app_website; ?></p>
            </div>
            <?php } ?>
            <div class="contactBlock">
              <i class="fa fa-pencil-square-o"></i>
              <p>PREPARED BY : <?= $emp_name?></p>
            </div>
        </div>
        </section>
      </section>

  </body>

</html>