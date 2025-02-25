<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img,$app_quot_format;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='b2b_sale/index.php'"));
$branch_status = $sq['branch_status'];
$branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));

$package_id = $_GET['package_id'];
$sq_pckg = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$package_id'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id='$sq_pckg[dest_id]'"));
?>
<!-- landingPage -->
<section class="landingSec main_block">
  <div class="landingPageTop main_block">
    <img src="<?= getFormatImg($app_quot_format,$sq_pckg['dest_id']) ?>" class="img-responsive">
    <h1 class="landingpageTitle"><?= $sq_pckg['package_name'] ?> <em><?= '(' . $sq_pckg['package_code'] . ')' ?></em></h1>
  </div>

  <div class="ladingPageBottom main_block side_pad">
    <div class="row">
      <div class="col-md-4">
        <div class="landigPageCustomer mg_tp_30">
          <h3 class="customerFrom">Destination</h3>
          <span class="customerName mg_tp_10"><i class="fa fa-map-marker"></i> : <?= $sq_dest['dest_name'] ?></span>
        </div>
      </div>
      <div class="col-md-8 text-right">

        <div class="detailBlock text-center">
          <div class="detailBlockIcon detailBlockGreen">
            <i class="fa fa-sun-o"></i>
          </div>
          <div class="detailBlockContent">
            <h3 class="contentValue"><?= $sq_pckg['total_days'] ?></h3>
            <span class="contentLabel">TOTAL DAYS</span>
          </div>
        </div>
        <div class="detailBlock text-center">
          <div class="detailBlockIcon detailBlockYellow">
            <i class="fa fa-moon-o"></i>
          </div>
          <div class="detailBlockContent">
            <h3 class="contentValue"><?= $sq_pckg['total_nights'] ?></h3>
            <span class="contentLabel">TOTAL NIGHT</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Itinerary -->
<section class="itinerarySec main_block side_pad mg_tp_30">

  <ul class="print_itinenary main_block no-pad no-marg">
    <?php
    $count = 1;
    $sq_package_program = mysqlQuery("select * from custom_package_program where package_id = '$package_id'");
    while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {
      if ($count % 2 != 0) {
    ?>

        <li class="singleItinenrary leftItinerary col-md-12 no-pad">
          <div class="itneraryContent col-md-11 no-pad text-right mg_tp_20 mg_bt_20">
            <div class="itneraryImg col-md-4">
              <img src="http://itourscloud.com/quotation_format_images/dummy-image.jpg" class="img-responsive" style="border:6px solid #eaeaea;">
              <div class="dayCount mg_tp_20" style="position: static; top: unset; left: unset; margin: 0; text-align: left; padding-left: 15px; margin-top:5px;">
                <span>Day-<?= $count ?></span>
                <span style="margin-right:5px;"><i class="fa fa-bed"></i> : </span><?= $row_itinarary['stay'] ?>
                <span style="text-align: right; margin-right:5px;"><i class="fa fa-cutlery"></i> : </span><?= $row_itinarary['meal_plan'] ?>
              </div>

            </div>
            <div class="itneraryText col-md-8">
              <h5 class="specialAttraction no-marg" style="text-align: left;"><?= $row_itinarary['attraction'] ?></h5>
              <p style="text-align: left;"><?= $row_itinarary['day_wise_program'] ?></p>
            </div>
          </div>
        </li>

      <?php } else { ?>

        <li class="singleItinenrary leftItinerary col-md-12 no-pad">
          <div class="itneraryContent col-md-11 no-pad text-right mg_tp_20 mg_bt_20">
            <div class="itneraryImg col-md-4">
              <img src="http://itourscloud.com/quotation_format_images/dummy-image.jpg" class="img-responsive" style="border:6px solid #eaeaea;">
              <div class="dayCount mg_tp_20" style="position: static; top: unset; left: unset; margin: 0; text-align: left; padding-left: 15px; margin-top:5px;">
                <span>Day-<?= $count ?></span>
                <span style="margin-right:5px;"><i class="fa fa-bed"></i> : </span><?= $row_itinarary['stay'] ?>
                <span style="text-align: right; margin-right:5px;"><i class="fa fa-cutlery"></i> : </span><?= $row_itinarary['meal_plan'] ?>
              </div>

            </div>
            <div class="itneraryText col-md-8">
              <h5 class="specialAttraction no-marg" style="text-align: left;"><?= $row_itinarary['attraction'] ?></h5>
              <p style="text-align: left;"><?= $row_itinarary['day_wise_program'] ?></p>
            </div>
          </div>
        </li>

    <?php }
      $count++;
    } ?>
  </ul>

</section>

<?php
$sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id='$package_id'"));
if ($sq_hotelc != 0) { ?>
  <!-- traveling Information -->
  <section class="travelingDetails main_block mg_tp_30">
    <!-- Hotel -->
    <section class="transportDetails main_block side_pad mg_tp_30">
      <div class="row mg_bt_20">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
          <div class="transportImg">
            <img src="<?= BASE_URL ?>images/quotation/hotel.png" class="img-responsive">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="table-responsive mg_tp_30">
            <table class="table table-bordered no-marg" id="tbl_emp_list">
              <thead>
                <tr class="table-heading-row">
                  <th>City</th>
                  <th>Hotel Name</th>
                  <th>Total Nights</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sq_hotel = mysqlQuery("select * from custom_package_hotels where package_id='$package_id'");
                while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
                  $hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
                  $city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel[city_name]'"));
                ?>

                  <tr>
                    <td><?php echo $city_name['city_name']; ?></td>
                    <td><?php echo $hotel_name['hotel_name'] . $similar_text; ?></td>
                    <td></span><?php echo $row_hotel['total_days']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </section>
<?php } ?>

<!-- traveling Information -->
<?php
$sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_transport where package_id='$package_id'"));
if ($sq_hotelc != 0) { ?>
  <section class="travelingDetails main_block mg_tp_30">
    <!-- Transport -->
    <section class="transportDetails main_block side_pad mg_tp_30">
      <div class="row mg_bt_20">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
          <div class="transportImg">
            <img src="<?= BASE_URL ?>images/quotation/car.png" class="img-responsive">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="table-responsive mg_tp_30">
            <table class="table table-bordered no-marg" id="tbl_emp_list">
              <thead>
                <tr class="table-heading-row">
                  <th>VEHICLE</th>
                  <th>Pickup</th>
                  <th>Drop</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $count = 0;
                $sq_hotel = mysqlQuery("select * from custom_package_transport where package_id='$package_id'");
                while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
                  $transport_name = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id ='$row_hotel[vehicle_name]'"));
                  // Pickup
                  if ($row_hotel['pickup_type'] == 'city') {
                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[pickup]'"));
                    $pickup = $row['city_name'];
                  } else if ($row_hotel['pickup_type'] == 'hotel') {
                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[pickup]'"));
                    $pickup = $row['hotel_name'];
                  } else {
                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[pickup]'"));
                    $airport_nam = clean($row['airport_name']);
                    $airport_code = clean($row['airport_code']);
                    $pickup = $airport_nam . " (" . $airport_code . ")";
                    $html = '<optgroup value="airport" label="Airport Name"><option value="' . $row['airport_id'] . '">' . $pickup . '</option></optgroup>';
                  }
                  // Drop
                  if ($row_hotel['drop_type'] == 'city') {
                    $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[drop]'"));
                    $drop = $row['city_name'];
                  } else if ($row_hotel['drop_type'] == 'hotel') {
                    $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[drop]'"));
                    $drop = $row['hotel_name'];
                  } else {
                    $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[drop]'"));
                    $airport_nam = clean($row['airport_name']);
                    $airport_code = clean($row['airport_code']);
                    $drop = $airport_nam . " (" . $airport_code . ")";
                    $html = '<optgroup value="airport" label="Airport Name"><option value="' . $row['airport_id'] . '">' . $pickup . '</option></optgroup>';
                  }
                ?>
                  <tr>
                    <td><?= $transport_name['vehicle_name'] . $similar_text ?></td>
                    <td><?= $pickup ?></td>
                    <td><?= $drop ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </section>
<?php } ?>
<?php if ($sq_pckg['inclusions'] != ' ' || $sq_pckg['exclusions'] != ' ' || $sq_pckg['note'] != '') { ?>
  <section class="incluExcluTerms main_block">
    <!-- Inclusion Exclusion -->
    <div class="incluExclu main_block">
      <div class="imgPanel">
        <img src="<?= BASE_URL ?>images/quotation/inexBg.jpg" class="img-responsive">
        <div class="imgPanelOvelay"></div>
      </div>
      <div class="contenPanel main_block side_pad mg_tp_30">
        <div class="row">
          <?php if ($sq_pckg['inclusions'] != ' ') { ?>
            <div class="col-md-6">
              <h3 class="lgTitle">Inclusion</h3>
              <pre class="real_text"><?= $sq_pckg['inclusions'] ?></pre>
            </div>
          <?php } ?>
          <?php if ($sq_pckg['exclusions'] != ' ') { ?>
            <div class="col-md-6">
              <h3 class="lgTitle">Exclusion</h3>
              <pre class="real_text"><?= $sq_pckg['exclusions'] ?></pre>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php if ($sq_pckg['note'] != '') { ?>
      <!-- <section class="incluExcluTerms main_block"> -->
      <!-- Inclusion Exclusion -->
      <div class="incluExclu main_block">

        <div class="contenPanel main_block side_pad mg_tp_30">
          <div class="row">
            <div class="col-md-12">
              <h3 class="lgTitle">Note</h3>
              <pre class="real_text"><?= $sq_pckg['note'] ?></pre>
            </div>
          </div>
        </div>
      </div>
      <!-- </section> -->
    <?php } ?>
  </section>
<?php } ?>

<!-- contact-detail -->
<section class="contactsec main_block mg_tp_30">
  <div class="row">
    <div class="col-md-7">
      <div class="contactTitlePanel text-center">
        <!-- <h3>Contact Us</h3> -->
        <img src="<?= BASE_URL ?>images/quotation/contactImg.jpg" class="img-responsive">
        <?php if ($app_website != '') { ?><p class="no-marg"><?php echo $app_website; ?></p><?php } ?>
      </div>
    </div>
    <div class="col-md-5">
      <?php //if($app_address != ''){
      ?>
      <div class="contactBlock main_block side_pad mg_tp_20">
        <div class="cBlockIcon"> <i class="fa fa-map-marker"></i> </div>
        <div class="cBlockContent">
          <h5 class="cTitle">Corporate Office</h5>
          <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address; ?></p>
        </div>
      </div>
      <?php //} 
      ?>
      <?php //if($app_contact_no != ''){
      ?>
      <div class="contactBlock main_block side_pad mg_tp_20">
        <div class="cBlockIcon"> <i class="fa fa-phone"></i> </div>
        <div class="cBlockContent">
          <h5 class="cTitle">Contact</h5>
          <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['contact_no']  : $app_contact_no; ?></p>
        </div>
      </div>
      <?php //} 
      ?>
      <?php //if($app_email_id != ''){
      ?>
      <div class="contactBlock main_block side_pad mg_tp_20">
        <div class="cBlockIcon"> <i class="fa fa-envelope"></i> </div>
        <div class="cBlockContent">
          <h5 class="cTitle">Email Id</h5>
          <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></p>
        </div>
      </div>
      <?php //} 
      ?>

    </div>
  </div>
</section>


</body>

</html>